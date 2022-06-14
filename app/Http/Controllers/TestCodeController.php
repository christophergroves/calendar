<?php

namespace App\Http\Controllers;

use App\Models\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TestCodeController extends Controller
{
    public function test(Request $request)
    {
       return $request->all();

    }

}