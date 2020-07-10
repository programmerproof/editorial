<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Journalist;
use App\Magazine;
use App\Writes;

class writesController extends Controller
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
            $writes = Writes::wherehas('journalist', function($query) use($search) {
                            $query->cardNumLike($search);
                         })->with('magazine')->paginate();
        }else{ 
            $writes = Writes::with('journalist')->with('magazine')->paginate();
        }   
        return view('writes.index',compact('writes'))
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
        $journalists = Journalist::orderBySurnameAsc();
        return view('writes.create', compact('magazines', 'journalists'));
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
        $journalist = Journalist::find($request->journalist_id);
        $writes = new Writes();
        $writes->user()->associate($user);
        $writes->magazine()->associate($magazine);
        $writes->journalist()->associate($journalist);
        $this->validate($request,
                           [
                            'magazine_reg_num'=>'required|numeric', 
                            'journalist_id'=>'required|numeric', 
                           ]
                       );
        $writes->created_at = date('Y-m-d H:i:s', time());
        $writes->save();
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
            return redirect()->route('writes.index')->with('success', 'Registro creado satisfactoriamente.')
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
        $show = Writes::validate(
            (object) [
                'id' => $id,
                'success' => function() use($id){
                    $writes = Writes::where('id', $id)->with('journalist')->with('magazine')->get();
                    return view('writes.show', compact('writes'));
                },
                'err' => function(){
                    return redirect()->route('writes.index')->with('errorAccess', 'Ese Escritor no existe.');
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
        $edit = Writes::validate(
            (object) [
                'id' => $id,
                'success' => function() use($id){
                    $user_id = \Auth::id();
                    $writes = Writes::where('id', $id)->with('magazine')->get();
                    $magazines = Magazine::orderByTitleAsc();
                    $journalists = Journalist::orderBySurnameAsc();
                    
                    if($writes[0]->user_id != $user_id){
                        return redirect()->route('writes.index')->with('errorAccess', 'No tiene acceso a ese Escritor.');
                    }else{
                        return view('writes.edit', compact('magazines', 'journalists', 'writes'));
                    }
                },
                'err' => function(){
                    return redirect()->route('writes.index')->with('errorAccess', 'Ese Escritor no existe.');
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
        $journalist = Journalist::find($request->journalist_id);
        $writes = new Writes();
        $writes->user()->associate($user);
        $writes->magazine()->associate($magazine);
        $writes->journalist()->associate($journalist);
        $this->validate($request,
                           [
                            'magazine_reg_num'=>'required|numeric', 
                            'journalist_id'=>'required|numeric', 
                           ]
                       );
        $writes->created_at = date('Y-m-d H:i:s', time());
        $writes->save();
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
        return redirect()->route('writes.index')->with('success','Registro actualizado satisfactoriamente.')
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
        $destroy = Writes::validate(
            (object) [
                'id' => $id,
                'success' => function() use($id){
                    $user_id = \Auth::id();
                    $writes = Writes::find($id);

                    if($writes->user_id != $user_id){
                        return redirect()->route('writes.index')->with('errorAccess', 'No tiene acceso a ese Escritor.');
                    }else{
                        $writes->delete();
                        return redirect()->route('writes.index')->with('success','Registro eliminado satisfactoriamente.')
                             ->with('action','destroy');
                    }
                },
                'err' => function(){
                    return redirect()->route('writes.index')->with('errorAccess', 'Ese Escritor no existe.');
                }  
            ]
        );
        return $destroy;
    }
}
