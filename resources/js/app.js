require('./bootstrap');

console.log("Booted!");

const authUser = JSON.parse(document.querySelector("meta[name='auth-user']").content);
console.log("Hello " + authUser.name);

import Echo from "laravel-echo"

window.io = require('socket.io-client');
window.Vue = require('vue');

window.eventCallbacks = {
    "chatMessageReceived" : [],
    "platformNotificationFired" : []
};

window.eventCallbacks.chatMessageReceived.push(function (event) {
    console.log("We have some message from bCastServer");
    console.log(event);
    console.log("--------------------------------------");
});

window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: window.location.hostname + ':6001'
});




/*.listen('.platformNotificationFired', (e) => {
    for(var i in window.eventCallbacks.chatMessageReceived){
        window.eventCallbacks.chatMessageReceived[i](e);
    }
})*/
console.log("Echo server connected!");