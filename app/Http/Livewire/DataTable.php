<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class DataTable extends Component
{
    use WithPagination;

    public $model;
    public $columns = [];
    public $searchable = [];
    public $search = '';
    public $sortField;
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $with = [];
    public $actions = [];
    public $confirmingAction = null;
    public $actionToConfirm = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => ''],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
    ];

    public function mount($model, $columns, $searchable = [], $with = [], $actions = [])
    {
        $this->model = 'App\\Models\\' . $model;
        $this->columns = $columns;
        $this->searchable = $searchable ?: array_keys($columns);
        $this->with = $with;
        $this->actions = $actions;
        $this->sortField = $this->sortField ?: array_key_first($columns);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function confirmAction($action, $itemId)
    {
        $this->confirmingAction = $itemId;
        $this->actionToConfirm = $action;
    }

    public function performAction($itemId)
    {
        $action = $this->actionToConfirm;

        if ($action['type'] === 'delete') {
            $model = app($this->model);
            $item = $model::find($itemId);
            $item->delete();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Record deleted successfully'
            ]);
        } else {
            return redirect()->route($action['route'], $itemId);
        }

        $this->reset(['confirmingAction', 'actionToConfirm']);
    }

    public function render()
    {
        $model = app($this->model);

        if (!$model instanceof Model) {
            throw new \InvalidArgumentException("The provided model must be an Eloquent model instance.");
        }

        $query = $model::query()->with($this->with);

        // Apply search
        if ($this->search) {
            $query->where(function($q) {
                foreach ($this->searchable as $column) {
                    $q->orWhere($column, 'like', "%{$this->search}%");
                }
            });
        }

        // Apply sorting
        if ($this->sortField) {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $items = $query->paginate($this->perPage);

        return view('livewire.data-table', [
            'items' => $items,
            'columns' => $this->columns,
        ]);
    }
}