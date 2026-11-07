<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

// Dibuat dengan: php artisan make:component AdminLayout
// Layout halaman admin (sidebar) - dipakai sebagai <x-admin-layout>
class AdminLayout extends Component
{
    public function __construct(public string $title = 'Admin Mutiara Jaya')
    {
    }

    public function render(): View
    {
        return view('layouts.admin-layout');
    }
}
