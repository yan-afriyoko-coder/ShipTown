<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        PrintNode API Configuration
                    </span>
                </div>
            </div>
            <div class="card-body">
                <form @submit.prevent="handleSubmit" autocomplete="off">
                    <div class="form-group">
                        <label for="key">API Key</label>
                        <input v-model="value" type="text" class="form-control" id="key" />
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import api from "../../mixins/api";

export default {
    mixins: [api],

    created() {
        this.apiGetModulePrintnodeClients()
            .then(({ data }) => {
                this.value = data.data[0]['api_key'];
            });
    },

    data: () => ({
        value: null,
    }),

    methods: {
        handleSubmit() {
            if (this.value) {
                this.apiPostModulePrintnodeClients({
                    'api_key': this.value
                });
            }
        }
    },
}
</script>
