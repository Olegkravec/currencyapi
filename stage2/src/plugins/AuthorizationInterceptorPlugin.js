import UserModel from "@/models/UserModel"

export default {
    install(Vue) {
        Vue.mixin({
            mounted() {
                if(window.authInterPlugEventer) // Check if already registered event
                    return;

                window.authInterPlugEventer = true;

                this.$events.listen('loggedInSuccessfully', (eventData) => {
                    let user = new UserModel(eventData);
                    window.user = user;
                    Vue.localStorage.set("user", user, UserModel)
                });
            }
        });
    }
};
