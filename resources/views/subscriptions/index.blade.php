@extends('adminlte::page')

@section('title', 'Subscriptions ' . env("APP_NAME"))

@section('content_header')
    <h1>Subscriptions list</h1>
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
                        <th>Status</th>
                        <th>User</th>
                        <th>Action</th>
                    </tr>

                    @foreach($subscriptions as $subscription)
                        <tr  class="bg-{{ $subscription->stripe_status == "active" ? 'white' : 'black' }}">
                            <td>{{$subscription->id}}</td>
                            <td>{{ $subscription->name }}</td>
                            <td>{{$subscription->stripe_status}}</td>
                            <td>{{ \App\User::find($subscription->user_id)->name }}</td>
                            <td>
                                @can("edit subscription")
                                    @if($subscription->stripe_status == "active")
                                        <a href="{{ route('subscriptions.edit', $subscription->id) }}" class="btn btn-xs btn-primary">Edit</a>
                                    @endif
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
