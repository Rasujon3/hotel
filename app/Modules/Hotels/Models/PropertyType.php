<?php

namespace App\Modules\Hotels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PropertyType extends Model
{
    use HasFactory;

    protected $table = 'property_types';
    protected $fillable = [
        'name',
        'image_url',
        'image_path',
        'status',
    ];

    protected $hidden = [
        'image_path',
        'created_at',
        'updated_at',
    ];

    public static function rules($id = null)
    {
        $rules = [
            'name' => ['required', 'string', 'max:45', 'unique:property_types,name,' . $id],
            'status' => 'required|in:Active,Inactive',
        ];
        if (is_null($id)) {
            // Rule for create (if $id is null)
            $rules['image'] = 'required|image|mimes:jpg,jpeg,png|max:5120';
        } else {
            // Rule for update (if $id is not null)
            $rules['image'] = 'nullable|image|mimes:jpg,jpeg,png|max:5120';
        }
        return $rules;
    }
    public function hotels(): hasMany
    {
        return $this->hasMany(Hotel::class, 'property_type_id');
    }
}
