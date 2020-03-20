<template>
    <div>
        <div>
            <div class="card card-default">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>
                            API2CART Configuration
                        </span>
                    </div>
                </div>

                <div class="card-body">

                    <table class="table table-borderless mb-0">
                        <thead>
                            <tr>
                                <th>
                                    <div class="form-group">
                                        <label for="storeKey">Store Key</label>
                                        <input type="text" class="form-control" id="storeKey" v-model="storeKey" placeholder="Store Key">
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th><div class="text-center"><button id="product-btn" class="btn-primary centre" v-on:click="saveStoreKey">Save</button></div></th>
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
                storeKey: null
            };
        },

        methods: {
            saveStoreKey : function() {

                console.log(this.data);

                axios.post('/api/company/configuration', this.data)
                    .then(response => {
                        alert("Configuration saved");
                    })
                    .catch(error => {
                        alert("Something went wrong. Please try again");
                    });
            }
        }
    }

</script>
