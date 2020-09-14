<template>
    <div class="ChatsIndex">
        <h2>Chat rooms</h2>
        <table>
            <thead>
            <tr>
                <td>#ID</td>
                <td>Name</td>
                <td>Actions</td>
            </tr>
            </thead>
            <tbody>
            <tr v-for="chat in chats" :key="chat.id">
                <td>{{ chat.id }}</td>
                <td>{{ chat.name }}</td>
                <td>Actions</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
const axios = require('axios');
    export default {
        name: "LoginComponent",
        data: function () {
            return {
                chats: [],
            }
        },
        mounted() {
            axios.post("//" + process.env.VUE_APP_API_DOMAIN + "/api/v2/chats").then(function (resp) {
                console.log("CHAT RESPONSE")
                console.log(resp)
            })
        }
    };
</script>
