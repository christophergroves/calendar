<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;


    /**
     * Get the attendance for this session.
     */
    public function session_attendances()
    {
        return $this->hasMany(SessionAttendance::class);
    }


    /**
     * Belongs to activity.
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }


    protected $fillable = [
        'activity_id',
        'recurrance_type',
    ];
}
