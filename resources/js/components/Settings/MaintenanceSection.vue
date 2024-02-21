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
                <button :disabled="buttonDisabled['EveryDay']" @click.prevent="runJobs('EveryDay')" class="btn btn-block btn-primary mb-2">
                    Run Every Day Jobs
                </button>
                <button :disabled="buttonDisabled['EveryHour']" @click.prevent="runJobs('EveryHour')" class="btn btn-block btn-primary mb-2">
                    Run Every Hour Jobs
                </button>
                <button :disabled="buttonDisabled['EveryTenMinutes']" @click.prevent="runJobs('EveryTenMinutes')" class="btn btn-block btn-primary mb-2">
                    Run Every 10 Minutes Jobs
                </button>
                <button :disabled="buttonDisabled['EveryFiveMinutes']" @click.prevent="runJobs('EveryFiveMinutes')" class="btn btn-block btn-primary mb-2">
                    Run Every 5 Minutes Jobs
                </button>
                <button :disabled="buttonDisabled['EveryMinute']" @click.prevent="runJobs('EveryMinute')" class="btn btn-block btn-primary mb-2">
                    Run Every 1 Minute Jobs
                </button>
                <button :disabled="buttonDisabled['SyncRequest']" @click.prevent="runJobs('SyncRequest')" class="btn btn-block btn-primary mb-2">
                    Run Manual Request Jobs
                </button>
            </div>
        </div>



        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Maintenance
                    </span>
                </div>
            </div>

            <div class="card-body">

                <template v-for="job in jobs">
                    <button :disabled="buttonDisabled[job['job_class']]" @click.prevent="runJobs(job)" class="btn btn-block btn-primary mb-2 small">
                        {{ job['job_name'] }}
                    </button>
                </template>
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
            jobs: {},
        }
    },

    mounted() {
        this.fetchJobs();
    },

    methods: {
        fetchJobs() {
            this.apiGetJobsRequest()
                .then((response) => {
                    this.jobs = response.data.data;
                })
                .catch((error) => {
                    this.displayApiCallError(error);
                });
        },

        runJobs(job) {
            this.apiPostJobsRequest({"job_id": job['id']})
                .then(() => {
                        this.$snotify.success('Job run requested');
                        this.buttonDisabled[job['job_class']] = true;
                    }
                )
                .catch((error) => {
                        this.displayApiCallError(error);
                    }
                );
        },
    }
}
</script>

<style scoped>

</style>
