<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Textarea extends Component
{
    public $label;
    public $error;
    public $name;
    public $value;
    public $placeholder;
    public $required;
    public $disabled;
    public $rows;

    public function __construct(
        $label = null,
        $error = null,
        $name = null,
        $value = null,
        $placeholder = null,
        $required = false,
        $disabled = false,
        $rows = 4
    ) {
        $this->label = $label;
        $this->error = $error;
        $this->name = $name;
        $this->value = $value;
        $this->placeholder = $placeholder;
        $this->required = $required;
        $this->disabled = $disabled;
        $this->rows = $rows;
    }

    public function render()
    {
        return view('components.textarea');
    }
}