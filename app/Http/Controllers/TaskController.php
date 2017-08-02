<?php

namespace App\Http\Controllers;

use PDF;
use Auth;
use Excel;
use Storage;
use App\User;
use App\Task;
use App\Http\Requests\TaskRequest;
use Illuminate\Http\Request;

class TaskController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $tasks = Task::getFilteredForUser($request->all());
    $tasks = $tasks->paginate(3);
    
    $data = [
    'tasks' => $tasks
    ];

    return view('task.index', $data);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('task.create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(TaskRequest $request)
  {
      // Create The Task...
    $task = Task::create([
      'name'          =>  $request->name,
      'user_id'       =>  Auth::id(),
      'description'   =>  $request->description,
      'url'           =>  $request->url,
      'date'          =>  $request->date,
      ]);

    if($request->file('image')){
      Storage::put('public/images/tasks/'.$task->id.'.'.$request->file('image')->getClientOriginalExtension(), file_get_contents($request->file('image')->getRealPath()));
      $task->update(['image' => 'images/tasks/'.$task->id.'.'.$request->file('image')->getClientOriginalExtension()]);
    }

    return redirect()->route('task.index')->with('status','Se pudo crear la tarea satisfactoriamente');
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Task  $task
   * @return \Illuminate\Http\Response
   */
  public function show(Task $task)
  {
    $data = [
    'task' => $task
    ];

    return view('task.show', $data);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Task  $task
   * @return \Illuminate\Http\Response
   */
  public function edit(Task $task)
  {
    $data = [
    'task' => $task
    ];

    return view('task.edit', $data);
  }


  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Task  $task
   * @return \Illuminate\Http\Response
   */
  public function update(TaskRequest $request, Task $task)
  {
    $task->update([
      'name'          =>  $request->name,
      'description'   =>  $request->description,
      'url'           =>  $request->url,
      'date'          =>  $request->date,
      ]);

    if($request->file('image')){

      $exists = Storage::exists('public/'.$task->image);

      if($task->image && $exists){
        Storage::delete('public/'.$task->image);
      }
      
      Storage::put('public/images/tasks/'.$task->id.'.'.$request->file('image')->getClientOriginalExtension(), file_get_contents($request->file('image')->getRealPath()));
      $task->update(['image' => 'images/tasks/'.$task->id.'.'.$request->file('image')->getClientOriginalExtension()]);
    }

    return redirect()->route('task.show',$task->id)->with('status','Se pudo editar la tarea satisfactoriamente');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Task  $task
   * @return \Illuminate\Http\Response
   */
  public function destroy(Task $task)
  {
    if(Auth::id() == $task->user_id){
      foreach ($task->files as $file) {
        $exists = Storage::exists('public/'.$file->url);

        if($exists){
          Storage::delete('public/'.$file->url);
        }
        
        $file->delete();  
      }

      $exists = Storage::exists('public/'.$task->image);

      if($exists && $task->image){
        Storage::delete('public/'.$task->image);
      }
      
      $task->delete();    
    }else{
      return redirect()->back()->withErrors(['La tarea solo puede ser eliminada por el autor de la misma']);
    }

    return redirect()->route('task.index')->with('status','Se pudo eliminar la tarea satisfactoriamente');
  }

  public function exportpdf(Task $task)
  {
    $data = [
    'task'  =>  $task,
    ];

    $pdf = PDF::loadView('task.export', $data);


    return @$pdf->download('task.pdf');
  }

  public function exportpublicindexpdf()
  {
    $tasks = Task::all();

    $data = [
    'tasks'  =>  $tasks,
    ];

    $pdf = PDF::loadView('task.exportindex', $data);


    return @$pdf->download('tasks.pdf');
  }

  public function exportindexpdf(User $user)
  {
    $tasks = $user->tasks;

    $data = [
    'tasks'  =>  $tasks,
    ];

    $pdf = PDF::loadView('task.exportindex', $data);


    return @$pdf->download('tasks.pdf');
  }

  public function exportindexexcel(User $user)
  {
    $tasks = $user->tasks;

    $data = [];

    array_push($data, ['Nombre', 'Descripcion', 'Fecha', 'Url de video']);

    foreach ($tasks as $task) {
      array_push($data, [$task->name, $task->description, $task->date, $task->url]);
    }

    Excel::create('tasks', function($excel) use ($data) {

      $excel->sheet('Hoja 1', function($sheet) use ($data){
        $sheet->fromArray($data);
      });

    })->download('xls');

  }

  public function exportpublicindexexcel()
  {
    $tasks = Task::all();

    $data = [];

    array_push($data, ['Nombre', 'Descripcion', 'Autor', 'Fecha', 'Url de video']);

    foreach ($tasks as $task) {
      array_push($data, [$task->name, $task->description, $task->user->name, $task->date, $task->url]);
    }

    Excel::create('tasks', function($excel) use ($data) {

      $excel->sheet('Hoja 1', function($sheet) use ($data){
        $sheet->fromArray($data);
      });

    })->download('xls');

  }
}
