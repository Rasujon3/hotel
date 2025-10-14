<?php

namespace App\Modules\Bookings\Requests;

use App\Modules\Areas\Models\Area;
use App\Modules\Bookings\Models\Booking;
use App\Modules\Packages\Models\Package;
use App\Modules\Rooms\Models\Room;
use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // You can add any authorization logic here
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Get the route name and apply null-safe operator
        $routeName = $this->route()?->getName();

        /*
        if ($routeName === 'areas.import') {
            return Area::importRules();
        }

        if ($routeName === 'areas.bulkUpdate') {
            return Area::bulkRules();
        }

        if ($routeName === 'areas.list') {
            return Area::listRules();
        }
        if ($routeName === 'areas.checkAvailability') {
            return Area::checkAvailabilityRules();
        }
        $areaId = $this->route('area') ?: null;
        */

        if ($routeName === 'bookings.list') {
            return Booking::listRules();
        }

        if ($routeName === 'rooms.update') {
            return Booking::updateRules();
        }

        if ($routeName === 'bookings.update-status') {
            return Booking::updateStatusRules();
        }

        if ($routeName === 'bookings.update-check-in-status') {
            return Booking::updateCheckInStatusRules();
        }

        if ($routeName === 'bookings.update-check-out-status') {
            return Booking::updateCheckInStatusRules();
        }

        if ($routeName === 'bookings.search-booking-by-user') {
            return Booking::searchBookingByUserRules();
        }

        if ($routeName === 'bookings.user-bookings') {
            return Booking::userBookingsRules();
        }

        $id = $this->route('booking') ?: null;
        return Booking::rules($id);
    }
}
