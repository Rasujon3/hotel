<?php

namespace App\Modules\Hotels\Requests;

use App\Modules\Areas\Models\Area;
use App\Modules\Hotels\Models\Hotel;
use Illuminate\Foundation\Http\FormRequest;

class HotelRequest extends FormRequest
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

        if ($routeName === 'hotels.update') {
            return Hotel::updateRules();
        }

        if ($routeName === 'hotels.check-balance') {
            return Hotel::checkBalanceRules();
        }

        if ($routeName === 'hotels.revenue-tracker') {
            return Hotel::checkBalanceRules();
        }
        $id = $this->route('hotel') ?: null;
        return Hotel::rules($id);
    }
}
