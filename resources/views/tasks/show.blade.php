<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight max-w-xl">
                {{ __('tasks.Task details') }}
            </h2>
            <div class="max-w-xl">
                <a class="text-black py-2 px-4 rounded transition duration-200" href="{{ route('tasks.index')}}">
                    <span class="mdi mdi-arrow-left-thin"></span> {{ __('tasks.Back to To Do List') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="w-full">

                    <div class="mt-3">
                        <h2 class="text-xl font-bold mb-2">{{ $task->title }}</h2>
                        <p class="mb-1"><strong>{{ __('Status') }}:</strong> {{ \App\Models\Task::statuses()[$task->status] }}</p>
                        <p class="mb-1"><strong>{{ __('tasks.Priority') }}:</strong> {{ \App\Models\Task::priorities()[$task->priority] }}</p>
                        <p class="mb-1"><strong>{{ __('tasks.Deadline for completion') }}:</strong> {{ $task->due_date }}</p>
                        <p class="mb-4"><strong>{{ __('tasks.Task description') }}:</strong> {{ $task->description }}</p>
                    </div>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
