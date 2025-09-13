<?php

function getUser()
{
    return Auth::user();
}

function calculateBookingPrice($price, $bookingPercentage)
{
    return ceil(($price * $bookingPercentage) / 100);
}
