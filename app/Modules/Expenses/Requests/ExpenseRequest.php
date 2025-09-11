<?php

namespace App\Modules\Expenses\Requests;

use App\Modules\Areas\Models\Area;
use App\Modules\Expenses\Models\Expense;
use App\Modules\Floors\Models\Floor;
use App\Modules\Packages\Models\Package;
use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
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

        if ($routeName === 'expenses.update') {
            return Expense::updateRules();
        }

        if ($routeName === 'expenses.list') {
            return Expense::listRules();
        }

        $id = $this->route('expense') ?: null;

        return Expense::rules($id);
    }
}
