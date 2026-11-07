<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

// Dibuat dengan: php artisan make:component HomeLayout
// Layout halaman depan (navbar, footer) - dipakai sebagai <x-home-layout>
class HomeLayout extends Component
{
    public function __construct(public string $title = 'Mutiara Jaya - Pesan Antar Cepat')
    {
    }

    public function render(): View
    {
        return view('layouts.home-layout');
    }
}
