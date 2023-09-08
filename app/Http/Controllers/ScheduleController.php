<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    

    public function add(Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'begin_time' => ['required'],
            'end_time' => ['required'],
            'memo' => ['nullable', 'string', 'max:255'],
            'template_name' => ['nullable', 'string', 'max:255']
        ]);

        //追加する
        
    }
}
