<?php

namespace App\Livewire;

use Livewire\Component;
use App\Livewire\Actions\Logout;

class LogoutButton extends Component
{
    public function logout()
    {
        // Call the Logout action class
        $logout = new Logout();
        $logout();

        // Redirect to the login page or home page
        return redirect()->route('welcome');
    }

    public function render()
    {
        return view('livewire.logout-button');
    }
}
