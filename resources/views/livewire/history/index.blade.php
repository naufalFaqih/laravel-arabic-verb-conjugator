<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    @if($statusMessage)
        <div class="m-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-3 rounded-md text-sm" role="alert">
            {{ $statusMessage }}
        </div>
    @endif

    @if($errorMessage)
        <div class="m-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded-md text-sm" role="alert">
            {{ $errorMessage }}
        </div>
    @endif

    <div class="px-4 py-5 sm:px-6 flex justify-between">
        <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ __('messages.search_history_title') }}
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                {{ __('messages.search_history_subtitle') }}
            </p>
        </div>

        @if(count($this->histories) > 0)
            <button type="button"
                    wire:click="deleteAll"
                    wire:confirm="{{ __('messages.confirm_delete') }}"
                    class="px-3 py-1 text-xs text-red-600 hover:text-red-800 border border-red-300 rounded hover:bg-red-50">
                {{ __('messages.clear_history') }}
            </button>
        @endif
    </div>

    <div class="border-t border-gray-200">
        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
                {{ __('messages.search_date') }}
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->format('d M Y, H:i') : '-' }}
            </dd>
        </div>
    </div>

    <div class="p-4">
        @if(! $this->isAvailable())
            <div class="text-center py-8 text-gray-500">
                <p>{{ __('messages.empty_history_message') }}</p>
            </div>
        @elseif(count($this->histories) > 0)
            <div class="divide-y divide-gray-200">
                @foreach($this->histories as $index => $history)
                    <div class="py-3 flex justify-between items-center" wire:key="history-{{ $history->id }}">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <span class="text-gray-800 font-medium">{{ $index + 1 }}.</span>
                                <span class="text-right text-gray-800 font-medium">{!! $history->query !!}</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $history->created_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="/search?query={{ urlencode($history->query) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                {{ __('messages.search') }}
                            </a>
                            <button type="button"
                                    wire:click="deleteOne({{ $history->id }})"
                                    wire:confirm="{{ __('messages.confirm_delete') }}"
                                    class="text-red-500 hover:text-red-700 text-sm">
                                {{ __('messages.delete') }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <p>{{ __('messages.empty_history_message') }}</p>
            </div>
        @endif
    </div>
</div>
