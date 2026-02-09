<?php

namespace App\View\Components\Admin\Common;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ConfirmDelete extends Component
{
    public $id;
    public $title;
    public $message;
    public $onConfirm;
    public $onCancel;

    /**
     * Create a new component instance.
     */
    public function __construct($id, $title, $message, $onConfirm, $onCancel)
    {
        $this->id = $id;
        $this->title = $title;
        $this->message = $message;
        $this->onConfirm = $onConfirm;
        $this->onCancel = $onCancel;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.common.confirm-delete');
    }
}
