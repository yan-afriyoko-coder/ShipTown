<template>
    <div>
        <div>
            <div class="card card-default">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>
                            Subscribe To Topic
                        </span>
                    </div>
                </div>

                <div class="card-body">

                    <table class="table table-borderless mb-0">
                        <thead>
                            <tr>
                                <th>
                                    <div class="form-group">
                                        <label for="URL">URL</label>
                                        <input type="text" class="form-control" id="URL" v-model="url" placeholder="Enter the URL">
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th><div class="text-center"><button id="product-btn" class="btn-primary centre" v-on:click="subscribe(url)">Subscribe</button></div></th>
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

            subscribe : function(url) {

                axios.post('/api/subscribetopic', url)
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
