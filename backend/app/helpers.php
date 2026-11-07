<?php

if (! function_exists('rp')) {
    // rp(18000) => "Rp 18.000"  (sama dengan fungsi rp() pada promos.js versi React)
    function rp($number): string
    {
        return 'Rp '.number_format((float) $number, 0, ',', '.');
    }
}
