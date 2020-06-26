@extends('adminlte::page')

@section('title', 'Permission list ' . env("APP_NAME"))

@section('content_header')
    <div class="no-margin pull-right">
        <a href="{{ route('users_create') }}"><div class="btn btn-success btn-xs">Create new user</div></a>
    </div>
    <h1>Permission list for user <b>{{ $user->name }}</b> </h1>
@stop

@section('content')
    @include('flash::message')
    <div class="box box-primary">
        <div class="box-header with-border">

            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Guard</th>
                        <th>Created At</th>
                        <th>Actions</th>
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
                                        <button class="btn btn-xs btn-danger" type="submit">Revoke Permission</button>
                                    </form>
                                @endcan

                                @can("see permissions")
                                        <a href="{{ route('users_permission', ['id' => $user->id]) }}" class="btn btn-xs btn-primary">Show permissions</a>
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
