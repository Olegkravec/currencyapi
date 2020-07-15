@extends('adminlte::page')

@section('title', 'Subscription list ' . env("APP_NAME"))

@section('content_header')
    @can("edit subscription")
        <div class="no-margin pull-right">
            <a href="{{ route('subscriptions.createAssigned', $user->id) }}"><div class="btn btn-success btn-xs">{{ __('Make new subscription') }}</div></a>
        </div>
    @endcan
    <h1>{!!  __('Subscriptions list for user <b>:name</b>', ['name' => $user->name])  !!}</h1>
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
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                    @foreach($subscriptions as $subscription)
                        <tr class="bg-{{ $subscription->stripe_status == "active" ? 'white' : 'black' }}">
                            <td>{{$subscription->id}}</td>
                            <td>{{$subscription->name}}</td>
                            <td>{{$subscription->stripe_status}}</td>

                            <td>
                                @can("edit subscription")
                                    @if($subscription->stripe_status == "active")
                                        <a href="{{ route('subscriptions.edit', $subscription->id) }}" class="btn btn-xs btn-primary">Edit</a>
                                        <form method="post" action="{{route("subscriptions.destroy", $subscription->id)}}">
                                        @csrf
                                        @method('delete')
                                            <button type="submit" class="btn btn-xs btn-primary">{{ __('Cancel') }}</button>
                                        </form>
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
