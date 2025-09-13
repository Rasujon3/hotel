<?php

namespace App\Modules\Rooms\Models;

use App\Models\User;
use App\Modules\Floors\Models\Floor;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Rooms\Models\Room;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class RoomImg extends Model
{
    use HasFactory;

    protected $table = 'room_imgs';

    protected $fillable = [
        'user_id',
        'hotel_id',
        'floor_id',
        'room_id',
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
    public function floor(): belongsTo
    {
        return $this->belongsTo(Floor::class);
    }
    public function room(): belongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
