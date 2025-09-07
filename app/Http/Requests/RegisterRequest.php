<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function rules()
    {
        // Get the route name and apply null-safe operator
        $routeName = $this->route()?->getName();

        /*
        if ($routeName === 'countries.import') {
            return Country::importRules();
        }

        if ($routeName === 'countries.bulkUpdate') {
            return Country::bulkRules();
        }

        if ($routeName === 'countries.list') {
            return Country::listRules();
        }

        if ($routeName === 'countries.checkAvailability') {
            return Country::checkAvailabilityRules();
        }

        $countryId = $this->route('country') ?: null;

        return Country::rules($countryId);
        */
        return User::rules();
    }
}
