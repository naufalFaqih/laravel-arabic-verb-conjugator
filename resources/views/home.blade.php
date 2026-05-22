<x-layout>
    <x-slot:title>{{ $title ?? 'ArabicMorph - Arabic Conjugation Tool' }}</x-slot:title>

    <livewire:verb.search :query="request('q') ?? request('query')" />
</x-layout>
