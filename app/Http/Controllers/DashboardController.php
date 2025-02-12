<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(){
        return view('dashboard.dashboard',[
            'quizzes' => Quiz::withCount('questions')->get()
        ]);
    }
    public function statistics(){
        return view('dashboard.statistics');
    }
    public function quizzes(){
        return view('dashboard.quizzes');
    }
}
