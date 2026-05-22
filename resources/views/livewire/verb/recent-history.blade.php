<div class="mt-8 p-6 bg-gray-50 rounded-lg shadow-md border border-gray-100">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Recent Search History</h3>
        <a href="{{ route('history') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
            See All
        </a>
    </div>

    @if(count($this->items) > 0)
        <div class="divide-y divide-gray-100">
            @foreach($this->items as $history)
                <div class="py-2 flex justify-between items-center" wire:key="recent-{{ $history->id }}">
                    <div class="flex-1">
                        <span class="text-right text-gray-800 font-medium">{!! $history->query !!}</span>
                        <div class="text-xs text-gray-500">{{ $history->created_at->diffForHumans() }}</div>
                    </div>
                    <a href="/search?query={{ urlencode($history->query) }}" class="ml-4 text-indigo-600 hover:text-indigo-900 text-sm">
                        Search Again
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 text-sm">No Search History Yet.</p>
    @endif
</div>
