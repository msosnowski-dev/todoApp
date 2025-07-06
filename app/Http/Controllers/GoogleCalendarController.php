<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use Spatie\GoogleCalendar\Event;

use App\Models\Task;
use App\Services\GoogleCalendarService;


class GoogleCalendarController extends Controller
{
    /**
     * Dodaje zadanie do Google Calendar jako wydarzenie.
     */
    public function sendTaskToGoogleCalendar(Task $task)
    {
        // Autoryzacja: tylko właściciel zadania może wysłać je do kalendarza
        if(request()->user()->cannot('view', $task)) {
            abort(403);
        }

        $task->load('currentVersion');

        // Sprawdzenie konfiguracji Google Calendar
        if(!$this->isGoogleCalendarConfigured()) {
            return back()->with('error', __('tasks.Google Calendar credentials file is missing.'));
        }

        // Utworzenie wydarzenia na podstawie aktualnej wersji zadania
        try {
            
            $event = new Event;
            $event->name = config('app.name').': '.$task->currentVersion->title;
            $event->description = $task->currentVersion->description;
            $event->startDateTime = Carbon::parse($task->currentVersion->due_date)->startOfDay();
            $event->endDateTime = Carbon::parse($task->currentVersion->due_date)->endOfDay();

            // Zapis do Google Calendar
            $googleEvent = $event->save();
        

            // Zapisz ID wydarzenia w bazie
            $task->google_event_id = $googleEvent->id;
            $task->save();

            return redirect()->back()->with('success', __('tasks.The task has been attached to the Google Calendar'));

        } catch (\Google\Service\Exception $e) {
            if (str_contains($e->getMessage(), 'API has not been used')) {
                return back()->with('error', __('tasks.Google Calendar API has not been activated in Google Cloud Console.').' '.__('tasks.Activate it to be able to create events.'));
            }

            return back()->with('error', __('tasks.An error occurred during integration with Google Calendar.'));
        }
    }

    /**
     * Usuwa powiązane wydarzenie z Google Calendar.
     */
    public function deleteGoogleCalendarEvent(Task $task, GoogleCalendarService $google_calendar_service)
    {

        if (!$this->isGoogleCalendarConfigured()) {
            return back()->with('error', __('tasks.Google Calendar credentials file is missing.'));
        }

        // Usuń wydarzenie z kalendarza
        $google_calendar_service->deleteEvent($task->google_event_id);

        // Usuń powiązanie w bazie
        $task->google_event_id = null;
        $task->save();

        return redirect()->back()->with('success', __('tasks.The task has been removed from Google Calendar'));
    }

    /**
     * Sprawdza, czy Google Calendar jest poprawnie skonfigurowany.
     */
    private function isGoogleCalendarConfigured(): bool
    {
        return file_exists(config('google-calendar.auth_profiles.service_account.credentials_json')) &&
            !empty(config('google-calendar.calendar_id'));
    }

}
