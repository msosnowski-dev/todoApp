<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        $tasks = Task::with('user', 'currentVersion')
            ->whereHas('currentVersion')
            ->where('user_id', auth()->id())
            ->get();

        return response()->json($tasks, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Task $task)
    {
        if ($request->user()->cannot('delete', $task)) {
            return response()->json(['message' => __('tasks.You do not have permission to delete the task.')], 403);
        }

        $task->delete();

        return response()->json(['message' => __('tasks.The task has been deleted.')]);
    }
}
