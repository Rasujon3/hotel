<?php

use App\Modules\Hotels\Models\Hotel;
use App\Modules\Receptionists\Models\Receptionist;

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
if (! function_exists('getUserHotelIds')) {
    function getUserHotelIds($userId, $userTypeId)
    {
        if ($userTypeId == 3) {
            return Hotel::where('user_id', $userId)->pluck('id')->toArray();
        }

        if ($userTypeId == 4) {
            return Receptionist::where('user_id', $userId)
                ->pluck('hotel_id')
                ->toArray();
        }

        // অন্য টাইপ হলে খালি array রিটার্ন করবে
        return [];
    }
}
