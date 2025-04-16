<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Button extends Component
{
    public $variant;
    public $size;
    public $fullWidth;
    public $loading;
    public $icon;
    public $iconPosition;
    public $type;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $variant = 'primary',
        $size = 'md',
        $fullWidth = false,
        $loading = false,
        $icon = null,
        $iconPosition = 'right',
        $type = 'button'
    ) {
        $this->variant = $variant;
        $this->size = $size;
        $this->fullWidth = $fullWidth;
        $this->loading = $loading;
        $this->icon = $icon;
        $this->iconPosition = $iconPosition;
        $this->type = $type;
    }

    public function variantClasses()
    {
        return [
            'primary' => 'bg-[#00998F] text-white hover:bg-[#00998F]/90',
            'secondary' => 'bg-[#D2EAE8] text-[#00998F] hover:bg-[#D2EAE8]/80',
            'outline' => 'bg-transparent text-[#00998F] border border-[#00998F] hover:bg-[#D2EAE8]/30',
            'ghost' => 'bg-transparent text-[#00998F] hover:bg-[#D2EAE8]/30',
            'danger' => 'bg-red-500 text-white hover:bg-red-600',
        ][$this->variant];
    }

    public function sizeClasses()
    {
        return [
            'sm' => 'text-sm py-1 px-3',
            'md' => 'text-base py-2 px-4',
            'lg' => 'text-lg py-2 px-6',
        ][$this->size];
    }

    public function render()
    {
        return view('components.button');
    }
}