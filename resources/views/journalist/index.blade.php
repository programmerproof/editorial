@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content')
<div class="row">
  <section class="content">
    <div class="col-md-10 col-md-offset-1">
      <div class="panel panel-default">
        <div class="panel-body">
          @if(session('success'))
            @if(session('action')=='create')
             <div class="alert alert-success alert-dismissible">
            @endif
            @if(session('action')=='edit'||session('action')=='destroy')
              <div class="alert alert-info alert-dismissible">
            @endif
                <button type="button" class="close" data-dismiss="alert" aria-hiddem="true"></button>
                <h4><i class="icon fa fa-check"></i> {{session('success')}} </h4>
            </div>
          @endif
          @if(session('errorAccess'))
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hiddem="true"></button>
              <h4><i class="icon fa fa-ban"></i> {{session('errorAccess')}} </h4>
            </div>
          @endif
          <div><h3>Lista Periodistas</h3></div>
          <div>

            <div class="panel panel-default">
                <div class="panel-body">
                  <div class="pull-right">
                    <div class="btn-group">
                    <form action="{{ route('journalist.index')}}" method="get" accept-charset="utf-8">
                        <div class="btn-group">
                          <input name="search" id="search" type="text" class="form-control">
                        </div>
                        <div class="btn-group">
                          <button type="submit" class="btn btn-info">Buscar por identificación</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
            </div>

            <div class="btn-group">
              <a href="{{ route('journalist.create') }}" class="btn btn-info" >Añadir Periodista</a>
            </div>
          </div>
          <div class="table-container">
            <table class="table table-condensed table-bordered table-hover">
             <thead>
               <th>Especialidad</th>
               <th>Nombres</th>
			         <th>Apellidos</th>
               <th>Identificación</th>
               <th>Dirección</th>
               <th>Teléfono</th>
			         <th>Móvil</th>
               <th class="text-center">Ver</th>
                @if($journalists->count())  
                  @foreach($journalists as $journalist)
                    @if($journalist->user_id == $user)
                      <th class="text-center">Editar</th>
                      <th class="text-center">Eliminar</th>
                      @break;
                    @endif
                  @endforeach 
                @endif
             </thead>
             <tbody>
                @if($journalists->count())  
                  @foreach($journalists as $journalist)  
                    <tr>
                      <td>{{$journalist->jrnspeciality->name}}</td>
                      <td>{{$journalist->name_1}} {{$journalist->name_2}}</td>
					            <td>{{$journalist->surname_1}} {{$journalist->surname_2}}</td>
                      <td>{{$journalist->id_card_num}}</td>
                      <td>{{$journalist->address}}</td>
                      <td>{{$journalist->phone_num}}</td>
					            <td>{{$journalist->mobile_num}}</td>
                      <td class="text-center">  
                        <a class="btn btn-primary btn-xs" href="{{ route('journalist.show', $journalist->id) }}"><span class="glyphicon glyphicon-eye-open"></span></a>
                      </td>
                    @if($journalist->user_id == $user)
                      <td class="text-center">
                        <a class="btn btn-primary btn-xs" href="{{action('JournalistController@edit', $journalist->id)}}" ><span class="glyphicon glyphicon-pencil"></span></a>
                      </td>
                      <td class="text-center">
                        <form action="{{action('JournalistController@destroy', $journalist->id)}}" method="post">
                          {{csrf_field()}}
                            <input name="_method" type="hidden" value="DELETE">
                            <button class="btn btn-danger btn-xs" type="submit"><span class="glyphicon glyphicon-trash"></span></button>
                        </form> 
                      </td>
                    @endif
                    </tr>
                  @endforeach 
               @else
                <tr>
                  <td colspan="8">No hay registro !!</td>
                </tr>
              @endif
            </tbody>
 
          </table>
        </div>
      </div>
      {{ $journalists->links() }}
    </div>
  </div>
</section>
 
@endsection