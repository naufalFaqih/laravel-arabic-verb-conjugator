<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * Smoke-test component untuk verifikasi Livewire 3 terpasang dengan benar.
 *
 * Dipasang di route /dev/livewire-check yang hanya aktif di environment local.
 * Akan dihapus di Task 9 (cleanup) setelah seluruh refactor selesai.
 */
class Hello extends Component
{
    public int $count = 0;

    public function increment(): void
    {
        $this->count++;
    }

    public function decrement(): void
    {
        $this->count--;
    }

    public function resetCount(): void
    {
        $this->count = 0;
    }

    public function render()
    {
        return view('livewire.hello');
    }
}
