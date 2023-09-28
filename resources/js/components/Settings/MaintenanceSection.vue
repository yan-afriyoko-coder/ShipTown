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
                <button :disabled="buttonDisabled['EveryDay']" @click.prevent="runSchedule('EveryDay')" class="btn btn-block btn-primary mb-2">
                    Run Every Day Jobs
                </button>
                <button :disabled="buttonDisabled['EveryHour']" @click.prevent="runSchedule('EveryHour')" class="btn btn-block btn-primary mb-2">
                    Run Every Hour Jobs
                </button>
                <button :disabled="buttonDisabled['EveryTenMinutes']" @click.prevent="runSchedule('EveryTenMinutes')" class="btn btn-block btn-primary mb-2">
                    Run Every 10 Minutes Jobs
                </button>
                <button :disabled="buttonDisabled['EveryFiveMinutes']" @click.prevent="runSchedule('EveryFiveMinutes')" class="btn btn-block btn-primary mb-2">
                    Run Every 5 Minutes Jobs
                </button>
                <button :disabled="buttonDisabled['EveryOneMinute']" @click.prevent="runSchedule('EveryOneMinute')" class="btn btn-block btn-primary mb-2">
                    Run Every 1 Minute Jobs
                </button>
                <button :disabled="buttonDisabled['ManualRequest']" @click.prevent="runSchedule('ManualRequest')" class="btn btn-block btn-primary mb-2">
                    Run Manual Request Jobs
                </button>
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
            buttonDisabled: {},
        }
    },
    methods: {
        runSchedule(schedule) {
            this.apiPostRunScheduledJobsRequest({"schedule": schedule})
                .then(() => {
                        this.$snotify.success('Cron run requested');
                        this.buttonDisabled[schedule] = true;
                    }
                )
                .catch(() => {
                        this.$snotify.error('Cron run request failed');
                    }
                );
        },
    }
}
</script>

<style scoped>

</style>
