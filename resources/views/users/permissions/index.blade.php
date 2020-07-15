@extends('adminlte::page')

@section('title', 'Permission list ' . env("APP_NAME"))

@section('content_header')
    <div class="no-margin pull-right">
        <a href="{{ route('users_permission_grant', ["user_id" => $user->id]) }}"><div class="btn btn-success btn-xs">Grant new permission</div></a>
    </div>
    <h1>{!! __('Permission list for user <b>:name</b>', ['name'=>$user->name]) !!} </h1>
@stop

@section('content')
    @include('flash::message')
    <div class="box box-primary">
        <div class="box-header with-border">

            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Guard') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                    @foreach($permissions as $permission)
                        <tr class="users users-{{$permission->id}}">
                            <td>{{$permission->id}}</td>
                            <td>{{$permission->name}}</td>
                            <td>{{$permission->guard_name}}</td>
                            <td>{{$permission->created_at}}</td>
                            <td>
                                @can("edit users permissions")
                                    <form method="post" action="{{ route('users_permission_revoke', ['user_id' => $user->id, 'permission_id' => $permission->id]) }}">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-xs btn-danger" type="submit">{{ __('Revoke Permission') }}</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>


        </div>
    </div>
@stop
