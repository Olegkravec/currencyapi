@extends('adminlte::page')

@section('title', 'Users ' . env("APP_NAME"))

@section('content_header')
    {{ $users->links() }}
    <div class="no-margin pull-right">
        <a href="{{ route('users.create') }}"><div class="btn btn-success btn-xs">{{ __('Create new user') }}</div></a>
    </div>
    <h1>{{__("Users list")}}</h1>
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
                        <th>{{ __("Name") }}</th>
                        <th>{{ __("Email") }}</th>
                        <th>{{ __("Roles") }}</th>
                        <th>{{ __("Created At") }}</th>
                        <th>{{__("Actions")}}</th>
                    </tr>

                    @foreach($users as $user)
                        <tr class="users users-{{$user->id}}">
                            <td>{{$user->id}}</td>
                            <td>{{$user->name}}</td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->getRoleNames()}}</td>
                            <td>{{$user->created_at}}</td>
                            <td>
                                @if($user->hasRole('admin'))
                                    @can("can chatting with others")
                                        <a href="{{ route('new_conversion', ['user_id' => $user->id]) }}" class="btn btn-xs btn-primary">{{ __('Start conversation') }}</a>
                                    @endcan
                                @endif

                                    @can("see permissions")
                                        <a href="{{ route('users_permission', ['id' => $user->id]) }}" class="btn btn-xs btn-primary">{{ __('Show permissions') }}</a>
                                    @endcan

                                @if($user->hasRole('client'))
                                    @can("see subscriptions")
                                        <a href="{{ route('users.subscriptions', ['user_id' => $user->id]) }}" class="btn btn-xs btn-primary">{{ __('Show subscription') }}</a>
                                    @endcan
                                @endif

                                @can("edit users")
                                    <a href="{{ route('users.edit', ['user' => $user->id]) }}" class="btn btn-xs btn-primary">{{ __('Edit user') }}</a>
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
