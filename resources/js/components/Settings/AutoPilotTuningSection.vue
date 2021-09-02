<template>
    <div>

        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        AutoStatus "picking"
                    </span>
                </div>
            </div>

            <div class="card-body" v-if="configuration !== null">
                <div class="form-group">
                    <label>Max Batch Size (picking)</label>
                    <input class="form-control" type="number" :placeholder="''" v-model="configuration['max_batch_size']"/>
                </div>
                <div class="form-group">
                    <label>Max Order Age Allowed</label>
                    <input class="form-control" type="number" :placeholder="''" v-model="configuration['max_order_age']"/>
                </div>

                <button class="btn btn-primary" :disabled="!btnSaveAutoPilotTuningSettings" @click.prevent="saveAutoPilotTuningSettings">Save</button>
            </div>
        </div>

    </div>
</template>

<script>
import api from "../../mixins/api";

export default {
    mixins: [api],

    name: "AutoPilotTuningSection",

    data: function () {
        return {
            btnSaveAutoPilotTuningSettings: true,
            configuration: null,
        }
    },

    created() {
        this.apiGetModuleAutoStatusPickingConfiguration()
            .then(({ data }) => {
                this.configuration = data.data;
            });
    },

    methods: {
        saveAutoPilotTuningSettings() {
            this.apiSetModuleAutoStatusPickingConfiguration(this.configuration);
            this.btnSaveAutoPilotTuningSettings = ! this.btnSaveAutoPilotTuningSettings;
        },
    }
}
</script>

<style scoped>

</style>
