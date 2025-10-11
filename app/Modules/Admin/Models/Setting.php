<?php

namespace App\Modules\Admin\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'fpass_limit_per_day',
    ];

    public static function rules($id = null)
    {
        $uniqueCodeRule = Rule::unique('packages', 'name');

        if ($id) {
            $uniqueCodeRule->ignore($id);
        }
        return [
            'name' => ['required', 'string', 'max:45', $uniqueCodeRule],
            'duration' => 'required|string|max:191|in:weekly,monthly,yearly',
            'price' => 'required|numeric|min:1',
            'status' => 'required|in:Active,Inactive',

        ];
    }
}
