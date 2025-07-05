<h2 class="text-xl font-bold mb-2">{{ $task->currentVersion->title }}</h2>
<p class="mb-1"><strong>{{ __('Status') }}:</strong> {{ \App\Models\Task::statuses()[$task->currentVersion->status] }}</p>
<p class="mb-1"><strong>{{ __('tasks.Priority') }}:</strong> {{ \App\Models\Task::priorities()[$task->currentVersion->priority] }}</p>
<p class="mb-1"><strong>{{ __('tasks.Deadline for completion') }}:</strong> {{ $task->currentVersion->due_date }}</p>
<p class="mb-4"><strong>{{ __('tasks.Task description') }}:</strong> {{ $task->currentVersion->description }}</p>