<x-guest-full-width-layout>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                <div class="w-full">

                    <div class="mt-3">
                        <div class="text-right">
                            <span class="pe-3">{{ __('tasks.Owner') }}: {{$task->user->name}}</span>
                            <span class="bg-gray-100 text-gray-600 font-semibold py-2 px-4 rounded"><span class="mdi mdi-earth"></span> {{ __('tasks.Task shared') }} </span>
                        </div>

                        @include('tasks.partials.task-show-data')

                    </div>

                </div>
            </div>
        </div>
    </div>

</x-guest-full-width-layout>
