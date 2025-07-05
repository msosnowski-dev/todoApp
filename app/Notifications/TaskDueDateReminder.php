<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\Task;


class TaskDueDateReminder extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Task $task)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject(__('tasks.Reminder').': '.__('tasks.task').' "' . $this->task->currentVersion->title . '" '.__('tasks.the deadline is approaching!'))
                    ->line(__('tasks.Reminder').': '.__('tasks.Task to do tomorrow'))
                    ->action(__('tasks.You have a task').' "' . $this->task->currentVersion->title . '" '.__('tasks.to be done tomorrow').'.', config('app.url').'/tasks/' . $this->task->id)
                    ->line(__('tasks.View task'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
