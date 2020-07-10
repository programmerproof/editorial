<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Magazine;
use App\Magissue;

class MagissueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $user_id = \Auth::id();
        $search = $request->get('search');
        if($search != ''){
            $magissues = Magissue::wherehas('magazine', function($query) use($search) {
                            $query->titleLike($search);
                         })->paginate();
        }else{ 
            $magissues = Magissue::with('magazine')->paginate();
        }   
        return view('magissue.index',compact('magissues'))
                 ->with('user', $user_id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $magazines = Magazine::orderByTitleAsc();
        return view('magissue.create', compact('magazines'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $exception = '';
    try{
        $user_id = \Auth::id();
        $user = User::find($user_id);
        $magazine = Magazine::find($request->magazine_reg_num);
        $magissue = new Magissue();
        $magissue->user()->associate($user);
        $magissue->magazine()->associate($magazine);
        $this->validate($request,
                           [
                            'magazine_reg_num'=>'required|numeric', 
                            'date' => 'required|date_format:Y-m-d',
                            'pages_num'=>'required|numeric|min:1|max:99999',
                            'copies_num'=>'required|numeric|min:1|max:99999',
                           ]
                       );
        $magissue->date = $request->date;
        $magissue->pages_num = $request->pages_num;
        $magissue->copies_num = $request->copies_num;
        $magissue->created_at = date('Y-m-d H:i:s', time());
        $magissue->save();
        }catch(\Illuminate\Database\QueryException $e){
            $errorCode = $e->errorInfo[1];
            $err = '';
            switch ($errorCode) {
                case 1364:
                    $err = response([
                        'errors'=>$e->getMessage()
                    ],Response::HTTP_NOT_FOUND);
                    $exception = 'Error de trasabilidad consulte con el Dpto. de Sistemas.';
                break;
            }
        }
        
        if($exception != ''){
            return redirect()->back()->with('errorAccess', $exception)->withInput($request->all());
        }else {
            return redirect()->route('magissue.index')->with('success', 'Registro creado satisfactoriamente.')
                         ->with('action','create');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $show = Magissue::validate(
            (object) [
                'id' => $id,
                'success' => function() use($id){
                    $magissues = Magissue::where('id', $id)->with('magazine')->get();
                    return view('magissue.show', compact('magissues'));
                },
                'err' => function(){
                    return redirect()->route('magissue.index')->with('errorAccess', 'Ese Número no existe.');
                }  
            ]
        );
        return $show;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $edit = Magissue::validate(
            (object) [
                'id' => $id,
                'success' => function() use($id){
                    $user_id = \Auth::id();
                    $magissues = Magissue::where('id', $id)->with('magazine')->get();
                    $magazines = Magazine::orderByTitleAsc();
                    
                    if($magissues[0]->user_id != $user_id){
                        return redirect()->route('magissue.index')->with('errorAccess', 'No tiene acceso a ese Número.');
                    }else{
                        return view('magissue.edit', compact('magazines', 'magissues'));
                    }
                },
                'err' => function(){
                    return redirect()->route('magissue.index')->with('errorAccess', 'Ese Número no existe.');
                }  
            ]
        );
        return $edit;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $exception = '';
    try{
        $user_id = \Auth::id();
        $user = User::find($user_id);
        $magazine = Magazine::find($request->magazine_reg_num);
        $magissue = Magissue::find($id);
        $magissue->user()->associate($user);
        $magissue->magazine()->associate($magazine);
        $this->validate($request,
                           [
                            'magazine_reg_num'=>'required|numeric', 
                            'date' => 'required|date_format:Y-m-d',
                            'pages_num'=>'required|numeric|min:1|max:99999',
                            'copies_num'=>'required|numeric|min:1|max:99999',
                           ]
                       );
        $magissue->date = $request->date;
        $magissue->pages_num = $request->pages_num;
        $magissue->copies_num = $request->copies_num;
        $magazine->created_at = date('Y-m-d H:i:s', time());
        $magazine->save();
    }catch(\Illuminate\Database\QueryException $e){
        $errorCode = $e->errorInfo[1];
        $err = '';
        switch ($errorCode) {
            case 1364:
                $err = response([
                    'errors'=>$e->getMessage()
                ],Response::HTTP_NOT_FOUND);
                $exception = 'Error de trasabilidad consulte con el Dpto. de Sistemas.';
            break;
        }
    }
    
    if($exception != ''){
        return redirect()->back()->with('errorAccess', $exception)->withInput($request->all());
    }else {
        return redirect()->route('magissue.index')->with('success','Registro actualizado satisfactoriamente.')
                         ->with('action','edit');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $destroy = Magissue::validate(
            (object) [
                'id' => $id,
                'success' => function() use($id){
                    $user_id = \Auth::id();
                    $magissues = Magissue::find($id);

                    if($magissues->user_id != $user_id){
                        return redirect()->route('magissue.index')->with('errorAccess', 'No tiene acceso a ese Número.');
                    }else{
                        $magissues->delete();
                        return redirect()->route('magissue.index')->with('success','Registro eliminado satisfactoriamente.')
                             ->with('action','destroy');
                    }
                },
                'err' => function(){
                    return redirect()->route('magissue.index')->with('errorAccess', 'Ese Número no existe.');
                }  
            ]
        );
        return $destroy;
    }
}
