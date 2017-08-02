<?php

namespace App\Http\Controllers;

use Auth;
use Storage;
use App\File;
use App\Task;
use App\Http\Requests\FileRequest;
use Illuminate\Http\Request;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Task $task)
    {
      $data = [
      'task' => $task
      ];

      return view('file.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Task $task)
    {
      $data = [
      'task' => $task
      ];

      return view('file.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FileRequest $request, Task $task)
    {
      $file = File::create([
        'name'          =>  $request->name,
        'task_id'       =>  $task->id,
        ]);

      $extension = strtolower($request->file('file')->getClientOriginalExtension());

      Storage::put('public/files/tasks/'.$file->id.'.'.$extension, file_get_contents($request->file('file')->getRealPath()));
      $file->update(['url' => 'files/tasks/'.$file->id.'.'.$extension, 'extension' => $extension]);

      return redirect()->route('file.index', $task->id)->with('status','Se pudo subir el archivo satisfactoriamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\File  $file
     * @return \Illuminate\Http\Response
     */
    public function show(File $file)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\File  $file
     * @return \Illuminate\Http\Response
     */
    public function edit(File $file)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\File  $file
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, File $file)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\File  $file
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
      if(Auth::id() == $file->task->user_id){
        $task = $file->task;
        $exists = Storage::exists('public/'.$file->url);

        if($exists){
          Storage::delete('public/'.$file->url);
        }

        $file->delete();    
      }else{
        return redirect()->back()->withErrors(['La archivo solo puede ser eliminado por el autor de la mismo']);
      }

      return redirect()->route('file.index', $task->id)->with('status','Se pudo eliminar el archivo satisfactoriamente');
    }
  }
