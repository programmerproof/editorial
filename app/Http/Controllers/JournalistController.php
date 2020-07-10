<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\User;
use App\Jrnspeciality;
use App\Journalist;

class JournalistController extends Controller
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
            $journalists = Journalist::cardNumLike($search)->paginate();
        }else{ 
            $journalists = Journalist::with('jrnspeciality')->paginate();
        }   
        return view('journalist.index',compact('journalists'))
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
        $jrnspecialities = Jrnspeciality::orderByNameAsc();
        return view('journalist.create', compact('jrnspecialities'));
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
        $jrnspeciality = Jrnspeciality::find($request->jrnspeciality_id);
        $journalist = new Journalist();
        $journalist->user()->associate($user);
        $journalist->jrnspeciality()->associate($jrnspeciality);
        $this->validate($request,
                           [
                            'jrnspeciality_id'=>'required|numeric', 
                            'name_1'=>'required|regex:/^[A-Za-z-ZñÑáéíóúÁÉÍÓÚ][A-Za-z0-9-ZñÑáéíóúÁÉÍÓÚ]*$/|min:3|max:30', 
                            'name_2'=>'regex:/^[A-Za-z-ZñÑáéíóúÁÉÍÓÚ][A-Za-z0-9-ZñÑáéíóúÁÉÍÓÚ]*$/|min:3|max:30', 
                            'surname_1'=>'required|regex:/^[A-Za-z-ZñÑáéíóúÁÉÍÓÚ][A-Za-z0-9-ZñÑáéíóúÁÉÍÓÚ]*$/|min:3|max:30', 
                            'surname_2'=>'regex:/^[A-Za-z-ZñÑáéíóúÁÉÍÓÚ][A-Za-z0-9-ZñÑáéíóúÁÉÍÓÚ]*$/|min:3|max:30', 
                            'id_card_num'=>'required|numeric|min:99999999|max:9999999999',
                            'address'=>'required|regex:/^([0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/|min:3|max:30', 
                            'phone_num'=>'required|numeric|min:999999|max:9999999',
                            'mobile_num'=>'required|numeric|min:999999999|max:9999999999'
                           ]
                       );
        $journalist->name_1 = $request->name_1;
        $journalist->name_2 = $request->name_2;
        $journalist->surname_1 = $request->surname_1;
        $journalist->surname_2 = $request->surname_2;
        $journalist->id_card_num = $request->id_card_num;
        $journalist->address = $request->address;
        $journalist->phone_num = $request->phone_num;
        $journalist->mobile_num = $request->mobile_num;
        $journalist->created_at = date('Y-m-d H:i:s', time());
        $journalist->save();
        }catch(\Illuminate\Database\QueryException $e){
            $errorCode = $e->errorInfo[1];
            $err = '';
            switch ($errorCode) {
                case 1062: 
                    $err = response([
                        'errors'=>$e->getMessage()
                    ],Response::HTTP_NOT_FOUND);
                    $exception = 'EL número '.$request->id_card_num.' de identificación ya existe.';
                break;
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
            return redirect()->route('journalist.index')->with('success', 'Registro creado satisfactoriamente.')
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
        $show = Journalist::validate(
            (object) [
                'id' => $id,
                'success' => function() use($id){
                    $journalists = Journalist::where('id', $id)->with('jrnspeciality')->get();
                    return view('journalist.show', compact('journalists'));
                },
                'err' => function(){
                    return redirect()->route('journalist.index')->with('errorAccess', 'Ese Periodista no existe.');
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
        $edit = Journalist::validate(
            (object) [
                'id' => $id,
                'success' => function() use($id){
                    $user_id = \Auth::id();
                    $journalists = Journalist::where('id', $id)->with('jrnspeciality')->get();
                    $jrnspecialities = Jrnspeciality::orderByNameAsc(); 
                    
                    if($journalists[0]->user_id != $user_id){
                        return redirect()->route('journalist.index')->with('errorAccess', 'No tiene acceso a ese Periodista.');
                    }else{
                        return view('journalist.edit', compact('jrnspecialities', 'journalists'));
                    }
                },
                'err' => function(){
                    return redirect()->route('journalist.index')->with('errorAccess', 'Ese Periodista no existe.');
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
        $jrnspeciality = Jrnspeciality::find($request->jrnspeciality_id);
        $journalist = Journalist::find($id);
        $journalist->user()->associate($user);
        $journalist->jrnspeciality()->associate($jrnspeciality);
        $this->validate($request,
                           [
                            'jrnspeciality_id'=>'required|numeric', 
                            'name_1'=>'required|regex:/^[A-Za-z-ZñÑáéíóúÁÉÍÓÚ][A-Za-z0-9-ZñÑáéíóúÁÉÍÓÚ]*$/|min:3|max:30', 
                            'name_2'=>'regex:/^[A-Za-z-ZñÑáéíóúÁÉÍÓÚ][A-Za-z0-9-ZñÑáéíóúÁÉÍÓÚ]*$/|min:3|max:30', 
                            'surname_1'=>'required|regex:/^[A-Za-z-ZñÑáéíóúÁÉÍÓÚ][A-Za-z0-9-ZñÑáéíóúÁÉÍÓÚ]*$/|min:3|max:30', 
                            'surname_2'=>'regex:/^[A-Za-z-ZñÑáéíóúÁÉÍÓÚ][A-Za-z0-9-ZñÑáéíóúÁÉÍÓÚ]*$/|min:3|max:30', 
                            'id_card_num'=>'required|numeric|min:99999999|max:9999999999',
                            'address'=>'required|regex:/^([0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/|min:3|max:30', 
                            'phone_num'=>'required|numeric|min:999999|max:9999999',
                            'mobile_num'=>'required|numeric|min:999999999|max:9999999999'
                           ]
                       );
        $journalist->name_1 = $request->name_1;
        $journalist->name_2 = $request->name_2;
        $journalist->surname_1 = $request->surname_1;
        $journalist->surname_2 = $request->surname_2;
        $journalist->id_card_num = $request->id_card_num;
        $journalist->address = $request->address;
        $journalist->phone_num = $request->phone_num;
        $journalist->mobile_num = $request->mobile_num;
        $journalist->created_at = date('Y-m-d H:i:s', time());
        $journalist->save();
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
        return redirect()->route('journalist.index')->with('success','Registro actualizado satisfactoriamente.')
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
        $destroy = Journalist::validate(
            (object) [
                'id' => $id,
                'success' => function() use($id){
                    $user_id = \Auth::id();
                    $journalists = Journalist::find($id);

                    if($journalists->user_id != $user_id){
                        return redirect()->route('journalist.index')->with('errorAccess', 'No tiene acceso a ese Peridista.');
                    }else{
                        $journalists->delete();
                        return redirect()->route('journalist.index')->with('success','Registro eliminado satisfactoriamente.')
                             ->with('action','destroy');
                    }
                },
                'err' => function(){
                    return redirect()->route('journalist.index')->with('errorAccess', 'Ese Periodista no existe.');
                }  
            ]
        );
        return $destroy;
    }
}
