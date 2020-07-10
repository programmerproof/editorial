<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\User;
use App\State;
use App\City;
use App\Branch;
use App\Employee;

class EmployeeController extends Controller
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
            $employees = Employee::cardNumLike($search)->paginate();
        }else{ 
            $employees = Employee::with('branch')->with('city.state')->paginate();
        }   
        return view('employee.index',compact('employees'))
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
        $states = State::orderByNameAsc();
        $branches = Branch::orderByNameAsc();
        return view('employee.create', compact('states'), compact('branches'));
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
        $branch = Branch::find($request->branch_code);
        $employee = new Employee();
        $employee->user()->associate($user);
        $employee->branch()->associate($branch);
        $this->validate($request,
                           [
                            'state_code'=>'required|numeric', 
                            'city_code'=>'required|numeric',
                            'branch_code'=>'required|numeric', 
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
        $employee->name_1 = $request->name_1;
        $employee->name_2 = $request->name_2;
        $employee->surname_1 = $request->surname_1;
        $employee->surname_2 = $request->surname_2;
        $employee->id_card_num = $request->id_card_num;
        $employee->address = $request->address;
        $employee->phone_num = $request->phone_num;
        $employee->mobile_num = $request->mobile_num;
        $employee->created_at = date('Y-m-d H:i:s', time());
        $employee->save();
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
            return redirect()->route('employee.index')->with('success', 'Registro creado satisfactoriamente.')
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
        $show = Employee::validate(
            (object) [
                'id' => $id,
                'success' => function() use($id){
                    $employees = Employee::where('id', $id)->with('branch')->with('city.state')->get();
                    $states = State::orderByNameAsc(); 
                    $cities = City::whereStateCode($employees[0]->branch->city->state->code);
                    return view('employee.show', compact('employees', 'states'))->with('cities', $cities);
                },
                'err' => function(){
                    return redirect()->route('employee.index')->with('errorAccess', 'Ese Empleado no existe.');
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
        $edit = Employee::validate(
            (object) [
                'id' => $id,
                'success' => function() use($id){
                    $user_id = \Auth::id();
                    $employees = Employee::where('id', $id)->with('branch')->with('city.state')->get();
                    $states = State::orderByNameAsc(); 
                    $cities = City::whereStateCode($employees[0]->branch->city->state->code);
                    $branches = Branch::whereCityCode($employees[0]->branch->city->code);
                    
                    if($employees[0]->user_id != $user_id){
                        return redirect()->route('employee.index')->with('errorAccess', 'No tiene acceso a ese Empleado.');
                    }else{
                        return view('employee.edit', compact('branches', 'employees', 'states'))->with('cities', $cities);
                    }
                },
                'err' => function(){
                    return redirect()->route('employee.index')->with('errorAccess', 'Ese Empleado no existe.');
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
        $branch = Branch::find($request->branch_code);
        $employee = Employee::find($id);
        $employee->user()->associate($user);
        $employee->branch()->associate($branch);
        $this->validate($request,
                           [
                            'state_code'=>'required|numeric', 
                            'city_code'=>'required|numeric',
                            'branch_code'=>'required|numeric', 
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
        $employee->name_1 = $request->name_1;
        $employee->name_2 = $request->name_2;
        $employee->surname_1 = $request->surname_1;
        $employee->surname_2 = $request->surname_2;
        $employee->id_card_num = $request->id_card_num;
        $employee->address = $request->address;
        $employee->phone_num = $request->phone_num;
        $employee->mobile_num = $request->mobile_num;
        $employee->created_at = date('Y-m-d H:i:s', time());
        $employee->save();
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
        return redirect()->route('employee.index')->with('success','Registro actualizado satisfactoriamente.')
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
        $destroy = Employee::validate(
            (object) [
                'id' => $id,
                'success' => function() use($id){
                    $user_id = \Auth::id();
                    $employees = Employee::find($id);

                    if($employees->user_id != $user_id){
                        return redirect()->route('employee.index')->with('errorAccess', 'No tiene acceso a ese Empleado.');
                    }else{
                        $employees->delete();
                        return redirect()->route('employee.index')->with('success','Registro eliminado satisfactoriamente.')
                             ->with('action','destroy');
                    }
                },
                'err' => function(){
                    return redirect()->route('employee.index')->with('errorAccess', 'Ese Empleado no existe.');
                }  
            ]
        );
        return $destroy;
    }
}
