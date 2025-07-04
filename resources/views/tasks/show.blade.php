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
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}</a>
                    </div>
                @endif
                @if(session('link'))
                    <div class="alert alert-success">
                        {{ __('tasks.Public link has been generated') }}: <a href="{{ session('link') }}" >{{ session('link') }}</a>
                    </div>
                @endif

                <div class="w-full">

                    <div class="mt-3">
                        <div class="text-right">
                            @if(!$token)
                                <div class="flex justify-content-end">
                                    @if($task->google_event_id)
                                        <form action="{{ route('task.delete-google-calendar-event', $task->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="bg-red-600 text-white font-semibold py-2 px-4 rounded hover:bg-red-700 transition duration-200">
                                                <span class="mdi mdi-calendar-month-outline"></span> {{ __('tasks.Delete task from calendar') }}
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('task.send-task-google-calendar', $task->id) }}" method="POST">
                                            @csrf
                                            <button class="bg-green-600 text-white font-semibold py-2 px-4 rounded hover:bg-green-700 transition duration-200">
                                                <span class="mdi mdi-calendar-month-outline"></span> {{ __('tasks.Create a calendar task') }}
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('task.generate-url', $task->id) }}" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded hover:bg-blue-700 transition duration-200 ms-3">
                                        <span class="mdi mdi-share-variant-outline"></span> {{ __('tasks.Share') }}
                                    </a>
                                </div>
                            @else
                                <span class="pe-3">{{ __('tasks.Owner') }}: {{$task->user->name}}</span>
                                <span class="bg-gray-100 text-gray-600 font-semibold py-2 px-4 rounded"><span class="mdi mdi-earth"></span> {{ __('tasks.Task shared') }} </span>
                                
                            @endif
                        </div>

                        <h2 class="text-xl font-bold mb-2">{{ $task->currentVersion->title }}</h2>
                        <p class="mb-1"><strong>{{ __('Status') }}:</strong> {{ \App\Models\Task::statuses()[$task->currentVersion->status] }}</p>
                        <p class="mb-1"><strong>{{ __('tasks.Priority') }}:</strong> {{ \App\Models\Task::priorities()[$task->currentVersion->priority] }}</p>
                        <p class="mb-1"><strong>{{ __('tasks.Deadline for completion') }}:</strong> {{ $task->currentVersion->due_date }}</p>
                        <p class="mb-4"><strong>{{ __('tasks.Task description') }}:</strong> {{ $task->currentVersion->description }}</p>
                    </div>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
