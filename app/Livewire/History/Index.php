<?php

namespace App\Livewire\History;

use App\Services\SearchHistoryService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Search history page (logged-in users).
 *
 * Replaces the controller-driven {@see \App\Http\Controllers\SearchHistoryController::index}.
 * Storage of new entries is still handled by the controller's store() endpoint
 * (called from the front-end JavaScript translation flow).
 */
class Index extends Component
{
    public ?string $statusMessage = null;

    public ?string $errorMessage = null;

    public function mount(): void
    {
        if (! Auth::check()) {
            abort(401);
        }
    }

    /**
     * @return Collection<int, \App\Models\SearchHistory>
     */
    #[Computed(persist: false)]
    public function histories(): Collection
    {
        return app(SearchHistoryService::class)->listForUser((int) Auth::id());
    }

    public function isAvailable(): bool
    {
        return app(SearchHistoryService::class)->isAvailable();
    }

    public function deleteOne(int $id): void
    {
        $service = app(SearchHistoryService::class);

        try {
            $deleted = $service->deleteForUser($id, (int) Auth::id());

            if ($deleted) {
                $this->statusMessage = 'Riwayat pencarian berhasil dihapus';
                $this->errorMessage = null;
            } else {
                $this->errorMessage = 'Riwayat pencarian tidak ditemukan atau Anda tidak memiliki akses';
                $this->statusMessage = null;
            }
        } catch (\Throwable $e) {
            Log::error('Livewire deleteOne error: ' . $e->getMessage());
            $this->errorMessage = 'Terjadi kesalahan saat menghapus riwayat pencarian';
            $this->statusMessage = null;
        }

        unset($this->histories);
    }

    public function deleteAll(): void
    {
        $service = app(SearchHistoryService::class);

        try {
            $count = $service->deleteAllForUser((int) Auth::id());

            $this->statusMessage = $count > 0
                ? "Berhasil menghapus {$count} riwayat pencarian"
                : 'Tidak ada riwayat pencarian untuk dihapus';
            $this->errorMessage = null;
        } catch (\Throwable $e) {
            Log::error('Livewire deleteAll error: ' . $e->getMessage());
            $this->errorMessage = 'Terjadi kesalahan saat menghapus riwayat pencarian';
            $this->statusMessage = null;
        }

        unset($this->histories);
    }

    public function render()
    {
        return view('livewire.history.index');
    }
}
