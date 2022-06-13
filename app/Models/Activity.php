<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;



    /**
     * Belongs to user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Get the sessions for this activity.
     */
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }




}
