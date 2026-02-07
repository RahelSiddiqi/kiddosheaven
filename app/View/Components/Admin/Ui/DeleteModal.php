<?php

namespace App\View\Components\Admin\Ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DeleteModal extends Component
{
    public $modalId;
    public $title;
    public $message;
    public $deleteRoute;
    public $itemId;
    public $itemName;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $modalId = 'deleteModal',
        $title = 'Delete Item',
        $message = 'Are you sure you want to delete this item? This action cannot be undone.',
        $deleteRoute = '#',
        $itemId = null,
        $itemName = null
    ) {
        $this->modalId = $modalId;
        $this->title = $title;
        $this->message = $message;
        $this->deleteRoute = $deleteRoute;
        $this->itemId = $itemId;
        $this->itemName = $itemName;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.ui.delete-modal');
    }
}
