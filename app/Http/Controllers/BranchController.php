<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\User;
use App\State;
use App\City;
use App\Branch;

class BranchController extends Controller
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
            $branches = Branch::nameLike($search)->paginate();
        }else{ 
            $branches = Branch::with('city.state')->paginate();
        }   
        return view('branch.index',compact('branches'))
                 ->with('user', $user_id);   
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        $states = State::orderByNameAsc();
        return view('branch.create', compact('states'));
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
        $user = User::find( $user_id);
        $city = City::find($request->city_code);
        $branch = new Branch();
        $branch->user()->associate($user);
        $branch->city()->associate($city);
        $this->validate($request,
                        [
                         'state_code'=>'required|numeric', 
                         'city_code'=>'required|numeric', 
                         'name'=>'required|regex:/^([0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/|max:30', 
                         'address'=>'required|regex:/^([0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/|max:30', 
                         'phone_num'=>'required|numeric|min:999999|max:9999999'
                        ]
                       );
        $branch->name = $request->name;
        $branch->address = $request->address;
        $branch->phone_num = $request->phone_num;
        $branch->created_at = date('Y-m-d H:i:s', time());
        $branch->save();
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
            return redirect()->route('branch.index')->with('success','Registro creado satisfactoriamente.')
                            ->with('action','create');     
        }          
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($code)
    {
        //
        $show = Branch::validate(
            (object) [
                'code' => $code,
                'success' => function() use($code){
                    $branches = Branch::where('code', $code)->with('city.state')->get();
                    $states = State::orderByNameAsc(); 
                    $cities = City::whereStateCode($branches[0]->city->state->code);
                    return view('branch.show', compact('branches', 'states'))->with('cities', $cities);
                },
                'err' => function(){
                    return redirect()->route('branch.index')->with('errorAccess', 'Esa Sucursal no existe.');
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
    public function edit(Request $request, $code)
    {
        $edit = Branch::validate(
            (object) [
                'code' => $code,
                'success' => function() use($code){
                    $user_id = \Auth::id();
                    $branches = Branch::where('code', $code)->with('city.state')->get();
                    $states = State::orderByNameAsc(); 
                    $cities = City::whereStateCode($branches[0]->city->state->code);
                    
                    if($branches[0]->user_id != $user_id){
                        return redirect()->route('branch.index')->with('errorAccess', 'No tiene acceso a esa Sucursal.');
                    }else{
                        return view('branch.edit', compact('branches', 'states'))->with('cities', $cities);
                    }
                },
                'err' => function(){
                    return redirect()->route('branch.index')->with('errorAccess', 'Esa Sucursal no existe.');
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
    public function update(Request $request, $code)
    {
        //
        $exception = '';
    try{
        $user_id = \Auth::id();
        $user = User::find($user_id);
        $city = City::find($request->city_code);
        $branch = Branch::find($code);
        $branch->user()->associate($user);
        $branch->city()->associate($city);
        $this->validate($request,
                        [
                         'state_code'=>'required|numeric', 
                         'city_code'=>'required|numeric', 
                         'name'=>'required|regex:/^([0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/|max:30', 
                         'address'=>'required|regex:/^([0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/|max:30', 
                         'phone_num'=>'required|numeric|min:999999|max:9999999'
                        ]
                       );
        $branch->name = $request->name;
        $branch->address = $request->address;
        $branch->phone_num = $request->phone_num;
        $branch->created_at = date('Y-m-d H:i:s', time());
        $branch->save();
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
        return redirect()->route('branch.index')->with('success','Registro actualizado satisfactoriamente.')
                         ->with('action','edit');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($code)
    {
        //
        $destroy = Branch::validate(
            (object) [
                'code' => $code,
                'success' => function() use($code){
                    $user_id = \Auth::id();
                    $branches = Branch::find($code);

                    if($branches->user_id != $user_id){
                        return redirect()->route('branch.index')->with('errorAccess', 'No tiene acceso a esa Sucursal.');
                    }else{
                        $branches->delete();
                        return redirect()->route('branch.index')->with('success','Registro eliminado satisfactoriamente.')
                             ->with('action','destroy');
                    }
                },
                'err' => function(){
                    return redirect()->route('branch.index')->with('errorAccess', 'Esa Sucursal no existe.');
                }  
            ]
        );
        return $destroy;
    }

    /**
     * Display the specific resource
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request, $code)
    {
        //
        $result = Branch::whereCityCode($code);
        return \Response::json($result);
    }
}
