<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\User;
use App\magperiodicity;
use App\Magtype;
use App\Magazine;

class MagazineController extends Controller
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
            $magazines = Magazine::titleLike($search)->paginate();
        }else{ 
            $magazines = Magazine::with('magperiodicity')->with('magtype')->paginate();
        }   
        return view('magazine.index',compact('magazines'))
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
        $magperiodicities = magperiodicity::orderByNameAsc();
        $magtypes = magtype::orderByNameAsc();
        return view('magazine.create', compact('magperiodicities', 'magtypes'));
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
        $magperiodicity = Magperiodicity::find($request->magperiodicity_id);
        $magtype = Magtype::find($request->magtype_id);
        $magazine = new Magazine();
        $magazine->user()->associate($user);
        $magazine->magperiodicity()->associate($magperiodicity);
        $magazine->magtype()->associate($magtype);
        $this->validate($request,
                           [
                            'title'=>'required|regex:/^([0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/|min:3|max:30', 
                            'magperiodicity_id'=>'required|numeric', 
                            'magtype_id'=>'required|numeric', 
                           ]
                       );
        $magazine->title = $request->title;
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
            return redirect()->route('magazine.index')->with('success', 'Registro creado satisfactoriamente.')
                         ->with('action','create');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($reg_num)
    {
        //
        $show = Magazine::validate(
            (object) [
                'reg_num' => $reg_num,
                'success' => function() use($reg_num){
                    $magazines = Magazine::where('reg_num', $reg_num)->with('magperiodicity')->with('magtype')->get();
                    return view('magazine.show', compact('magazines'));
                },
                'err' => function(){
                    return redirect()->route('magazine.index')->with('errorAccess', 'Esa Revista no existe.');
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
    public function edit($reg_num)
    {
        //
        $edit = Magazine::validate(
            (object) [
                'reg_num' => $reg_num,
                'success' => function() use($reg_num){
                    $user_id = \Auth::id();
                    $magazines = Magazine::where('reg_num', $reg_num)->with('magperiodicity')->with('magtype')->get();
                    $magperiodicities = Magperiodicity::orderByNameAsc();
                    $magtypes = Magtype::orderByNameAsc(); 
                    
                    if($magazines[0]->user_id != $user_id){
                        return redirect()->route('magazine.index')->with('errorAccess', 'No tiene acceso a esa Revista.');
                    }else{
                        return view('magazine.edit', compact('magperiodicities', 'magtypes', 'magazines'));
                    }
                },
                'err' => function(){
                    return redirect()->route('magazine.index')->with('errorAccess', 'Esa Revista no existe.');
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
    public function update(Request $request, $reg_num)
    {
        //
        $exception = '';
    try{
        $user_id = \Auth::id();
        $user = User::find($user_id);
        $magperiodicity = Magperiodicity::find($request->magperiodicity_id);
        $magtype = Magtype::find($request->magtype_id);
        $magazine = Magazine::find($reg_num);
        $magazine->user()->associate($user);
        $magazine->magperiodicity()->associate($magperiodicity);
        $magazine->magtype()->associate($magtype);
        $this->validate($request,
                           [
                            'magperiodicity_id'=>'required|numeric', 
                            'magtype_id'=>'required|numeric', 
                            'title'=>'required|regex:/^([0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/|min:3|max:30', 
                           ]
                       );
        $magazine->title;
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
        return redirect()->route('magazine.index')->with('success','Registro actualizado satisfactoriamente.')
                         ->with('action','edit');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($reg_num)
    {
        //
        $destroy = Magazine::validate(
            (object) [
                'reg_num' => $reg_num,
                'success' => function() use($reg_num){
                    $user_id = \Auth::id();
                    $magazines = Magazine::find($reg_num);

                    if($magazines->user_id != $user_id){
                        return redirect()->route('magazine.index')->with('errorAccess', 'No tiene acceso a esa Revista.');
                    }else{
                        $magazines->delete();
                        return redirect()->route('magazine.index')->with('success','Registro eliminado satisfactoriamente.')
                             ->with('action','destroy');
                    }
                },
                'err' => function(){
                    return redirect()->route('magazine.index')->with('errorAccess', 'Esa Revista no existe.');
                }  
            ]
        );
        return $destroy;
    }
}
