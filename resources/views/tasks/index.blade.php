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
                <div class="">

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
                                            <a href="{{ route('tasks.edit', $task->id) }}" class="mdi mdi-text-box-edit-outline text-blue-500 hover:underline mr-2"> {{ __('Edit') }}</a>
                                            <span class="mdi mdi-eye-outline hover:underline mr-2"> {{ __('Preview') }}</span> 
                                            <span class="mdi mdi-trash-can-outline text-red-500 hover:underline"> {{ __('Delete') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                                
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $tasks->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>
</x-app-layout>