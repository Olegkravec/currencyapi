export const bind = function (room, defaultMessages, fireUrl) {
    if(!!!fireUrl){ // If undefined set fireUrl to default
        fireUrl = "/chats/fire/" + room.id;
    }
    if(!!!window.singleRoomBinds)
        window.singleRoomBinds = {};

    if(!!window.singleRoomBinds[room.id])
        throw "Room already bined";

    window.singleRoomBinds[room.id] = new Vue({
        el: '#app',
        data: {
            myName: window.authUser.name,
            myUId: window.authUser.id,
            messages: defaultMessages,
            enteredMessage: "",
            channelId: room.id,
            channel: window.Echo.join("room." + room.id),
            fireUrl: fireUrl,
            activeUsers: [],
            isTyping: "",
            typingTimer: null
        },
        mounted: function () {
            this.channel.here(function (e) {
                window.singleRoomBinds[room.id].activeUsers = (e)
            }).joining(function (e) {
                window.singleRoomBinds[room.id].activeUsers.push(e)
            }).leaving(function (e) {
                window.singleRoomBinds[room.id].activeUsers.splice(window.singleRoomBinds[room.id].activeUsers.indexOf(e));
            }).listen('.messageFired', (e) => {
                var newMsg = {
                    created_at: new Date().toGMTString(),
                    message: e.message.message,
                    user_id: e.user.id
                };
                window.singleRoomBinds[room.id].messages.push(newMsg);
                setTimeout(function () {
                    document.getElementById( 'bottom' ).scrollIntoView();
                }, 500);
            }).listenForWhisper('typing', (e) => {
                window.singleRoomBinds[room.id].isTyping = e.user + " is typing...";
                if(!e.typing)
                    window.singleRoomBinds[room.id].isTyping = ""
            });
        },
        methods: {
            startTyping: function(){
                if(!!!window.singleRoomBinds[room.id].typingTimer) {
                    window.singleRoomBinds[room.id].typingTimer = setTimeout(function () {
                        window.singleRoomBinds[room.id].channel.whisper('typing', {
                            user: this.myName  + " is typing...",
                            typing: false
                        });
                        window.singleRoomBinds[room.id].typingTimer = null;
                    }, 5000);
                }
                this.channel.whisper('typing', {
                    user: this.myName,
                    typing: true
                });
            },
            sendMessage: function () {
                var newMsg = {
                    created_at: 'Sending...',
                    message: this.enteredMessage,
                    user_id: this.myUId
                };

                axios.post(fireUrl, {
                    message: this.enteredMessage,
                }).then(function (response) {
                    window.singleRoomBinds[room.id].messages[window.singleRoomBinds[room.id].messages.length-1].created_at = new Date().toGMTString();
                })
                    .catch(function (error) {
                        alert(error);
                    });
                this.enteredMessage = "";
            }
        }
    });
};