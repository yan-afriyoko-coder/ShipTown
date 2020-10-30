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
export default {
    name: "AutoPilotTuningSection",

    data: function () {
        return {
            btnSaveAutoPilotTuningSettings: true,
            value: null,
            maxOrderAgeAllowed: null,
        }
    },

    created() {
        axios.get(`api/configuration/${process.env.MIX_PACKING_DAILY_MAX_CONFIG_KEY_NAME}`).then(({ data }) => {
            this.value = data.data.value;
        });
        axios.get(`api/configuration/max_order_age_allowed`).then(({ data }) => {
            this.maxOrderAgeAllowed = data.data.value;
        });
    },

    methods: {
        saveAutoPilotTuningSettings() {
            if (this.value) {
                this.btnSaveAutoPilotTuningSettings = ! this.btnSaveAutoPilotTuningSettings;
                axios.post(`api/configuration`, {
                    key: process.env.MIX_PACKING_DAILY_MAX_CONFIG_KEY_NAME,
                    value: this.value,
                });
                axios.post(`api/configuration`, {
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
