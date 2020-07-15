@extends('adminlte::page')

@section('title', 'Edit subscription #' . $subscription->id . " " . env("APP_NAME"))

@section('js')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-plans').select2();
        });
    </script>
@stop

@section('content_header')
    <h1>{{ __('Edit subscription #:id', ['id' => $subscription->id]) }}</h1>
@stop

@section('content')
    @include('flash::message')
    <div class="box box-primary">
        <div class="box-header with-border">

            <div class="box-body table-responsive no-padding">
                <form method="post" action="{{route("subscriptions.update", $subscription->id)}}">
                    @csrf
                    @method('put')
                    <input type="text" hidden name="subscription_id" value="{{$subscription->id}}">

                    <div class="card ">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title">{{ __('Edit subscription') }} #<b>{{$subscription->id}}</b></h4>
                        </div>
                        <div class="card-body ">
                            <div class="row">
                                <label class="col-md-2 col-form-label">{{ __('Select plan') }}</label>
                                <div class="col-md-7">
                                    <select class="se1lect2-plans" name="plan_id">
                                        @foreach($plans as $plan)
                                            <option {{ ($subscription->stripe_plan === $plan->id) ? "selected" : "" }} value="{{$plan->id}}">
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
