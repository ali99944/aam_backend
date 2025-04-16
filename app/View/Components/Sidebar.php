<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Sidebar extends Component
{
    public $logo;
    public $logoHref;
    public $collapsed;

    public function __construct($logo = null, $logoHref = '/', $collapsed = false)
    {
        $this->logo = $logo;
        $this->logoHref = $logoHref;
        $this->collapsed = $collapsed;
    }

    public function render()
    {
        return view('components.sidebar');
    }
}