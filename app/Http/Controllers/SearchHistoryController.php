<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SearchHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SearchHistoryController extends Controller
{
    /**
     * Tampilkan halaman riwayat pencarian
     */
     public function index()
    {
        try {
            // Check if the table exists first
            if (!Schema::hasTable('search_histories')) {
                return view('history', [
                    'histories' => collect([]),
                    'title' => 'Riwayat Pencarian',
                    'message' => 'Sistem riwayat pencarian belum tersedia.'
                ]);
            }
            
            // Ambil 20 riwayat pencarian terakhir
            $user = Auth::user();
            $histories = SearchHistory::where('user_id', $user->id)
                ->latest()
                ->take(20)
                ->get();
                
            return view('history', [
                'histories' => $histories,
                'title' => 'Riwayat Pencarian'
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving search history: ' . $e->getMessage());
            return view('history', [
                'histories' => collect([]), // Empty collection
                'title' => 'Riwayat Pencarian',
                'error' => 'Terjadi kesalahan saat mengambil data riwayat pencarian.'
            ]);
        }
    }

    /**
     * Simpan riwayat pencarian baru
     */
    public function store(Request $request)
    {
        try {
            // Check if table exists
            if (!Schema::hasTable('search_histories')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sistem riwayat pencarian belum tersedia'
                ], 500);
            }
            
            $validated = $request->validate([
                'query' => 'required|string|max:255',
                'result' => 'nullable',
            ]);
            
            Log::info('Menyimpan riwayat pencarian', [
                'query' => $validated['query'],
                'user_id' => Auth::id()
            ]);
            
            $history = new SearchHistory();
            $history->user_id = Auth::id();
            $history->query = $validated['query'];
            $history->result = $validated['result'];
            $history->save();

            return response()->json([
                'success' => true,
                'message' => 'Pencarian berhasil disimpan',
                'history' => $history
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving search history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan riwayat pencarian: ' . $e->getMessage()
            ], 500);
        }
    }

    

    /**
     * Hapus riwayat pencarian
     */
    public function destroy($id)
    {
        try {
            $history = SearchHistory::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();
                
            $history->delete();
            
            return back()->with('success', 'Riwayat pencarian berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Riwayat pencarian tidak ditemukan atau Anda tidak memiliki akses');
        } catch (\Exception $e) {
            Log::error('Error deleting search history: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus riwayat pencarian');
        }
    }

    /**
     * Hapus semua riwayat pencarian
     */
    public function destroyAll()
    {
        try {
            $user = Auth::user();
            $count = SearchHistory::where('user_id', $user->id)->count();
            SearchHistory::where('user_id', $user->id)->delete();
            
            return back()->with('success', $count > 0 ? 
                "Berhasil menghapus $count riwayat pencarian" : 
                'Tidak ada riwayat pencarian untuk dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting all search histories: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus riwayat pencarian');
        }
    }
}