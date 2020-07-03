@extends('adminlte::page')

@section('title', 'Invite members ' . env("APP_NAME"))

@section('content_header')
    <h1>Invite more to room #{{$room->id}}</h1>
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
                        <th>Action</th>
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
                            <button type="submit" class="btn btn-primary">Save</button>
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

