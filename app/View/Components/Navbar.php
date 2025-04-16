<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Navbar extends Component
{
    public $userName;
    public $userEmail;
    public $userInitial;

    public function __construct($userName = 'Admin', $userEmail = 'admin@example.com', $userInitial = 'A')
    {
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->userInitial = $userInitial;
    }

    public function render()
    {
        return view('components.navbar');
    }
}