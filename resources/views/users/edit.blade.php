@extends('adminlte::page')

@section('title', 'Users ' . env("APP_NAME"))

@section('content_header')
    <div class="no-margin pull-right">
        <a href="{{ route('users_create') }}"><div class="btn btn-success btn-xs">Create new user</div></a>
    </div>
    <h1>Users list</h1>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.0/clipboard.min.js"></script>

    <script>
        var clipboard = new ClipboardJS('.copy');

        function open_linkedBoxes( element, id ){
            // jQuery("#linkedBox_description_place"+id).html(element.getAttribute("data_desc"));
            $('#linkedBox_'+id).modal();
        }
    </script>
@stop

@section('content')
    @include('flash::message')
    <div class="box box-primary">
        <div class="box-header with-border">

            <div class="box-body table-responsive no-padding">

            </div>


        </div>
    </div>
@stop
