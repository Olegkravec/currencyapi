@extends('adminlte::page')

@section('title', 'Create new subscription ' . env("APP_NAME"))

@section('content_header')
    <h1>{{ __('Create new subscription') }}</h1>
@stop

@section('content')
    @include('flash::message')
    <div class="box box-primary">
        <div class="box-header with-border">

            <div class="box-body table-responsive no-padding">
                <form method="post" action="{{route("subscriptions.store")}}">
                    @csrf

                    <input type="text" hidden name="user_id" value="{{$user->id}}">

                    <div class="card ">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title">{{ __('Crete subscription for') }} <b>{{$user->name}}</b></h4>
                        </div>
                        <div class="card-body ">
                            <div class="row">
                                <label class="col-md-2 col-form-label">{{ __('Select plan') }}</label>
                                <div class="col-md-7">
                                    <select class="se1lect2-plans" name="plan_id">
                                        @foreach($plans as $plan)
                                            <option value="{{$plan->id}}">
                                                {{$plan->nickname}} / {{ $plan->amount/100 }} {{ $plan->currency }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer ml-auto mr-auto">
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        </div>
                    </div>
                </form>
            </div>


        </div>
    </div>
@stop
