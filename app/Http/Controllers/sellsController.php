<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Branch;
use App\Magazine;
use App\Sells;

class sellsController extends Controller
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
            $sells = Sells::wherehas('branch', function($query) use($search) {
                            $query->nameLike($search);
                         })->with('magazine')->paginate();
        }else{ 
            $sells = Sells::with('branch')->with('magazine')->paginate();
        }   
        return view('sells.index',compact('sells'))
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
        $branches = Branch::orderByNameAsc();
        return view('sells.create', compact('magazines', 'branches'));
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
        $branch = Branch::find($request->branch_code);
        $sells = new sells();
        $sells->user()->associate($user);
        $sells->magazine()->associate($magazine);
        $sells->branch()->associate($branch);
        $this->validate($request,
                           [
                            'branch_code'=>'required|numeric',
                            'magazine_reg_num'=>'required|numeric',  
                           ]
                       );
        $sells->created_at = date('Y-m-d H:i:s', time());
        $sells->save();
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
            return redirect()->route('sells.index')->with('success', 'Registro creado satisfactoriamente.')
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
        $show = Sells::validate(
            (object) [
                'id' => $id,
                'success' => function() use($id){
                    $sells = Sells::where('id', $id)->with('branch')->with('magazine')->get();
                    return view('sells.show', compact('sells'));
                },
                'err' => function(){
                    return redirect()->route('sells.index')->with('errorAccess', 'Esa Venta no existe.');
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
        $edit = Sells::validate(
            (object) [
                'id' => $id,
                'success' => function() use($id){
                    $user_id = \Auth::id();
                    $sells = Sells::where('id', $id)->with('magazine')->get();
                    $magazines = Magazine::orderByTitleAsc();
                    $branches = Branch::orderByNameAsc();
                    
                    if($sells[0]->user_id != $user_id){
                        return redirect()->route('sells.index')->with('errorAccess', 'No tiene acceso a esa Venta.');
                    }else{
                        return view('sells.edit', compact('magazines', 'branches', 'sells'));
                    }
                },
                'err' => function(){
                    return redirect()->route('sells.index')->with('errorAccess', 'Esa Venta no existe.');
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
        $branch = Branch::find($request->branch_code);
        $sells = Sells::find($id);
        $sells->user()->associate($user);
        $sells->magazine()->associate($magazine);
        $sells->branch()->associate($branch);
        $this->validate($request,
                           [
                            'branch_code'=>'required|numeric',
                            'magazine_reg_num'=>'required|numeric',  
                           ]
                       );
        $sells->created_at = date('Y-m-d H:i:s', time());
        $sells->save();
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
        return redirect()->route('sells.index')->with('success','Registro actualizado satisfactoriamente.')
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
        $destroy = Sells::validate(
            (object) [
                'id' => $id,
                'success' => function() use($id){
                    $user_id = \Auth::id();
                    $sells = Sells::find($id);

                    if($sells->user_id != $user_id){
                        return redirect()->route('sells.index')->with('errorAccess', 'No tiene acceso a esa Venta.');
                    }else{
                        $sells->delete();
                        return redirect()->route('sells.index')->with('success','Registro eliminado satisfactoriamente.')
                             ->with('action','destroy');
                    }
                },
                'err' => function(){
                    return redirect()->route('sells.index')->with('errorAccess', 'Esa Venta no existe.');
                }  
            ]
        );
        return $destroy;
    }
}
