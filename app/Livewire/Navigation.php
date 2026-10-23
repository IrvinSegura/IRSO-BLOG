<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class Navigation extends Component
{
    /**
     * Render the top navigation bar used by the Jetstream pages.
     */
    public function render(): View
    {
        return view('livewire.navigation');
    }
}
