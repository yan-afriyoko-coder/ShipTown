<template>
    <div>
        <div class="list-group">
            <template v-for="module in modules" >
                <div class="setting-list">
                    <div class="setting-body">
                        <div class="setting-title">{{ module.name }}</div>
                        <div class="setting-desc">{{ module.description }}</div>
                    </div>
                    <template v-if="module.settings_link">
                            <div class="setting-icon flex-fill text-right bg-white">
                                <a :href="module.settings_link" class="btn-link">
                                    <font-awesome-icon icon="cog" class="fa-sm bg-white"></font-awesome-icon>
                                </a>
                            </div>
                    </template>
                    <template v-if="!module.settings_link">
                        <div class="flex-fill bg-white text-right"></div>
                    </template>
                    <div class="custom-control custom-switch m-auto text-right align-content-center">
                        <input type="checkbox" @change="updateModule(module)" class="custom-control-input" :id="module.id" v-model="module.enabled">
                        <label class="custom-control-label" :for="module.id"></label>
                    </div>
                </div>
            </template>
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
                }).finally(() => {
                    this.loadModules();
                });



        }
    }
}
</script>
