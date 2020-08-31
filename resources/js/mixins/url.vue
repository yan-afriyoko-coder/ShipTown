<template>

</template>

<script>
    import VueRouter from "vue-router";

    Vue.use(VueRouter);

    const Router = new VueRouter({
        mode: 'history',
    });


    export default {
        name: "url",

        router: Router,

        methods: {
            getUrlFilterOrSet: function(name, defaultValue) {
                let value = this.getUrlFilter(name);
            },

            setUrlFilter: function(name, defaultValue = null) {
                const $fullFilterName = 'filter['+name+']';
                return this.setUrlParameter($fullFilterName, defaultValue);
            },

            getUrlFilter: function(name, defaultValue = null) {
                const $fullFilterName = 'filter['+name+']';
                return this.getUrlParameter($fullFilterName, defaultValue);
            },

            getUrlParameter: function(param, defaultValue = null) {
                const $urlParameters = this.$router.currentRoute.query;
                return this.getValueOrDefault($urlParameters[param], defaultValue);
            },

            setUrlParameter: function(param, value) {
                this.$router.currentRoute.query[param] = value;
                this.updateUrl(this.$router.currentRoute.query);
            },

            updateUrl: function(params) {
                let url = this.$router.currentRoute.path + '?';

                for (let element in params) {
                   url += element +'=' + params[element] + '&';
                }

                this.$router.push(url);
            },

            getValueOrDefault: function (value, defaultValue){
                return (value === undefined) || (value === null) ? defaultValue : value;
            },
        }
    }
</script>

<style scoped>

</style>
