@extends('adminlte::page')

@section('title', $room_name . ' chat ' . env("APP_NAME"))

@section('content_header')
    <h1>{{ $room_name }}</h1>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.11"></script>
    <script>
        var root_messages = JSON.parse('{!! str_replace("'", "\\'", $messages) !!}');
        var app = null;
        window.addEventListener("DOMContentLoaded", function(event) {
            app = new Vue({
                el: '#app',
                data: {
                    myUId: {{\Illuminate\Support\Facades\Auth::id()}},
                    messages: JSON.parse('{!! str_replace("'", "\\'", $messages) !!}'),
                    enteredMessage: "",
                    channelId: {{$room->id}},
                    channel: window.Echo.join("room.{{$room->id}}"),
                    fireUrl: "{{ route('chats_fireMessage', ['room_id' => $room->id]) }}",
                    activeUsers: []
                },
                mounted: function () {
                    this.channel.here(function (e) {
                        app.activeUsers = (e)
                    }).joining(function (e) {
                        app.activeUsers.push(e)
                    }).leaving(function (e) {
                        app.activeUsers.splice(app.activeUsers.indexOf(e));
                    }).listen('MessageFiredEvent', (e) => {
                            console.log(e);
                        })
                        .listen('message.fired', (e) => {
                            console.log(e);
                        })
                        .listen('.message.fired', (e) => {
                            console.log(e);
                        });

                },
                methods: {
                    sendMessage: function () {
                        var newMsg = {
                            created_at: 'Sending...',
                            message: this.enteredMessage,
                            user_id: this.myUId
                        };

                        var fireUrl = "{{ route('chats_fireMessage', ['room_id' => $room->id]) }}";
                        axios.post(fireUrl, {
                            message: this.enteredMessage,
                        }).then(function (response) {
                            app.messages[app.messages.length-1].created_at = new Date().toGMTString();
                        })
                            .catch(function (error) {
                                alert(error);
                            });
                        this.messages.push(newMsg);
                        this.enteredMessage = "";

                    }
                }
            });
        });

    </script>
@stop

@section('content')
    @include('flash::message')
    <div id="app" class="box box-primary">
        <div class="box-header with-border" >
            <div class="box-body table-responsive no-padding">
                    <div class="col-md-12" {{--style="position: fixed; bottom: 0; right: 0; background: white;"--}}>
                        <div class="box box-warning direct-chat direct-chat-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title">Direct Chat <p>@{{ enteredMessage }}</p></h3>
                                <span><b>Online</b>:
                                    <span v-for="user in activeUsers">
                                        @{{ user.user.name }},
                                    </span>
                                </span>
                            </div>
                            <div class="box-body">
                                <div class="direct-chat-messages" >
                                    <div v-for="msg in messages">
                                        <div v-if="msg.user_id == myUId" class="direct-chat-msg right">
                                            <div class="direct-chat-info clearfix">
                                                <span class="direct-chat-timestamp pull-left">@{{ msg.created_at }}</span>
                                            </div>
                                            <!-- /.direct-chat-info -->
                                            <img class="direct-chat-img" src="//adminlte.io/themes/AdminLTE/dist/img/user1-128x128.jpg">
                                            <!-- /.direct-chat-img -->
                                            <div class="direct-chat-text">
                                                @{{ msg.message }}
                                            </div>
                                            <!-- /.direct-chat-text -->
                                        </div>
                                        <div v-else class="direct-chat-msg">
                                            <div class="direct-chat-info clearfix">
                                                <span class="direct-chat-timestamp pull-right">@{{ msg.created_at }}</span>
                                            </div>
                                            <!-- /.direct-chat-info -->
                                            <img class="direct-chat-img" src="//adminlte.io/themes/AdminLTE/dist/img/user1-128x128.jpg">
                                            <!-- /.direct-chat-img -->
                                            <div class="direct-chat-text">
                                                @{{ msg.message }}
                                            </div>
                                            <!-- /.direct-chat-text -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <div class="input-group">
                                    <input v-model="enteredMessage" v-on:keyup.enter="sendMessage" placeholder="Type Message ..." class="form-control">
                                    <span class="input-group-btn">
                                            <button v-on:click="sendMessage" type="button" class="btn btn-warning btn-flat">Send</button>
                                        </span>
                                </div>
                            </div>
                            <!-- /.box-footer-->
                        </div>
                        <!--/.direct-chat -->
                    </div>
            </div>
        </div>
    </div>
@stop
