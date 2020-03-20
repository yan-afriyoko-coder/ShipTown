<template>
    <div>
        <div>
            <div class="card card-default">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>
                            Web Bridge Configuration
                        </span>
                    </div>
                </div>

                <div class="card-body">

                    <table class="table table-borderless mb-0">
                        <thead>
                            <tr>
                                <th>
                                    <div class="form-group">
                                        <label for="web_store_key">API Key</label>
                                        <input type="text" class="form-control" id="web_store_key" v-model="web_store_key" placeholder="Enter new API key">
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th><div class="text-center"><button id="product-btn" class="btn-primary centre" v-on:click="saveConfiguration">Save</button></div></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        data() {
            return {
                // configuration: [],
                web_store_key: ""
            };
        },

        /**
         * Prepare the component (Vue 1.x).
         */
        ready() {
            this.prepareComponent();
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.prepareComponent();
        },

        methods: {

            prepareComponent() {
                this.getConfiguration();
            },

            /**
             * Get all of the personal access tokens for the user.
             */
            getConfiguration() {
                // axios.get('/api/company/configuration')
                //     .then(response => {
                //         this.configuration = response.data;
                //     });
            },

            saveConfiguration : function() {
                axios.post('/api/company/configuration', {
                    "bridge_api_key": this.web_store_key
                })
                .then(response => {
                    //alert("Configuration saved");
                })
                .catch(error => {
                    alert("Something went wrong. Please try again");
                });
            }
        }
    }

</script>
