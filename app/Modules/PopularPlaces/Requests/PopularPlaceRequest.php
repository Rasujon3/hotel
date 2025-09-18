<?php

namespace App\Modules\PopularPlaces\Requests;

use App\Modules\Areas\Models\Area;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\PopularPlaces\Models\PopularPlace;
use Illuminate\Foundation\Http\FormRequest;

class PopularPlaceRequest extends FormRequest
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
        */

        if ($routeName === 'popularPlaces.check-balance') {
            return PopularPlace::checkBalanceRules();
        }

        if ($routeName === 'popularPlaces.revenue-tracker') {
            return PopularPlace::checkBalanceRules();
        }

        $id = $this->route('popularPlace') ?: null;
        return PopularPlace::rules($id);
    }
}
