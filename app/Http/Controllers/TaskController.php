<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\PublicTaskToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::where('user_id', auth()->id());

        // Filtrowanie
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('due_date')) {
            $query->whereDate('due_date', $request->due_date);
        }

        // Paginacja
        $tasks = $query->latest()->paginate(5)->withQueryString();

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tasks.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateTask($request);
        $request->user()->tasks()->create($validated);

        return redirect()->route('tasks.index')->with('success', 'Zadanie zostało dodane.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task, Request $request)
    {
        if($request->token) {
            $token = PublicTaskToken::where('token', $request->token)
                ->where('expires_at', '>', now())
                ->first();

            if(!$token) abort(403);

            $task = $token->task;
            
        } else {
            if(request()->user()->cannot('view', $task)) {
                abort(403);
            }

            $token = null;
        }

        return view('tasks.show', compact('task', 'token'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        if(request()->user()->cannot('edit', $task)) {
            abort(403);
        }
        return view('tasks.form', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        if($request->user()->cannot('update', $task)) {
            abort(403);
        }
        $validated = $this->validateTask($request);
        $task->update($validated);
        return redirect()->route('tasks.index')->with('success', 'Zadanie zostało zaktualizowane.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        if(request()->user()->cannot('delete', $task)) {
            abort(403);
        }
        $task->delete();

        // Wykryj ajax
        if (request()->wantsJson()) {
            session()->flash('success', 'Zadanie zostało usunięte.');
            return response()->json(['success' => true]);
        }

        return redirect()->route('tasks.index')->with('success', 'Zadanie zostało usunięte.'); 
    }

    private function validateTask(Request $request): array
    {
        return $request->validate([
            'title' => 'bail|required|max:255',
            'description' => 'nullable',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:to-do,in-progress,done',
            'due_date' => 'required|date|date_format:d.m.Y',
        ]);
    }

    public function generatePublicUrl(Task $task)
    {
        if(request()->user()->cannot('view', $task)) {
            abort(403);
        }

        $token = $task->publicTokens()->create([
            'token' => Str::random(40),
            'expires_at' => Carbon::now()->addHour(),
        ]);

        return redirect()->back()->with('link', route('task.show-public', $token->token));
    }
}