<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


use App\Rules\Time;
 
// $request->validate([
//     'name' => ['required', 'string', new Uppercase],
// ]);


class StoreSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
 
            'activity_id' => 'required|numeric',
            'start_date' => 'nullable|date_format:d/m/Y',
            'finish_date' => 'nullable|date_format:d/m/Y',
            'session_date' => 'date_format:d/m/Y',
            'start_time' => new Time(),
            'finish_time' => new Time(),
            'hours' => 'nullable|integer',
            'recurrance_type' => 'integer',
            'recurrance_interval' => 'nullable|integer',
            'recurrance_number' => 'nullable|integer',
            'recurrance_day_single' => 'nullable|integer',
            'recurrance_monthly_interval' => 'nullable|alpha',
        ];



    }
}
