@extends('adminlte::page')

@section('title', 'Invite members ' . env("APP_NAME"))

@section('content_header')
    <h1>{{ __('Invite more to room #:id', ['id' => $room->id]) }}</h1>
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
                        <th>{{ __('Action') }}</th>
                    </tr>
                    <form method="post" action="{{route("chats.saveInvites", $room->id)}}">
                        @csrf
                        @method('put')

                        @foreach($users as $user)
                            <tr>
                                <td>{{$user->id}}</td>
                                <td>{{$user->name}}</td>
                                <td>
                                    @if($room->containMember($user->id))
                                        <input type="checkbox" disabled="">
                                    @else
                                        <input type="checkbox" name="id[]" value="{{ $user->id }}">
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <div class="card-footer ml-auto mr-auto">
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        </div>
                        @if ($errors->has('id'))
                            <span id="users-error" class="error text-danger" for="input-users">{{ $errors->first('id') }}</span>
                        @endif
                        @if ($errors->has('id.*'))
                            <span id="users-error" class="error text-danger" for="input-users">{{ $errors->first('id.*') }}</span>
                        @endif
                    </form>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@stop

