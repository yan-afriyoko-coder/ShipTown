<template>
    <div>
        <div class="row">
            <div class="col-sm-12 col-lg-5">
                <product-info-card :product= "record['product']"></product-info-card>
            </div>

            <div @click="toggleDetails" class="row col-sm-12 col-lg-7 text-right">
                <div class="col-12 col-md-4 text-left small">
                    <div :class="{ 'bg-warning':  Number(record['inventory']['quantity_available']) < 0}">in stock: <strong>{{ dashIfZero(Number(record['inventory']['quantity_available'])) }}</strong></div>
                    <div>last counted: <strong>{{ formatDateTime(record['inventory']['last_counted_at']) }}</strong></div>

                    <div>price: <strong>{{ Number(productPrice) }}</strong></div>
                </div>
                <div class="col-12 col-md-8 text-right">
                    <number-card label="points" :number="record['points']"></number-card>
                    <text-card label="shelf" :number="record['inventory']['shelf_location']"></text-card>
                </div>
            </div>
        </div>
        <div @click="toggleDetails" v-if="!showDetails" class="row text-center text-secondary">
            <div class="col">
                <font-awesome-icon icon="chevron-down" class="fa fa-xs"></font-awesome-icon>
            </div>
        </div>

        <template v-if="showDetails" @click="toggleDetails" >
            <div class="row text-center">
                <div class="col">
                    <font-awesome-icon icon="chevron-up" class="fa fa-xs text-secondary"></font-awesome-icon>
                </div>
            </div>
            <div class="row" v-for="detail in suggestionDetails">
                <div class="col">
                    {{ detail['points'] }} points - {{ detail['reason'] }}
                </div>
            </div>
        </template>
    </div>
</template>

<script>
import loadingOverlay from '../../mixins/loading-overlay';
import BarcodeInputField from "./../SharedComponents/BarcodeInputField";
import api from "../../mixins/api";
import helpers from "../../mixins/helpers";
import url from "../../mixins/url";

export default {
      name: 'SuggestionRecord',

      mixins: [loadingOverlay, url, api, helpers],

      components: {
          BarcodeInputField,
      },

      props: {
          record: {
              type: Object,
              required: true
          }
      },

      data: function() {
          return {
              showDetails: false,
              suggestionDetails: null,
          };
      },

      computed: {
          productPrice: function() {
            return this.record['product']['prices'][this.currentUser()['warehouse']['code']]['price'];
          }
      },


      methods: {
          toggleDetails() {
              this.showDetails = !this.showDetails;

              if (this.showDetails) {
                  this.loadSuggestionDetails();
              }
          },

          loadSuggestionDetails() {
              const params = {
                  'filter[inventory_id]': this.record['inventory_id'],
                  'include': 'product,inventory',
                  'sort': '-points,inventory_id',
                  'per_page': 999,
              }

              this.apiGetStocktakeSuggestionsDetails(params)
                  .then((response) => {
                      this.suggestionDetails = response.data.data;
                  })
                  .catch((error) => {
                      this.displayApiCallError(error);
                  });
          },
      },
  }
</script>

<style lang="scss" scoped>
</style>
