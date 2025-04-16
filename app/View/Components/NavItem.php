<?php

namespace App\View\Components;

use Illuminate\View\Component;

class NavItem extends Component
{
    public $href;
    public $active;
    public $icon;
    public $label;
    public $hasChildren;

    public function __construct($href = '#', $active = false, $icon = null, $label = null, $hasChildren = false)
    {
        $this->href = $href;
        $this->active = $active;
        $this->icon = $icon;
        $this->label = $label;
        $this->hasChildren = $hasChildren;
    }

    public function render()
    {
        return view('components.nav-item');
    }
}