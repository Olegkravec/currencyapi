@extends('adminlte::page')

@section('title', 'Grant permission ' . env("APP_NAME"))

@section('content_header')
    <h6><a href="{{ route('users_permission', ['id' => $user->id]) }}"><- return to {{ $user->name }}'s permission list</a></h6>

    <h1>Grant permission to user <b>{{ $user->name }}</b> </h1>
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
                        @if($user->hasPermissionTo($permission->name))
                            @continue
                        @endif
                        <tr class="users users-{{$permission->id}}">
                            <td>{{$permission->id}}</td>
                            <td>{{$permission->name}}</td>
                            <td>{{$permission->guard_name}}</td>
                            <td>{{$permission->created_at}}</td>
                            <td>
                                @can("edit users permissions")
                                    <form method="post" action="{{ route('users_permission_create', ['user_id' => $user->id, 'permission_id' => $permission->id]) }}">
                                        @csrf
                                        @method('post')
                                        <button class="btn btn-xs btn-success" type="submit">Grant Permission</button>
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
