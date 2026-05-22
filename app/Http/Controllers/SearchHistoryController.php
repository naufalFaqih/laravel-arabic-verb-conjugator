<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSearchHistoryRequest;
use App\Services\SearchHistoryService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Thin HTTP layer over {@see SearchHistoryService}.
 */
class SearchHistoryController extends Controller
{
    public function __construct(private readonly SearchHistoryService $histories)
    {
    }

    public function index(): View
    {
        if (! $this->histories->isAvailable()) {
            return view('history', [
                'histories' => new Collection(),
                'title' => 'Riwayat Pencarian',
                'message' => 'Sistem riwayat pencarian belum tersedia.',
            ]);
        }

        try {
            $userId = (int) Auth::id();

            return view('history', [
                'histories' => $this->histories->listForUser($userId),
                'title' => 'Riwayat Pencarian',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error retrieving search history: ' . $e->getMessage());

            return view('history', [
                'histories' => new Collection(),
                'title' => 'Riwayat Pencarian',
                'error' => 'Terjadi kesalahan saat mengambil data riwayat pencarian.',
            ]);
        }
    }

    public function store(StoreSearchHistoryRequest $request): JsonResponse
    {
        if (! $this->histories->isAvailable()) {
            return response()->json([
                'success' => false,
                'message' => 'Sistem riwayat pencarian belum tersedia',
            ], 500);
        }

        try {
            $history = $this->histories->store((int) Auth::id(), $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Pencarian berhasil disimpan',
                'history' => $history,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error saving search history: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan riwayat pencarian: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $deleted = $this->histories->deleteForUser($id, (int) Auth::id());

            if (! $deleted) {
                return back()->with('error', 'Riwayat pencarian tidak ditemukan atau Anda tidak memiliki akses');
            }

            return back()->with('success', 'Riwayat pencarian berhasil dihapus');
        } catch (\Throwable $e) {
            Log::error('Error deleting search history: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat menghapus riwayat pencarian');
        }
    }

    public function destroyAll(): RedirectResponse
    {
        try {
            $count = $this->histories->deleteAllForUser((int) Auth::id());

            $message = $count > 0
                ? "Berhasil menghapus {$count} riwayat pencarian"
                : 'Tidak ada riwayat pencarian untuk dihapus';

            return back()->with('success', $message);
        } catch (\Throwable $e) {
            Log::error('Error deleting all search histories: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat menghapus riwayat pencarian');
        }
    }
}
