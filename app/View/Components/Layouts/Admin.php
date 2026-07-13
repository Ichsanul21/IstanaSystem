<?php

namespace App\View\Components\Layouts;

use Illuminate\View\Component;

class Admin extends Component
{
    public function __construct(public ?string $title = null) {}

    public function render(): \Illuminate\View\View
    {
        return view('layouts.admin');
    }
}
