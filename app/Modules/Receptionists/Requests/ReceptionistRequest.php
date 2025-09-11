<?php

namespace App\Modules\Receptionists\Requests;

use App\Modules\Areas\Models\Area;
use App\Modules\Expenses\Models\Expense;
use App\Modules\Floors\Models\Floor;
use App\Modules\Packages\Models\Package;
use App\Modules\Receptionists\Models\Receptionist;
use Illuminate\Foundation\Http\FormRequest;

class ReceptionistRequest extends FormRequest
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

        if ($routeName === 'receptionists.updateReceptionist') {
            return Receptionist::updateRules();
        }

        if ($routeName === 'receptionists.list') {
            return Receptionist::listRules();
        }

        if ($routeName === 'receptionists.register') {
            return Receptionist::registerRules();
        }

        $id = $this->route('receptionist') ?: null;

        return Receptionist::rules($id);
    }
}
