<x-app-layout>

    <style>
    table tr td, table tr th {
        padding: 0 30px 0 0;
    }
    table tr th {
        padding-bottom: 10px;
    }
    </style>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Immensive Licence Management System Console') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div>
                <!-- Generate AWS EC2  -->
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1 flex justify-between">
                        <div class="px-4 sm:px-0">
                            <table class="dark:text-gray-100" style="margin-top:5px;">
                                <tr>
                                    <th>Resource</th>
                                    <th>API Endpoint</th>
                                    <th>Method</th>
                                </tr>
                                <tr>
                                    <td title="Get All Active License Details">All Licenses</td>
                                    <td>https://<span title="{{ parse_url(url(''), PHP_URL_HOST) }}" style="color: #6875f5;">HOST</span>/api/licenses</td>
                                    <td>GET</td>
                                </tr>
                                <tr>
                                    <td title="One License & it's Client, Product, Release, Patch">Single license id</td>
                                    <td>https://<span title="{{ parse_url(url(''), PHP_URL_HOST) }}" style="color: #6875f5;">HOST</span>/api/licenses/id</td>
                                    <td>GET</td>
                                </tr>
                                <tr>
                                    <td title="Update Version & Device ID">Single license id</td>
                                    <td>https://<span title="{{ parse_url(url(''), PHP_URL_HOST) }}" style="color: #6875f5;">HOST</span>/api/licenses/id</td>
                                    <td>PUT</td>
                                </tr>
                                <tr>
                                    <td title="One License, Product, All Releases, Patch">Single license id</td>
                                    <td>https://<span title="{{ parse_url(url(''), PHP_URL_HOST) }}" style="color: #6875f5;">HOST</span>/api/versions/id</td>
                                    <td>GET</td>
                                </tr>
                                <tr>
                                    <td title="One Client All Labs & Students">Single client id</td>
                                    <td>https://<span title="{{ parse_url(url(''), PHP_URL_HOST) }}" style="color: #6875f5;">HOST</span>/api/institute/id</td>
                                    <td>GET</td>
                                </tr>
                                <tr>
                                    <td title="Send Account ID, IP Address, Room ID">Add System</td>
                                    <td>https://<span title="{{ parse_url(url(''), PHP_URL_HOST) }}" style="color: #6875f5;">HOST</span>/api/student/on</td>
                                    <td>POST</td>
                                </tr>
                                <tr>
                                    <td title="Update Student Name">Single student id</td>
                                    <td>https://<span title="{{ parse_url(url(''), PHP_URL_HOST) }}" style="color: #6875f5;">HOST</span>/api/student/id</td>
                                    <td>PUT</td>
                                </tr>
                                <tr>
                                    <td title="One student & it's Client, and All Active Licenses">Single student id</td>
                                    <td>https://<span title="{{ parse_url(url(''), PHP_URL_HOST) }}" style="color: #6875f5;">HOST</span>/api/student/id</td>
                                    <td>GET</td>
                                </tr>
                                <tr>
                                    <td title="Remove Active System">Single student id</td>
                                    <td>https://<span title="{{ parse_url(url(''), PHP_URL_HOST) }}" style="color: #6875f5;">HOST</span>/api/student/id</td>
                                    <td>DELETE</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Assign ILMS Credentials</h3>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        To integrate the token with the Immensive License Management System (ILMS), assign the credentials appropriately. Once the token is assigned, you can leverage the ILMS APIs for various operations within the Immensive License Management System.
                        </p>
                        </br>
                        <form action="/ilms-console" method="POST">
                        @csrf
                            <div class="px-4 py-5 bg-white dark:bg-gray-800 sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                                <div class="grid grid-cols-6 gap-6">
                                    <!-- Select Token -->
                                    <div class="col-span-6 sm:col-span-4">
                                        <x-label for="token_id" value="{{ __('Select Token') }}" />
                                        <select id="token_id" name="token_id" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 w-full" autofocus>
                                            <option value="">None</option>
                                            @foreach($accessTokens as $accessToken)
                                                <option value="{{ $accessToken->id }}" style="@if(\App\Models\ILMSConsole::where('token_id', $accessToken->id)->exists())display:none;@endif">{{ $accessToken->name }}</option>
                                            @endforeach
                                        </select>

                                        <x-input-error for="token_id" class="mt-2" />

                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-end px-4 pb-3 bg-gray-50 dark:bg-gray-800 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                                <x-button>{{ __('Create') }}</x-button>
                            </div>
                        </form>
                    </div>
                </div>

                <x-section-border />

                <!-- Manage API Tokens -->
                <div class="mt-10 sm:mt-0">
                    <div class="md:grid md:grid-cols-3 md:gap-6">
                        <div class="md:col-span-1 flex justify-between">
                            <div class="px-4 sm:px-0">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Manage ILMS Credentials') }}</h3>

                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('You can view the token name, check the last usage status, and perform actions such as deleting or unassigning tokens. This enables efficient oversight and control of the credentials within the Immensive License Management System.') }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 md:mt-0 md:col-span-2">
                            <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                                <div class="space-y-6">
                                    

                                @foreach($ILMSConsoles as $ILMSConsole)
                                    <div class="flex items-center justify-between">
                                        <div class="break-all dark:text-white">
                                            @php
                                                $accessToken = \DB::table('personal_access_tokens')
                                                    ->where('id', $ILMSConsole->token_id)
                                                    ->value('name');
                                                    echo $accessToken;
                                            @endphp
                                        </div>

                                        <div class="flex items-center ml-2">
                                            <div class="text-sm text-gray-400">
                                                @if ($ILMSConsole->updated_at)
                                                    <?php $carbonDate = \Carbon\Carbon::parse($ILMSConsole->updated_at); ?>
                                                    {{ __('Created') }} {{ $carbonDate->diffForHumans() }}
                                                @else
                                                    {{ __('Created') }} {{ __('Never') }}
                                                @endif
                                            </div>
                                            <a href="{{ $ILMSConsole->publicDnsName }}" target="_blank" class="cursor-pointer ml-6 text-sm text-gray-400 underline" title="{{ $ILMSConsole->publicDnsName }}">
                                                @if ($ILMSConsole->status == '1')
                                                On   
                                                @else
                                                Off
                                                @endif
                                            </a>
                                            <a href="/ilms-console/{{ $ILMSConsole->id }}" class="cursor-pointer ml-6 text-sm text-red-500">Delete</a>
                                        </div>
                                    </div>
                                @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- __ENDBLOCK__ -->

                <!-- Token Value Modal -->
                <div x-data="{ show: window.Livewire.find('LaBlAy1MvtRVAT7g7SN1').entangle('displayingToken').live }" x-on:close.stop="show = false" x-on:keydown.escape.window="show = false" x-show="show" id="c2b2f21f3c0157c323786c80758a1304" class="jetstream-modal fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
                    style="display: none;">
                    <div x-show="show" class="fixed inset-0 transform transition-all" x-on:click="show = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
                        <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
                    </div>

                    <div x-show="show" class="mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-2xl sm:mx-auto" x-trap.inert.noscroll="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        style="display: none;">
                        <div class="px-6 py-4">
                            <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                API Token
                            </div>

                            <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                <div>
                                    Please copy your new API token. For your security, it won't be shown again.
                                </div>

                                <input class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-4 bg-gray-100 px-4 py-2 rounded font-mono text-sm text-gray-500 w-full break-all"
                                    x-ref="plaintextToken" type="text" readonly="readonly" autofocus="autofocus" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" @showing-token-modal.window="setTimeout(() => $refs.plaintextToken.select(), 250)">
                            </div>
                        </div>

                        <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 dark:bg-gray-800 text-right">
                            <button type="button" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
                                wire:click="$set('displayingToken', false)" wire:loading.attr="disabled">
                Close
            </button>
                        </div>
                    </div>
                </div>


                <!-- Delete Token Confirmation Modal -->
                <div x-data="{ show: window.Livewire.find('LaBlAy1MvtRVAT7g7SN1').entangle('confirmingApiTokenDeletion').live }" x-on:close.stop="show = false" x-on:keydown.escape.window="show = false" x-show="show" id="c4552dc4dc29aa1ae939f3386a0c0b8b" class="jetstream-modal fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
                    style="display: none;">
                    <div x-show="show" class="fixed inset-0 transform transition-all" x-on:click="show = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
                        <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
                    </div>

                    <div x-show="show" class="mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-2xl sm:mx-auto" x-trap.inert.noscroll="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        style="display: none;">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path>
                            </svg>
                                </div>

                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        Delete API Token
                                    </h3>

                                    <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                        Are you sure you would like to delete this API token?
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 dark:bg-gray-800 text-right">
                            <button type="button" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
                                wire:click="$toggle('confirmingApiTokenDeletion')" wire:loading.attr="disabled">
                            Cancel
                        </button>

                                        <button type="button" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 ml-3"
                                            wire:click="deleteApiToken" wire:loading.attr="disabled">
                            Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
</x-app-layout>