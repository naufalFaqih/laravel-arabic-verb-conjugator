<?php

namespace App\Livewire\Verb;

use App\Services\SearchHistoryService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Compact "Recent Search History" panel shown on the home page for
 * authenticated users. Refreshes whenever a sibling component
 * dispatches `verb-saved`.
 */
class RecentHistory extends Component
{
    /**
     * @return Collection<int, \App\Models\SearchHistory>
     */
    #[Computed(persist: false)]
    public function items(): Collection
    {
        if (! Auth::check()) {
            return new Collection();
        }

        return app(SearchHistoryService::class)->listForUser((int) Auth::id(), 5);
    }

    #[On('verb-saved')]
    public function refresh(): void
    {
        unset($this->items);
    }

    public function render()
    {
        return view('livewire.verb.recent-history');
    }
}
