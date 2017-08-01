<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use App\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tasks = Task::getFiltered($request->all());
        $users = [0 => 'Todos'] + User::pluck('name','id')->all();
        $tasks = $tasks->paginate(3);
        
        $data = [
            'tasks' =>  $tasks,
            'users' =>  $users
        ];

        return view('home', $data);
    }
}
