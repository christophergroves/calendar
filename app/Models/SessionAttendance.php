<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionAttendance extends Model
{
    use HasFactory;




    /**
     * Belongs to session.
     */
    public function session()
    {
        return $this->belongsTo(Session::class);
    }







}
