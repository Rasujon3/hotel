<?php

namespace App\Modules\Homes\Requests;

use App\Modules\Areas\Models\Area;
use App\Modules\Facilities\Models\Facility;
use App\Modules\Floors\Models\Floor;
use App\Modules\Homes\Models\Home;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Packages\Models\Package;
use App\Modules\Ratings\Models\Rating;
use Illuminate\Foundation\Http\FormRequest;

class HomeRequest extends FormRequest
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

        if ($routeName === 'homes.hotel-details') {
            return Home::hotelDetailsRules();
        }

        if ($routeName === 'homes.room-details') {
            return Home::roomDetailsRules();
        }

        if ($routeName === 'homes.search-by-area') {
            return Home::searchByAreaRules();
        }

        if ($routeName === 'homes.hotels-by-popular-place') {
            return Home::hotelsByPopularPlaceRules();
        }

        if ($routeName === 'homes.hotel-by-property-type') {
            return Home::hotelByPropertyType();
        }

        /*
        if ($routeName === 'ratings.list') {
            return Rating::listRules();
        }
        */

        # $id = $this->route('rating') ?: null;

        # return Rating::rules($id);
    }
}
