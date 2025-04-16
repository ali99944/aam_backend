<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Input extends Component
{
    public $label;
    public $error;
    public $icon;
    public $iconPosition;
    public $type;
    public $name;
    public $value;
    public $placeholder;
    public $required;
    public $disabled;

    public function __construct(
        $label = null,
        $error = null,
        $icon = null,
        $iconPosition = 'right',
        $type = 'text',
        $name = null,
        $value = null,
        $placeholder = null,
        $required = false,
        $disabled = false
    ) {
        $this->label = $label;
        $this->error = $error;
        $this->icon = $icon;
        $this->iconPosition = $iconPosition;
        $this->type = $type;
        $this->name = $name;
        $this->value = $value;
        $this->placeholder = $placeholder;
        $this->required = $required;
        $this->disabled = $disabled;
    }

    public function render()
    {
        return view('components.input');
    }
}