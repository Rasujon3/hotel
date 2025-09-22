<?php

namespace App\Modules\Packages\Models;

use App\Models\User;
use App\Modules\Hotels\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Package extends Model
{
    use HasFactory;

    protected $table = 'packages';

    protected $fillable = [
        'name',
        'duration',
        'price',
        'status',
    ];

    public static function rules($id = null)
    {
        $uniqueCodeRule = Rule::unique('packages', 'name');

        if ($id) {
            $uniqueCodeRule->ignore($id);
        }
        return [
            'name' => ['required', 'string', 'max:45', 'in:3 Star Hotel,4 Star Hotel,5 Star Hotel', $uniqueCodeRule],
            'duration' => 'required|string|max:191|in:monthly',
            'price' => 'required|numeric|min:1',
            'status' => 'required|in:Active,Inactive',

        ];
    }
    public function hotels(): HasOne
    {
        return $this->hasOne(Hotel::class);
    }
}
