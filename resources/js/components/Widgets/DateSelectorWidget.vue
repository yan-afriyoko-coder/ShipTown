<template>
<div>
    <div class="text-right">
        from <b>{{ formated_starting_date }}</b>
        to <b>{{ formated_ending_date }}</b>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="dropdownDateRange" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Date Filter
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownDateRange">
                <a class="dropdown-item" href="?today,now">Today</a>
                <a class="dropdown-item" href="?yesterday,today">Yesterday</a>
                <a class="dropdown-item" href="?-7days,now">Last 7 days</a>
                <a class="dropdown-item" href="?this week monday,now">This week</a>
                <a class="dropdown-item" href="?last week monday,this week monday">Last Week</a>
                <a class="dropdown-item" href="#" @click="showModal">Custom Date</a>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modal-date-selector-widget">
        <div class="modal-dialog modal-dialog-centered">
            <div ref="loadingContainer2" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Custom Date Filter</h5>
                </div>
                <div class="modal-body">
                    <form class="form" @submit.prevent="submit">
                        <div class="form-group">
                            <label class="form-label" for="starting_date">From</label>
                            <input class="form-control" id="starting_date" type="datetime-local" v-model="starting_date">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="ending_date">To</label>
                            <input class="form-control" id="ending_date" type="datetime-local" v-model="ending_date">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="closeModal" class="btn btn-default">Cancel</button>
                    <button type="button" @click="validateFilter" class="btn btn-primary">Apply</button>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import moment from "moment"
export default {
    props: ['dates'],
    data(){
        return {
            starting_date: null,
            ending_date: null,
            between_dates: null
        }
    },
    mounted(){
        this.formated_starting_date = moment(this.dates.starting_date).format('YYYY-MM-DD hh:mm')
        this.formated_ending_date = moment(this.dates.ending_date).format('YYYY-MM-DD hh:mm')
        this.starting_date = moment(this.dates.starting_date).format('YYYY-MM-DD\Thh:mm')
        this.ending_date = moment(this.dates.ending_date).format('YYYY-MM-DD\Thh:mm')
        this.between_dates = this.dates.between_dates
    },
    computed: {
        dateRange(){
            return this.starting_date + "," + this.ending_date
        }
    },
    methods: {
        applyFilter(range){
            location.href = '?between_dates='+range;
        },
        validateFilter(){
            if (this.starting_date > this.ending_date) {
                this.$snotify.warning('Invalid date filter.');
                return
            }
            this.applyFilter(this.dateRange)
        },

        showModal() {
            $('#modal-date-selector-widget').modal('show');
        },

        closeModal() {
            $('#modal-date-selector-widget').modal('hide');
        }
    }
}
</script>

<style>

</style>
