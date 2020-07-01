window.appModules = {};
window.appModuleDirectives = {};
require('./bootstrap');
window.appModules.bindSingleRoom = require('./modules/single-room.mod');
window.io = require('socket.io-client');
window.Vue = require('vue');
import Echo from "laravel-echo"

console.log("Booted!");

window.authUser = JSON.parse(document.querySelector("meta[name='auth-user']").content);
window.eventCallbacks = {
    "chatMessageReceived" : [],
    "platformNotificationFired" : []
};

console.log("Hello user " + authUser.name);

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