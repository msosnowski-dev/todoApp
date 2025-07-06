<?php

namespace App\Services;

use Spatie\GoogleCalendar\Event;

class GoogleCalendarService
{

    /**
     * Usuń wydarzenie z Google Calendar na podstawie jego ID.
     */
    public function deleteEvent(string $eventId)
    {
        $event = Event::find($eventId);


        if($event->status === 'confirmed') {
            $event->delete();
        } else {
            return false;
        }
            


        return true;
    }

}
