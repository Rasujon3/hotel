<?php

function getUser()
{
    return Auth::user();
}

function calculateBookingPrice($price, $bookingPercentage)
{
    return ceil(($price * $bookingPercentage) / 100);
}

function getBedIcon(string $bedType, string $numBeds): ?string
{
    $key = strtolower($bedType) . '_' . $numBeds; // e.g., "single_2"
    $icons = config('services.bed_icons');
    return $icons[$key] ?? null;
}
