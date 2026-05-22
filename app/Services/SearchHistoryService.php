<?php

namespace App\Services;

use App\Models\SearchHistory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Encapsulates CRUD operations for user search history.
 *
 * Centralises the schema-existence check that was previously duplicated
 * across controller actions, so callers can rely on simple method calls.
 */
class SearchHistoryService
{
    /**
     * Whether the underlying search_histories table exists.
     */
    public function isAvailable(): bool
    {
        return Schema::hasTable('search_histories');
    }

    /**
     * Return latest history entries for the given user.
     *
     * @return Collection<int, SearchHistory>
     */
    public function listForUser(int $userId, int $limit = 20): Collection
    {
        if (! $this->isAvailable()) {
            return new Collection();
        }

        return SearchHistory::where('user_id', $userId)
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Persist a new history entry for the given user.
     *
     * @param  array{query: string, result?: mixed}  $data
     */
    public function store(int $userId, array $data): SearchHistory
    {
        $history = new SearchHistory();
        $history->user_id = $userId;
        $history->query = $data['query'];
        $history->result = $data['result'] ?? null;
        $history->save();

        return $history;
    }

    /**
     * Delete a single history entry that belongs to the given user.
     */
    public function deleteForUser(int $id, int $userId): bool
    {
        return SearchHistory::where('id', $id)
            ->where('user_id', $userId)
            ->delete() > 0;
    }

    /**
     * Delete all history entries belonging to the given user.
     *
     * @return int Number of rows removed.
     */
    public function deleteAllForUser(int $userId): int
    {
        return SearchHistory::where('user_id', $userId)->delete();
    }
}
