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
                        <th>Message</th>
                        <th>Action</th>
                    </tr>
                    @foreach($conversations['rooms'] as $conversation)
                        <tr>
                            <td>{{$conversation['room']->id}}</td>
                            <td>
                                {{$conversation['room_name']}}
                            </td>
                            <td>
                                @if(empty($conversation['room']['messages'][0]))
                                    {{--empty conversation--}}
                                @else
                                    @if(strlen($conversation['room']['messages'][0]->message) > 15) {{-- if message too big we should to truncate --}}
                                        {{ substr($conversation['room']['messages'][0]->message, 0, 15) . "..." }}
                                    @else
                                        {{ $conversation['room']['messages'][0]->message }} {{-- or echo all --}}
                                    @endif
                                @endif
                            </td>
                            <td>
                                <a href="{{ route("chats_conversion", ["chat_id" => $conversation['room']->id]) }}" class="btn-xs btn-warning">Join conversation</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

