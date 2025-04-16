<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Select extends Component
{
    public $options;
    public $value;
    public $placeholder;
    public $label;
    public $error;
    public $searchable;
    public $name;
    public $required;
    public $disabled;

    public function __construct(
        $options = [],
        $value = null,
        $placeholder = 'اختر...',
        $label = null,
        $error = null,
        $searchable = false,
        $name = null,
        $required = false,
        $disabled = false
    ) {
        $this->options = $options;
        $this->value = $value;
        $this->placeholder = $placeholder;
        $this->label = $label;
        $this->error = $error;
        $this->searchable = $searchable;
        $this->name = $name;
        $this->required = $required;
        $this->disabled = $disabled;
    }

    public function render()
    {
        return view('components.select');
    }
}