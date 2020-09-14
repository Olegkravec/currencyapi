export default {
    install(Vue) {
        Vue.mixin({
            mounted() {
                if(window.xhrInterPlugEventer) // Check if already registered event
                    return;

                window.xhrInterPlugEventer = true;

                window.axios.interceptors.response.use(function (response) {
                    console.log(response);
                    return response;
                }, function (error) {
                    console.log(response);
                    return Promise.reject(error);
                });
            }
        });
    }
};
