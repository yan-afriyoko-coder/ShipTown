<template>
    <div>
        <div>
            <div class="card card-default">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>
                            SNS Topics
                        </span>
                    </div>
                </div>

                <div class="card-body">

                    <table class="table table-borderless mb-0">
                        <thead>
                            <tr>
                                <th>Products Topic</th>
                                <th><button id="product-btn" class="btn-primary" v-on:click="createTopic('Products')">Create</button></th>
                            </tr>
                            <tr>
                                <th>Orders Topic</th>
                                <th><button id="orders-btn" class="btn-primary" v-on:click="createTopic('Orders')">Create</button></th>
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

            createTopic : function(name) {

                axios.post('/api/createtopic', name)

                console.log(name);
            }
        }


    }





</script>
