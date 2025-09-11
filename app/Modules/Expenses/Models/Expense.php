<?php

namespace App\Modules\Expenses\Models;

use App\Models\User;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Expenses\Models\ExpenseImg;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'expenses';

    protected $fillable = [
        'user_id',
        'hotel_id',
        'name',
        'quantity',
        'unit',
        'amount',
    ];

    public static function rules()
    {
        return [
            'hotel_id' => 'required|string|max:191|exists:hotels,id',
            'name'      => 'required|string|max:255',
            'quantity'  => 'required|numeric|min:1',
            'unit'      => 'required|string|max:50',
            'amount'    => 'required|numeric|min:1|max:99999999.99',
            'images' => 'nullable|array|min:1',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
    public static function listRules()
    {
        return [
            'hotel_id' => 'required|string|max:191|exists:hotels,id',
        ];
    }
    public static function updateRules()
    {
        return [
            'hotel_id' => 'required|string|max:191|exists:hotels,id',
            'name'      => 'required|string|max:255',
            'quantity'  => 'required|numeric|min:1',
            'unit'      => 'required|string|max:50',
            'amount'    => 'required|numeric|min:1|max:99999999.99',
            'images' => 'nullable|array|min:1',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
    public function user() : belongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function hotel() : belongsTo
    {
        return $this->belongsTo(Hotel::class,'hotel_id');
    }
    public function images(): HasMany
    {
        return $this->hasMany(ExpenseImg::class);
    }
}
