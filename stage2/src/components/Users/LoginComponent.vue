<template>
    <div class="cotainer">
        <div class="row justify-content-md-center">
            <div class="col-md-4">
                <div>
                    <div class="form-group">
                        <label for="emailInput">Email address</label>
                        <input type="email" v-model="email" class="form-control" id="emailInput" aria-describedby="emailHelp">
                        <small class="error">{{emailErrors}}</small>
                    </div>
                    <div class="form-group">
                        <label for="passwordInput">Password</label>
                        <input type="password" v-model="password" class="form-control" id="passwordInput">
                        <small class="error">{{passwordErrors}}</small>
                    </div>
                    <button v-on:click="tryLogin" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const axios = require('axios');

    console.log(process.env)
    export default {
        name: "LoginComponent",
        data: function () {
            return {
                email: "",
                emailErrors: "",
                password: "",
                passwordErrors: "",
            }
        },
        methods: {
            tryLogin: function () {
                axios.post("//" + process.env.VUE_APP_API_DOMAIN + "/api/v1/signin", {
                    email: this.email,
                    password: this.password,
                }).catch((reason) => {
                    /**
                     * Check errors, echo errors
                     */
                    if(reason.response.data.errors){
                        if(reason.response.data.errors.email && reason.response.data.errors.email.length > 0){
                            this.emailErrors = reason.response.data.errors.email.join()
                        }
                        if(reason.response.data.errors.password && reason.response.data.errors.password.length > 0){
                            this.passwordErrors = reason.response.data.errors.password.join()
                        }
                    }

                    if(reason.response.data.error && reason.response.data.error === "Unauthorized"){
                        this.passwordErrors = "Wrong email or password."
                    }
                }).then((response) => {
                    if(response.data.id && response.data.id > 0){
                        this.passwordErrors = "Successfully logged in!"
                        let fireableData = response.data;
                        fireableData.access_token = response.headers.access_token
                        this.$events.fire('loggedInSuccessfully', fireableData)

                        this.$router.push("/")
                    }
                })
            }
        }
    }
</script>

<style scoped>
    .form-group .error{
        color: red;
    }
</style>
