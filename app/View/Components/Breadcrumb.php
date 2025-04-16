<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Breadcrumb extends Component
{
    public $items;
    public $className;
    public $separator;

    /**
     * Create a new component instance.
     *
     * @param array $items Array of breadcrumb items
     * @param string $className Additional CSS classes
     * @param string|null $separator Custom separator (default is chevron)
     */
    public function __construct(
        array $items = [],
        string $className = '',
        string $separator = null
    ) {
        $this->items = $items;
        $this->className = $className;
        $this->separator = $separator ?? '<svg class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>';
    }

    public function render()
    {
        return view('components.breadcrumb');
    }
}