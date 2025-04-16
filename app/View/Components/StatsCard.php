<?php

namespace App\View\Components;

use Illuminate\View\Component;

class StatsCard extends Component
{
    public $title;
    public $value;
    public $icon;
    public $change;
    public $changeType; // 'increase' or 'decrease'
    public $action;
    public $actionHref;
    public $actionLabel;

    public function __construct(
        $title,
        $value,
        $icon = null,
        $change = null,
        $changeType = null,
        $action = null,
        $actionHref = null,
        $actionLabel = 'عرض التفاصيل'
    ) {
        $this->title = $title;
        $this->value = $value;
        $this->icon = $icon;
        $this->change = $change;
        $this->changeType = $changeType;
        $this->action = $action;
        $this->actionHref = $actionHref;
        $this->actionLabel = $actionLabel;
    }

    public function render()
    {
        return view('components.stats-card');
    }
}