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
                <button :disabled="!btnMaintenanceJobsEnabled" @click.prevent="runMaintenanceJobs">Run Maintenance Jobs</button>
                <button :disabled="!btnSyncEnabled" @click.prevent="runSync">Run Sync</button>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    name: "MaintenanceSection",
    data: function () {
        return {
            btnMaintenanceJobsEnabled: true,
            btnSyncEnabled: true,
        }
    },
    methods: {
        runMaintenanceJobs() {
            this.btnMaintenanceJobsEnabled = false;
            axios.get('/api/run/maintenance')
                .then(() => {
                        this.$snotify.success('Jobs scheduled');
                    }
                )
                .catch(() => {
                        this.$snotify.error('Request failed');
                    }
                );
        },
        runSync() {
            this.btnSyncEnabled = false;
            axios.get('/api/run/sync')
                .then(() => {
                        this.$snotify.success('Sync scheduled');
                    }
                )
                .catch(() => {
                        this.$snotify.error('Request failed');
                    }
                );
        }
    }
}
</script>

<style scoped>

</style>
