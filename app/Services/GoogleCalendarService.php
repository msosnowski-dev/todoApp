<?php

namespace App\Services;

use Spatie\GoogleCalendar\Event;

class GoogleCalendarService
{

    /**
     * Usuń wydarzenie z Google Calendar na podstawie jego ID.
     */
    public function deleteEvent(string $eventId): bool
    {
        $event = Event::find($eventId);

        if (!$event) {
            return false;
        }

        $event->delete();

        return true;
    }

}
