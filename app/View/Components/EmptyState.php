<?php

namespace App\View\Components;

use Illuminate\View\Component;

class EmptyState extends Component
{
    public $title;
    public $description;
    public $icon;
    public $illustration;
    public $illustrationSize;
    public $actions;
    public $variant;
    public $defaultIcon;

    public function __construct(
        $title,
        $description = null,
        $icon = null,
        $illustration = null,
        $illustrationSize = 'md',
        $actions = null,
        $variant = 'default',
        $defaultIcon = 'heroicon-o-inbox'
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->icon = $icon;
        $this->illustration = $illustration;
        $this->illustrationSize = $illustrationSize;
        $this->actions = $actions;
        $this->variant = $variant;
        $this->defaultIcon = $defaultIcon;
    }

    public function illustrationSizeClasses()
    {
        return [
            'sm' => 'w-32 h-32',
            'md' => 'w-48 h-48',
            'lg' => 'w-64 h-64'
        ];
    }

    public function variantClasses()
    {
        return [
            'default' => 'p-8',
            'compact' => 'p-4',
            'card' => 'p-8 border border-gray-200 rounded bg-white'
        ];
    }

    public function render()
    {
        return view('components.empty-state');
    }
}