<template>
    <div>
        <div>
            <div class="card card-default">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>
                            Configuration
                        </span>
                    </div>
                </div>

                <div class="card-body">

                    <table class="table table-borderless mb-0">
                        <thead>
                            <tr>
                                <th>
                                    <div class="form-group">
                                        <label for="jsonConfig">Configurtion</label>
                                        <textarea type="textarea" class="form-control" id="jsonConfig" v-model="jsonConfig" placeholder="Json configuration here"></textarea>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th><div class="text-center"><button id="product-btn" class="btn-primary centre" v-on:click="saveConfig(jsonConfig)">Save</button></div></th>
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
        methods: {

            saveConfig : function(jsonConfig) {

                axios.post('/user/configuration', jsonConfig)
                    .then(response => {
                        if (!(response.status == 200)) {
                            alert("Subscribed to topic");
                        }
                        else {
                            alert(response.data)
                        }
                    })
            }
        }
    }

</script>
