<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight max-w-xl">
                {{ __('tasks.to-do list') }}
            </h2>
            <div class="max-w-xl">
                <a class="bg-blue-600 text-white font-semibold py-2 px-4 rounded hover:bg-blue-700 transition duration-200" href="{{ route('tasks.create')}}">
                    {{ __('tasks.Add new task') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">


        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <form method="GET" action="{{ route('tasks.index') }}" class="">

                    <div class="relative overflow-x-auto w-full">
                        <table class="w-full text-sm text-left rtl:text-right">

                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-right">
                                        {{ __('Id') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        {{ __('tasks.Task title') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center">
                                        {{ __('tasks.Priority') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center">
                                        {{ __('Status') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right">
                                        {{ __('tasks.Deadline for completion') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                    <td class="px-6 py-4"></td>
                                    <td class="px-6 py-4">{{ __('tasks.Filtration panel') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <select id="status" name="priority" class="form-control">
                                            <option value="">{{ __('Select') }}...</option>
                                            @foreach (\App\Models\Task::priorities() as $key => $label)
                                                <option value="{{ $key }}" {{ request('priority') === $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <select id="status" name="status" class="form-control">
                                            <option value="">{{ __('Select') }}...</option>
                                            @foreach (\App\Models\Task::statuses() as $key => $label)
                                                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <input name="due_date" type="date" value="{{ request('due_date') }}"/>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button type="submit" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded hover:bg-blue-700 transition duration-200" value="true">
                                            {{ __('tasks.Filter results') }}
                                        </button>
                                        <a href="{{ route('tasks.index') }}" class="mdi mdi-close text-sm text-gray-600 underline d-block mt-2">
                                            {{ __('tasks.Clear all filters') }}
                                        </a>
                                    </td>

                                </tr>
                                @foreach($tasks as $task)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                        <th class="px-6 py-4 text-right">
                                            {{ $task->id }}.
                                        </th>
                                        <th scope="row" class="px-6 py-4 font-medium whitespace-nowrap">
                                            <a href="{{ route('tasks.edit', $task->id) }}" class="text-blue-500 hover:underline mr-2">{{ $task->title }}</a>
                                        </th>
                                        <td class="px-6 py-4 text-center">
                                            {{ \App\Models\Task::priorities()[$task->priority] }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            {{ \App\Models\Task::statuses()[$task->status] }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            {{ $task->due_date }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="">
                                                <a href="{{ route('tasks.edit', $task->id) }}" class="mdi mdi-text-box-edit-outline text-blue-500 hover:underline mr-2">{{ __('Edit') }}</a>
                                                <a href="{{ route('tasks.show', $task->id) }}" class="mdi mdi-eye-outline hover:underline mr-2">{{ __('Preview') }}</a> 
                                                <span class="mdi mdi-trash-can-outline text-red-500 hover:underline">
                                                    <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit">{{ __('Delete') }}</button>
                                                    </form>
                                                </span>
                                            </div>
                                            <div class="mt-2">
                                                <button class="mdi mdi-trash-can-outline text-red-500 hover:underline delete-task" data-id="{{ $task->id }}">{{ __('Delete') }} [Ajax]</button>
                                            </div>

                                        </td>
                                    </tr>
                                @endforeach
                                
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $tasks->links() }}
                        </div>

                    </div>

                </form>
            </div>
        </div>

    </div>

    <script>
        document.querySelectorAll('.delete-task').forEach(btn => {
            btn.addEventListener('click', function () {
                if (!confirm('Na pewno usunąć zadanie?')) return;

                const taskId = this.dataset.id;

                axios.delete(`/tasks/${taskId}`)
                .then(response => {
                    if (response.status === 200) {
                        location.reload();
                    }
                });
            });
        });
    </script>
</x-app-layout>