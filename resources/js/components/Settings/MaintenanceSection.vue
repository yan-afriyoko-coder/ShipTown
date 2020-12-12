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
                <button :disabled="!btnRunSyncEnabled" @click.prevent="runSync">Run Sync</button>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    name: "MaintenanceSection",
    data: function () {
        return {
            btnRunHourlyJobsEnabled: true,
            btnRunDailyJobsEnabled: true,
            btnRunSyncEnabled: true,
        }
    },
    methods: {
        runHourlyJobs() {
            this.btnRunHourlyJobsEnabled = false;
            axios.get('/api/run/hourly/jobs')
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
            axios.get('/api/run/daily/jobs')
                .then(() => {
                        this.$snotify.success('Job run requested');
                    }
                )
                .catch(() => {
                        this.$snotify.error('Jub run request failed');
                    }
                );
        },
        runSync() {
            this.btnRunSyncEnabled = false;
            axios.get('/api/run/sync')
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
