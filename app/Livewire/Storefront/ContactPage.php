<?php

namespace App\Livewire\Storefront;

use Livewire\Component;

class ContactPage extends Component
{
    public $name = '';
    public $email = '';
    public $subject = '';
    public $message = '';
    public $sent = false;

    public function send()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // TODO: Send email notification
        $this->sent = true;
        $this->reset(['name', 'email', 'subject', 'message']);
    }

    public function render()
    {
        return view('livewire.storefront.contact-page');
    }
}
