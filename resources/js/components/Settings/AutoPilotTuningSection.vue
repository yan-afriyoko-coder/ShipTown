<template>
    <div>

        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        AutoPilot Tuning
                    </span>
                </div>
            </div>

            <div class="card-body">
                <div>Max Batch Size (picking)</div>
                <input class="form-control" type="number" :placeholder="''" v-model="value"/>
                <div>Max Order Age Allowed</div>
                <input class="form-control" type="number" :placeholder="''" v-model="maxOrderAgeAllowed"/>

                <button :disabled="!btnSaveAutoPilotTuningSettings" @click.prevent="saveAutoPilotTuningSettings">Save</button>
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
            value: null,
            maxOrderAgeAllowed: null,
        }
    },

    created() {
        this.apiGetConfigurationAutopilot('packing_daily_max')
            .then(({ data }) => {
                this.value = data.data.value;
            });
        this.apiGetConfigurationAutopilot(`max_order_age_allowed`)
            .then(({ data }) => {
                this.maxOrderAgeAllowed = data.data.value;
            });
    },

    methods: {

        saveAutoPilotTuningSettings() {
            if (this.value) {
                this.btnSaveAutoPilotTuningSettings = ! this.btnSaveAutoPilotTuningSettings;
                this.apiPostAutoPilotConfiguration({
                    key: 'packing_daily_max',
                    value: this.value,
                });
                this.apiPostAutoPilotConfiguration({
                    key: 'max_order_age_allowed',
                    value: this.maxOrderAgeAllowed,
                });
            }
        },
    }
}
</script>

<style scoped>

</style>
