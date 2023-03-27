<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class Select2Multiple extends Component
{
    public $users = [];
    public $selectedUsers = [];

    public function render()
    {
        return view('components.select2-multiple');
    }

    public function mount(){
        $this->users = User::all();
    }
}
