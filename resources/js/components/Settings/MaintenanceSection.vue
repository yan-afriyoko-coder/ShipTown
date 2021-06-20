<template>
    <div>

        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Maintenance
                    </span>
                </div>
            </div>

            <div class="card-body">
                <button :disabled="!btnRunHourlyJobsEnabled" @click.prevent="runHourlyJobs">Run Hourly Jobs</button>
                <button :disabled="!btnRunDailyJobsEnabled" @click.prevent="runDailyJobs">Run Daily Jobs</button>
                <button :disabled="!btnRunApi2cartSyncEnabled" @click.prevent="runApi2cartSync">Run Api2cart Sync</button>
                <button :disabled="!btnRunSyncEnabled" @click.prevent="runSync">Run Sync</button>
            </div>
        </div>

    </div>
</template>

<script>
import api from "../../mixins/api";

export default {
    mixins: [api],

    name: "MaintenanceSection",
    data: function () {
        return {
            btnRunHourlyJobsEnabled: true,
            btnRunDailyJobsEnabled: true,
            btnRunApi2cartSyncEnabled: true,
            btnRunSyncEnabled: true,
        }
    },
    methods: {
        runHourlyJobs() {
            this.btnRunHourlyJobsEnabled = false;
            this.apiGetRunHourlyJobs()
                .then(() => {
                        this.$snotify.success('Job run requested');
                    }
                )
                .catch(() => {
                        this.$snotify.error('Jub run request failed');
                    }
                );
        },

        runDailyJobs() {
            this.btnRunDailyJobsEnabled = false;
            this.apiGetRunDailyJobs()
                .then(() => {
                        this.$snotify.success('Job run requested');
                    }
                )
                .catch(() => {
                        this.$snotify.error('Jub run request failed');
                    }
                );
        },

        runApi2cartSync() {
            this.btnRunApi2cartSyncEnabled = false;
            this.apiGetRunSyncApi2cart()
                .then(() => {
                        this.$snotify.success('Api2cart Sync requested');
                    }
                )
                .catch(() => {
                        this.$snotify.error('Sync request failed');
                    }
                );
        },

        runSync() {
            this.btnRunSyncEnabled = false;
            this.apiGetRunSync()
                .then(() => {
                        this.$snotify.success('Sync requested');
                    }
                )
                .catch(() => {
                        this.$snotify.error('Sync request failed');
                    }
                );
        }
    }
}
</script>

<style scoped>

</style>
