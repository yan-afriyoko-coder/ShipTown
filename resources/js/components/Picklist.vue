<template>
    <div>
        <div v-if="showScanner" class="overlay" @click.prevent="stopScanner">
            <div id="interactive" class="viewport overlay-content"></div>
        </div>
        <div class="row mb-3 ml-1 mr-1">
            <div class="col-12">
                <input ref="search" @focus="handleSearchFocus" class="form-control" @keyup.enter="handleSearchEnter" v-model="query" placeholder="Scan current shelf location" />
            </div>
<!--            <div class="col">-->
<!--                <button type="button" class="btn btn-secondary" @click.prevent="initScanner" href="#"><font-awesome-icon icon="barcode"></font-awesome-icon></button>-->
<!--            </div>-->
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
                    <picklist-item :picklistItem="picklistItem" :key="picklistItem.id" @transitionEnd="pick" />
                </template>
            </template>
        </div>
    </div>
</template>

<script>
    import Quagga from 'quagga';

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

            pick({ id, quantity_picked, shelve_location }) {
                axios.post(`/api/picklist/${id}`, { quantity_picked }).then(({ data }) => {
                    this.$snotify.success(`${quantity_picked} items picked.`);
                    this.query = shelve_location;
                });
            },

            initScanner(e) {
                this.showScanner = true;
                this.$nextTick(() => {
                    Quagga.init({
                        decoder : {
                            readers : [
                                "code_39_reader",
                                "ean_reader",
                                "code_128_reader",
                            ],
                            debug: {
                                drawBoundingBox: false,
                                showFrequency: false,
                                drawScanline: false,
                                showPattern: false
                            }
                        },
                        inputStream: {
                            name: "Live",
                            type: "LiveStream",
                            area: { // defines rectangle of the detection/localization area
                                top: "0%",    // top offset
                                right: "0%",  // right offset
                                left: "0%",   // left offset
                                bottom: "0%"  // bottom offset
                            },
                            singleChannel: false // true: only the red color-channel is read
                        }
                    }, function(err) {
                        if (err) {
                            console.log(err);
                            //return
                        }
                        console.log("Initialization finished. Ready to start");
                        Quagga.start();
                    });


                    Quagga.onDetected((data) => {
                        this.query = data.codeResult.code;
                        this.loadProductList(1).then(this.handleSearchFocus);
                        this.stopScanner();
                    })
                });
            },

            stopScanner() {
                this.showScanner = false;
                Quagga.stop();
            }
        },

        data: function() {
            return {
                query: 'A0',
                sort: 'sku',
                order: 'asc',
                picklist: [],
                total: 0,
                page: 1,
                last_page: 1,
                showScanner: false,
            };
        },
    }
</script>

<style>
.overlay {
  height: 100%;
  width: 100%;
  position: fixed; /* Stay in place */
  z-index: 2; /* Sit on top */
  left: 0;
  top: 0;
  background-color: rgb(0,0,0); /* Black fallback color */
  background-color: rgba(0,0,0, 0.9); /* Black w/opacity */
  overflow-x: hidden; /* Disable horizontal scroll */
  transition: 0.5s; /* 0.5 second transition effect to slide in or slide down the overlay (height or width, depending on reveal) */
}

/* Position the content inside the overlay */
#interactive video {
  position: fixed;
  top: 10%; /* 25% from the top */
  width: 100%; /* 100% width */
  margin-top: 30px; /* 30px top margin to avoid conflict with the close button on smaller screens */
}

/* When the height of the screen is less than 450 pixels, change the font-size of the links and position the close button again, so they don't overlap */
@media screen and (max-height: 450px) {
  .overlay a {font-size: 20px}
  .overlay .closebtn {
    font-size: 40px;
    top: 15px;
    right: 35px;
  }
}
</style>
