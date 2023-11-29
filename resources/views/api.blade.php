<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('API Activities') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div>
                <!-- Generate AWS EC2  -->
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1 flex justify-between">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">List of the API Tokens</h3>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Below is a comprehensive list of API tokens; click on each token to view its respective activities.
                            </p>

                            <div class="dark:divide-gray-600 mt-5 flex flex-col space-y-2 p-4" style="width:95%;">
                            @foreach($accessToken as $accessToken)
                                <a href="/api-activities/{{ $accessToken->id }}" 
                                class="text-white hover:text-gray-300 px-4 py-2 bg-gray-800 rounded mt-5 flex items-center justify-between
                                @if(request()->segment(2) == $accessToken->id)
                                    dark:bg-gray-700
                                @endif">
                                    <div class="break-all dark:text-white">
                                        {{ $accessToken->name }}
                                    </div>
                                    
                                    <div class="text-sm text-gray-400">
                                        @if ($accessToken->last_used_at)
                                            <?php
                                                $carbonDate = \Carbon\Carbon::parse($accessToken->last_used_at);
                                            ?>
                                            {{ __('Last used') }} {{ $carbonDate->diffForHumans() }}
                                        @else
                                            {{ __('Last used') }} {{ __('Never') }}
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                            </div>

                        </div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="flex flex-col">
                            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="w-full py-2 align-middle inline-block">
                                    <div class="shadow overflow-x-auto border-gray-200 shadow sm:rounded-lg">
                                        <table class="w-full divide-y divide-gray-200 dark:divide-gray-600">
                                            <thead class="bg-gray-800 dark:bg-gray-700">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Level
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Time
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Description
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        IP Address
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-gray-100 dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600 text-sm text-gray-700 dark:text-gray-300">
                                            @foreach($APIActivities as $APIActivity)
                                                <tr class="border-b" style="border-color: #374151;">
                                                    <td class="px-6 py-3 whitespace-nowrap">
                                                        <b style="text-transform: capitalize;
                                                        @if ($APIActivity->response == 'error')
                                                        color: #e84042;
                                                        @else
                                                        color: #a46aff;
                                                        @endif
                                                        ">{{ $APIActivity->response }}</b>
                                                    </td>
                                                    <td class="px-6 py-3 whitespace-nowrap">
                                                        {{ $APIActivity->created_at }}
                                                    </td>
                                                    <td class="px-6 py-3 whitespace-nowrap underline" title="{{ $APIActivity->endpoint }}">
                                                        {{ $APIActivity->resource }}
                                                    </td>
                                                    <td class="px-6 py-3 whitespace-nowrap">
                                                        <a href="https://www.whatismyip.com/ip/{{ $APIActivity->ip_address }}" target="_blank" class="cursor-pointer text-sm dark:text-white">{{ $APIActivity->ip_address }}</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            {{ $APIActivities->links() }}
                        </div>
                    </div>
                </div>

                <!-- __ENDBLOCK__ -->

            </div>
        </div>
    </div>
    </div>
    </div>
</x-app-layout>