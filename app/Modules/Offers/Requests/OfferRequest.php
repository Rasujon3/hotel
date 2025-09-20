<?php

namespace App\Modules\Offers\Requests;

use App\Modules\Areas\Models\Area;
use App\Modules\Expenses\Models\Expense;
use App\Modules\Floors\Models\Floor;
use App\Modules\Offers\Models\Offer;
use App\Modules\Packages\Models\Package;
use Illuminate\Foundation\Http\FormRequest;

class OfferRequest extends FormRequest
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

        if ($routeName === 'offers.update') {
            return Offer::updateRules();
        }
        */

        if ($routeName === 'offers.list') {
            return Offer::listRules();
        }

        $id = $this->route('offers') ?: null;

        return Offer::rules($id);
    }
}
