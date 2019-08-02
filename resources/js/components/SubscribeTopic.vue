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
                                        <input type="text" class="form-control" id="URL" placeholder="Enter the URL">
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th><div class="text-center"><button id="product-btn" class="btn-primary centre" v-on:click="todo()">Subscribe</button></div></th>

                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

</script>
