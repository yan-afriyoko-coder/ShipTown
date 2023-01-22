<template>
    <div>
        <div class="row">
            <div class="col-sm-12 col-md-5">
                <product-info-card :product= "record['product']"></product-info-card>
            </div>

            <div class="col-sm-12 col-md-3 text-left small">
                <div @click="toggleDetails"  :class="{ 'bg-warning':  Number(record['inventory']['quantity_available']) < 0}">in stock: <strong>{{ dashIfZero(Number(record['inventory']['quantity_available'])) }}</strong></div>
                <div @click="toggleDetails" >price: <strong>{{ Number(productPrice) }}</strong></div>
                <div>last movement at: <strong><a :href="productItemMovementLink" target="_blank">{{ formatDateTime(record['inventory']['last_movement_at']) }}</a></strong></div>
                <div @click="toggleDetails" >last counted at: <strong>{{ formatDateTime(record['inventory']['last_counted_at']) }}</strong></div>
            </div>
            <div @click="toggleDetails" class="col-sm-12 col-md-4 text-right">
                    <number-card label="points" :number="record['points']"></number-card>
                    <text-card label="shelf" :number="record['inventory']['shelf_location']"></text-card>
            </div>
        </div>

        <div @click="toggleDetails" class="row text-center text-secondary">
            <div class="col">
                <font-awesome-icon v-if="!expanded" icon="chevron-down" class="fa fa-xs"></font-awesome-icon>
                <font-awesome-icon v-if="expanded" icon="chevron-up" class="fa fa-xs text-secondary"></font-awesome-icon>
            </div>
        </div>

       <template v-if="expanded" @click="toggleDetails" >
            <div class="row small" v-for="detail in suggestionDetails">
                <div class="col-12 mb-3">
                    <div @click="toggleDetails" >last received at: <strong>{{ formatDateTime(record['inventory']['last_received_at']) }}</strong></div>
                    <div @click="toggleDetails" >first received at: <strong>{{ formatDateTime(record['inventory']['first_received_at']) }}</strong></div>
                </div>
                <div class="col-12">
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
              expanded: false,
              suggestionDetails: null,
          };
      },

      computed: {
          productItemMovementLink() {
              return '/reports/inventory-movements?hide_nav_bar=true&filter[search]=' + this.record['product']['sku'];
          },

          productPrice: function() {
            return this.record['product']['prices'][this.currentUser()['warehouse']['code']]['price'];
          }
      },


      methods: {
          toggleDetails() {
              this.expanded = !this.expanded;

              if (this.expanded) {
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
