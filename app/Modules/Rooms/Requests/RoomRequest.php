<?php

namespace App\Modules\Rooms\Requests;

use App\Modules\Areas\Models\Area;
use App\Modules\Packages\Models\Package;
use App\Modules\Rooms\Models\Room;
use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
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

        if ($routeName === 'rooms.list') {
            return Room::listRules();
        }

        if ($routeName === 'rooms.update') {
            return Room::updateRules();
        }

        $id = $this->route('room') ?: null;
        return Room::rules($id);
    }
}
