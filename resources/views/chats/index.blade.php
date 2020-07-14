@extends('adminlte::page')

@section('title', 'Chats ' . env("APP_NAME"))

@section('content_header')
    <h1>Chat list</h1>
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
                        <th>Type</th>
                        <th>Action</th>
                    </tr>
                    @foreach($conversations as $conversation)
                        <tr>
                            <td>{{$conversation->id}}</td>
                            <td>
                                {{$conversation->name}}
                            </td>
                            <td>
                                @if($conversation->isGroup)
                                    Group
                                @else
                                    Direct
                                @endif
                            </td>
                            <td>
                                <a href="{{ route("chats_conversion", ["chat_id" => $conversation->id]) }}" class="btn-xs btn-warning">Join conversation</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

