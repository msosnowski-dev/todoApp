<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('tasks.Create task') }}
        </h2>
    </x-slot>

    <form method="POST" action="{{ isset($task) ? route('tasks.update', $task) : route('tasks.store') }}">
        @csrf

        @if (isset($task))
            @method('PUT')
        @endif

        <div class="py-12">

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="w-full">

                        <div class="">
                            <label for="title" class="input-label required">{{ __('tasks.Task title') }}</label>
                            <input id="title" type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $task->title ?? '') }}"/>
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mt-3 grid grid-cols-3 gap-4">
                            <div>
                                <label for="priority" class="input-label required">{{ __('tasks.Priority') }}</label>
                                <select id="priority" name="priority" class="form-control @error('priority') is-invalid @enderror">
                                    @foreach (\App\Models\Task::priorities() as $key => $label)
                                        <option value="{{ $key }}" {{ old('priority', $task->priority ?? '') === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('priority')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div>
                                <div>
                                    <label for="status" class="input-label required">{{ __('tasks.Priority') }}</label>
                                    <select id="status" name="status" class="form-control @error('status') is-invalid @enderror">
                                        @foreach (\App\Models\Task::statuses() as $key => $label)
                                            <option value="{{ $key }}" {{ old('status', $task->status ?? '') === $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="due_date" class="input-label required">{{ __('tasks.Deadline for completion') }}</label>
                                <input id="due_date" type="text" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $task->due_date ?? '') }}"/>
                                @error('due_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="mt-3">
                            <label for="title" class="input-label required">{{ __('tasks.Task description') }}</label>
                            <textarea name="description" class="@error('description') is-invalid @enderror w-full" style="height: 200px">{{ old('description', $task->description ?? '') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="text-right mt-3">
                            <button type="submit" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded hover:bg-blue-700 transition duration-200" value="true">
                                    {{ __('Save') }}
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
</form>
</x-app-layout>
