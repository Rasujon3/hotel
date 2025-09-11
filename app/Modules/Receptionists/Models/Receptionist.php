<?php

namespace App\Modules\Receptionists\Models;

use App\Models\User;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Expenses\Models\ExpenseImg;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Receptionist extends Model
{
    use HasFactory;

    protected $table = 'receptionists';

    protected $fillable = [
        'user_id',
        'hotel_id',
        'created_by',
        'updated_by',
        'name',
        'email',
        'phone',
        'nid',
        'shift',
        'image_path',
        'image_url',
    ];

    public static function rules()
    {
        return [
            'hotel_id'    => 'required|integer|exists:hotels,id',
            'name'        => 'nullable|string|max:255',
            'email'       => 'nullable|email|max:255',
            'phone'       => 'nullable|string|max:20',
            'nid'         => 'nullable|string|max:50',
            'shift'       => 'nullable|in:Morning,Evening,Night',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
    public static function listRules()
    {
        return [
            'hotel_id' => 'required|string|max:191|exists:hotels,id',
        ];
    }
    public static function registerRules()
    {
        return [
            'hotel_id'    => 'required|integer|exists:hotels,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|string|min:6|same:password',
            'nid'         => 'required|string|max:50',
            'shift'       => 'required|in:Morning,Evening,Night',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }
    public static function updateRules()
    {
        return [
            'hotel_id' => 'required|integer|exists:hotels,id',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'nid' => 'nullable|string|max:50',
            'shift' => 'nullable|in:Morning,Evening,Night',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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
    public function createdBy(): belongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }
}
