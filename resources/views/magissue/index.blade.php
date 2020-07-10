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
          <div><h3>Lista Revistas</h3></div>
          <div>

            <div class="panel panel-default">
                <div class="panel-body">
                  <div class="pull-right">
                    <div class="btn-group">
                    <form action="{{ route('magissue.index')}}" method="get" accept-charset="utf-8">
                        <div class="btn-group">
                          <input name="search" id="search" type="text" class="form-control">
                        </div>
                        <div class="btn-group">
                          <button type="submit" class="btn btn-info">Buscar por Título de revista</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
            </div>

            <div class="btn-group">
              <a href="{{ route('magissue.create') }}" class="btn btn-info" >Añadir Número</a>
            </div>
          </div>
          <div class="table-container">
            <table class="table table-condensed table-bordered table-hover">
             <thead>
               <th>Título</th>
               <th>Fecha</th>
               <th>Número páginas</th>
			         <th>Número ejemplares</th>
               <th class="text-center">Ver</th>
                @if($magissues->count())  
                  @foreach($magissues as $magissue)
                    @if($magissue->user_id == $user)
                      <th class="text-center">Editar</th>
                      <th class="text-center">Eliminar</th>
                      @break;
                    @endif
                  @endforeach 
                @endif
             </thead>
             <tbody>
                @if($magissues->count())  
                  @foreach($magissues as $magissue)  
                    <tr>
                      <td>{{$magissue->magazine->title}}</td>
                      <td>{{$magissue->date}}</td>
                      <td>{{$magissue->pages_num}}</td>
                      <td>{{$magissue->copies_num}}</td>
                      <td class="text-center">  
                        <a class="btn btn-primary btn-xs" href="{{ route('magissue.show', $magissue->id) }}"><span class="glyphicon glyphicon-eye-open"></span></a>
                      </td>
                    @if($magissue->user_id == $user)
                      <td class="text-center">
                        <a class="btn btn-primary btn-xs" href="{{action('MagissueController@edit', $magissue->id)}}" ><span class="glyphicon glyphicon-pencil"></span></a>
                      </td>
                      <td class="text-center">
                        <form action="{{action('MagissueController@destroy', $magissue->id)}}" method="post">
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
      {{ $magissues->links() }}
    </div>
  </div>
</section>
 
@endsection