<template>
    <div>
        <div class="row mb-2">
            <div class="col-10">
                <input ref="search" @focus="handleSearchFocus" class="form-control" @keyup.enter="handleSearchEnter" v-model="query" placeholder="Scan picked product" />
            </div>
            <div class="col">
                <input class="form-control" placeholder="A12" />
            </div>
        </div>
        <div class="container">
            <div v-if="total == 0 && !isLoading" class="row" >
                <div class="col">
                    <div class="alert alert-info" role="alert">
                        No products found.
                    </div>
                </div>
            </div>
            <template v-else class="row">
                <template v-for="picklistItem in picklist">
                    <picklist-item :product="picklistItem.product" :picklistItem="picklistItem" :key="picklistItem.id" @transitionEnd="pick" />
                </template>
            </template>
        </div>
    </div>
</template>

<script>
    import loadingOverlay from '../mixins/loading-overlay';
    import PicklistItem from './PicklistItem';

    export default {
        mixins: [loadingOverlay],

        components: { 'picklist-item': PicklistItem },

        created() {
            this.loadProductList(this.page);
        },

        mounted() {
            this.$refs.search.focus();
            this.scroll();
        },

        methods: {
            loadProductList: function(page) {
                return new Promise((resolve, reject) => {
                    this.showLoading();
                    axios.get('/api/picklist', {
                        params: {
                            page: page,
                            q: this.query,
                            sort: this.sort,
                            order: this.order,
                        }
                    }).then(({ data }) => {
                        this.picklist = this.picklist.concat(data.data);
                        this.total = data.total;
                        this.last_page = data.last_page;
                        resolve(data);
                    })
                    .catch(reject)
                    .then(() => {
                        this.hideLoading();
                    });
                });
            },

            handleSearchEnter(e) {
                this.picklist = [];
                this.page = 1;
                this.last_page = 1;
                this.total = 0;
                this.loadProductList(1).then(this.handleSearchFocus);
            },

            handleSearchFocus() {
                if (this.query) {
                    setTimeout(() => { document.execCommand('selectall', null, false); });
                }
            },

            scroll (person) {
                window.onscroll = () => {
                    let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;

                    if (bottomOfWindow && this.last_page > this.page) {
                        this.loadProductList(++this.page);
                    }
                };
            },

            pick({ id, quantity }) {
                axios.post(`/api/picklist/${id}`, { quantity }).then(({ data }) => {
                    this.$snotify.success(`${quantity} items picked.`);
                });
            }
        },

        data: function() {
            return {
                query: null,
                sort: 'sku',
                order: 'asc',
                picklist: [],
                total: 0,
                page: 1,
                last_page: 1,
            };
        },
    }
</script>
