<?php

namespace App\Modules\Expenses\Models;

use App\Models\User;
use App\Modules\Hotels\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseImg extends Model
{
    use HasFactory;

    protected $table = 'expense_imgs';

    protected $fillable = [
        'user_id',
        'hotel_id',
        'expense_id',
        'image_url',
        'image_path',
    ];

    protected $hidden = [
        'image_path',
    ];

    public function user() : belongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function hotel() : belongsTo
    {
        return $this->belongsTo(Hotel::class,'hotel_id');
    }
}
