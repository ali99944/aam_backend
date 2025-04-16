<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Card extends Component
{
    public $header;
    public $footer;
    public $bordered;
    public $hoverable;

    public function __construct($header = null, $footer = null, $bordered = false, $hoverable = false)
    {
        $this->header = $header;
        $this->footer = $footer;
        $this->bordered = $bordered;
        $this->hoverable = $hoverable;
    }

    public function render()
    {
        return view('components.card');
    }
}