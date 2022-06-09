<?php

namespace App\Http\Controllers;

use App\Models\SessionAttendance;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSessionAttendanceRequest;
use App\Http\Requests\UpdateSessionAttendanceRequest;

class SessionAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSessionAttendanceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSessionAttendanceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SessionAttendance  $sessionAttendance
     * @return \Illuminate\Http\Response
     */
    public function show(SessionAttendance $sessionAttendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SessionAttendance  $sessionAttendance
     * @return \Illuminate\Http\Response
     */
    public function edit(SessionAttendance $sessionAttendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSessionAttendanceRequest  $request
     * @param  \App\Models\SessionAttendance  $sessionAttendance
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSessionAttendanceRequest $request, SessionAttendance $sessionAttendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SessionAttendance  $sessionAttendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(SessionAttendance $sessionAttendance)
    {
        //
    }
}
