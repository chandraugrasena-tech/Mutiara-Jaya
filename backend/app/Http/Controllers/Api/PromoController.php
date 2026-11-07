<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PromoResource;
use App\Models\Promo;

class PromoController extends Controller
{
    // GET /api/promos
    public function index()
    {
        return PromoResource::collection(Promo::where('is_active', true)->orderBy('promo_id')->get());
    }
}
