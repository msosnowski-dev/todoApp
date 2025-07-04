<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\PublicTaskToken;
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
        $query = Task::with('currentVersion')->where('user_id', auth()->id());

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

        $task = Task::create(['user_id' => auth()->id()]);

        // Tworzymy pierwszą wersję
        $version = $task->versions()->create($validated);

        // Aktualizujemy task, by wskazywał na aktualną wersję
        $task->update(['current_version_id' => $version->id]);

        return redirect()->route('tasks.index')->with('success', __('tasks.The task has been added.'));
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

        $task->load('currentVersion');

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

        $task->load('currentVersion');

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

        $version = $task->versions()->create($validated);

        $task->update(['current_version_id' => $version->id]);

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
    public function destroy(Task $task)
    {
        if(request()->user()->cannot('delete', $task)) {
            abort(403);
        }
        $task->delete();

        //usunięcie wydarznie z kalendarza Google
        if($task->google_event_id) {
            $event = new Event;

            $event = Event::find($task->google_event_id);
            $event->delete();
        }

        // Wykryj ajax
        if (request()->wantsJson()) {
            session()->flash('success', __('tasks.The task has been deleted.'));
            return response()->json(['success' => true]);
        }

        return redirect()->route('tasks.index')->with('success', __('tasks.The task has been deleted.')); 
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

    public function sendTaskToGoogleCalendar(Task $task)
    {
        if(request()->user()->cannot('view', $task)) {
            abort(403);
        }

        $task->load('currentVersion');

        //wysłanie danych zadania do kalendarza Google
        $event = new Event;

        $event->name = config('app.name').': '.$task->currentVersion->title;
        $event->description = $task->currentVersion->description;
        $event->startDateTime = Carbon::parse($task->currentVersion->due_date)->startOfDay();
        $event->endDateTime = Carbon::parse($task->currentVersion->due_date)->endOfDay();

        $googleEvent = $event->save();
       
        //zapis id wydarzenia google do zadania
        $task->google_event_id = $googleEvent->id;
        $task->save();

        return redirect()->back()->with('success', __('tasks.The task has been attached to the Google Calendar'));
    }

    public function deleteGoogleCalendarEvent(Task $task)
    {

        //usunięcie wydarznie z kalendarza Google
        $event = new Event;

        $event = Event::find($task->google_event_id);
        $event->delete();
       
        //usuniecie id wydarzenia google w zadaniu
        $task->google_event_id = NULL;
        $task->save();

        return redirect()->back()->with('success', __('tasks.The task has been removed from Google Calendar'));
    }
}