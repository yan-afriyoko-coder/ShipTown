<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Module Configuration
                    </span>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tbody>
                        <tr v-for="module in modules" :key="module.id">
                            <td>
                                <div><strong>{{ module.name }}</strong></div>
                                <div class="text-secondary">{{ module.description }}</div>
                            </td>
                            <td class="text-right align-middle">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" @change="updateModule(module)" class="custom-control-input" :id="module.id" v-model="module.enabled">
                                    <label class="custom-control-label" :for="module.id"></label>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
        this.loadModules();
    },

    data: () => ({
        error: false,
        modules: []
    }),

    methods: {
        loadModules() {
            this.apiGetModules()
                .then(({ data }) => {
                    this.modules = data.data;
                });
        },

        updateModule(module) {
            this.apiPostModule(module.id, {
                    'enabled': module.enabled
                })
                .catch((error) => {
                    let errorMsg = 'Error ' + error.response.status + ': ' + JSON.stringify(error.response.data);

                    this.notifyError(errorMsg, {
                        closeOnClick: true,
                        timeout: 0,
                        buttons: [
                            {text: 'OK', action: null},
                        ]
                    });
                    this.loadModules();
                });
        }
    }
}
</script>
