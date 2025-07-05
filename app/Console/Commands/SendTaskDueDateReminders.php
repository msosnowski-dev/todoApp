<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Task;
use App\Notifications\TaskDueDateReminder;

class SendTaskDueDateReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wysyła przypomnienia o zadaniach na 1 dzień przed terminem.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = now()->addDay()->startOfDay();
        $tasks = Task::whereHas('currentVersion', function ($q) use ($tomorrow) {
            $q->whereDate('due_date', $tomorrow);
        })->with('user')->get();

        foreach ($tasks as $task) {
            if ($task->user) {
                $task->user->notify(new TaskDueDateReminder($task));
            }
        }

        $this->info('Przypomnienia wysłane.');
    }
}
