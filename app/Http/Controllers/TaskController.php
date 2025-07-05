<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\PublicTaskToken;
use App\Services\GoogleCalendarService;
use App\Services\TaskService;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::whereHas('currentVersion', function ($q) use ($request) {
            if($request->filled('priority')) {
                $q->where('priority', $request->priority);
            }
            if($request->filled('status')) {
                $q->where('status', $request->status);
            }
            if($request->filled('due_date')) {
                $q->whereDate('due_date', $request->due_date);
            }
        })->where('user_id', auth()->id());

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
    public function store(Request $request, TaskService $taskService)
    {

        $task = Task::create(['user_id' => auth()->id()]);

        // Tworzymy pierwszą wersję
        $taskService->createVersion($task);

        return redirect()->route('tasks.index')->with('success', __('tasks.The task has been added.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task, Request $request, TaskService $taskService)
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

        $task->load('currentVersion');

        $taskHistory = $taskService->getHistory($task);

        return view('tasks.show', compact('task', 'token', 'taskHistory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        if(request()->user()->cannot('edit', $task)) {
            abort(403);
        }

        $task->load('currentVersion');

        return view('tasks.form', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task, TaskService $taskService)
    {
        if($request->user()->cannot('update', $task)) {
            abort(403);
        }
        
        $version=$taskService->createVersion($task);
    
        //Edycja danych wydarzenia w kalendarzu Google
        if($task->google_event_id) {
            $event = Event::find($task->google_event_id);
            $event->name = config('app.name').': '.$version->title;
            $event->description = $version->description;
            $event->startDateTime = Carbon::parse($version->due_date)->startOfDay();
            $event->endDateTime = Carbon::parse($version->due_date)->endOfDay();

            $event->save();

        }

        return redirect()->route('tasks.index')->with('success', __('tasks.The task has been updated.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task, GoogleCalendarService $google_calendar_service)
    {
        if(request()->user()->cannot('delete', $task)) {
            abort(403);
        }
        $task->delete();

        //usunięcie wydarzenie z kalendarza Google
        if($task->google_event_id) {
            $google_calendar_service-> deleteEvent($task->google_event_id);
        }

        // Wykryj ajax
        if (request()->wantsJson()) {
            session()->flash('success', __('tasks.The task has been deleted.'));
            return response()->json(['success' => true]);
        }

        return redirect()->route('tasks.index')->with('success', __('tasks.The task has been deleted.')); 
    }

    /**
     * Tworzy publiczny link z tokenem do podglądu zadania (wygasa po 1h).
     */
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