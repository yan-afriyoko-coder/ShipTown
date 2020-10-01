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
                <div>Max Refill Batch Size (packing + packing_web)</div>
                <input ref="barcode" class="form-control" type="number" :placeholder="''"
                       v-model="value"/>

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
        }
    },

    created() {
        axios.get(`api/configuration/${process.env.MIX_PACKING_DAILY_MAX_CONFIG_KEY_NAME}`).then(({ data }) => {
            this.value = data.data.value;
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
            }
        },
    }
}
</script>

<style scoped>

</style>
