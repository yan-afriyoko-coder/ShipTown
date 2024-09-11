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

            <div class="card-body" v-if="client">
                <form @submit.prevent="disconnectClient" autocomplete="off">
                    <button type="submit" class="btn btn-primary">Disconnect</button>
                </form>
            </div>


            <div class="card-body" v-else>
                <form @submit.prevent="handleSubmit" autocomplete="off">
                    <div class="form-group">
                        <label for="key">API Key</label>
                        <input v-model="apiKey" type="text" class="form-control" id="key" />
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>

        </div>
    </div>
</template>

<script>
import api from "../../mixins/api";
import helpers from "../../mixins/helpers";

export default {
    mixins: [api, helpers],

    created() {
        this.apiGetModulePrintNodeClients()
            .then(({ data }) => {
                this.client = data.data[0];
            });
    },

    data: () => ({
        apiKey: null,
        client: null,
    }),

    methods: {
        disconnectClient() {
            this.apiDeletePrintNodeClient(this.client.id)
                .then(() => {
                    this.client = null;
                })
                .catch(() => {
                    this.notifyError('Error occurred while disconnecting client');
                })
        },

        handleSubmit() {
            if (!this.apiKey) {
                return;
            }

            this.apiPostModulePrintNodeClients({
                    'api_key': this.apiKey
                })
                .then((data) => {
                    this.client = data.data.data;
                    this.apiKey = null;
                })
                .catch((error) => {
                    this.notifyError(error.response.data.message);
                });

        }
    },
}
</script>
