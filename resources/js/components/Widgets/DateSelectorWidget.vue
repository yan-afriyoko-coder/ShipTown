<template>
<div>
    <div class="text-right">
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-primary dropdown-toggle font-weight-bold" type="button" id="dropdownDateRange" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ formated_starting_date }} - {{ formated_ending_date }}
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownDateRange">
                <a class="dropdown-item" href="?between_dates=today,now">Today</a>
                <a class="dropdown-item" href="?between_dates=yesterday,today">Yesterday</a>
                <a class="dropdown-item" href="?between_dates=-7days,now">Last 7 days</a>
                <a class="dropdown-item" href="?between_dates=this week monday,now">This week</a>
                <a class="dropdown-item" href="?between_dates=last week monday,this week monday">Last Week</a>
                <a class="dropdown-item" @click.prevent="showModal">Custom Date</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
        <dialog id="modal-date-selector-widget" class="m-auto border-light rounded">
                <div class="text-secondary h5">Custom Date Filter</div>
                <form class="form" @submit.prevent="">
                    <div class="form-group">
                        <label class="form-label" for="starting_date">From</label>
                        <input class="form-control" id="starting_date" type="datetime-local" v-model="starting_date">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="ending_date">To</label>
                        <input class="form-control" id="ending_date" type="datetime-local" v-model="ending_date">
                    </div>
                </form>
                <div>
                    <button type="button" @click="closeModal" class="btn btn-default">Cancel</button>
                    <button type="button" @click="validateFilter" class="btn btn-primary">Apply</button>
                </div>
        </dialog>
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
            formated_starting_date: null,
            formated_ending_date: null,
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
            location.href = '?between_dates=' + range;
        },
        validateFilter(){
            if (this.starting_date > this.ending_date) {
                this.$snotify.warning('Invalid date filter.');
                return
            }
            this.applyFilter(this.dateRange)
        },

        showModal() {
            document.getElementById('modal-date-selector-widget').showModal()
        },

        closeModal() {
            document.getElementById('modal-date-selector-widget').close();
        }
    }
}
</script>

<style>

</style>
