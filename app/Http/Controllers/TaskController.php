<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

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
    public function show(Task $task)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        if (request()->user()->cannot('edit', $task)) {
            abort(403);
        }
        return view('tasks.form', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        if ($request->user()->cannot('update', $task)) {
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
}
