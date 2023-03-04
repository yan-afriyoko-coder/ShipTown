<template>
    <div>
        <div class="row">
            <div class="col-sm-12 col-md-5">
                <product-info-card :product= "record['product']"></product-info-card>
            </div>

            <div class="col-sm-12 col-md-3 text-left small mt-2 mb-1">
                <div class="row">
                    <div class="col">
                        {{ record['suggestion_details'] }}
                    </div>
                </div>
                <div @click="toggleDetails"  :class="{ 'bg-warning':  Number(record['inventory']['quantity_available']) < 0}">in stock: <strong>{{ dashIfZero(Number(record['inventory']['quantity_available'])) }}</strong></div>
                <div @click="toggleDetails" >price: <strong>{{ Number(productPrice) }}</strong></div>
                <div @click="toggleDetails" >last counted at: <strong>{{ formatDateTime(record['inventory']['last_counted_at']) }}</strong></div>
                <div @click="toggleDetails" >last sold at: <strong>{{ formatDateTime(record['inventory']['last_sold_at']) }}</strong></div>
                <div>
                    <div @click="toggleDetails" class="d-inline">last movement at:</div>
                    <strong @click="showModalMovement(record['product_sku'])" class="text-primary cursor-pointer">{{ formatDateTime(record['inventory']['last_movement_at']) }}</strong>
                </div>

                <template v-if="expanded">
                    <div class="row mb-3" @click="toggleDetails" >
                        <div class="col-12" @click="toggleDetails" >last received at: <strong>{{ formatDateTime(record['inventory']['last_received_at']) }}</strong></div>
                        <div class="col-12" @click="toggleDetails" >first received at: <strong>{{ formatDateTime(record['inventory']['first_received_at']) }}</strong></div>
                    </div>
                </template>

            </div>

            <div @click="toggleDetails" class="col-sm-12 col-md-4 text-right">
                <div class="row">
                    <div class="col">
                        <text-card label="location" :text="record['inventory']['warehouse_code']"></text-card>
                        <number-card label="points" :number="record['points']"></number-card>
                        <text-card label="shelf" :text="record['inventory']['shelf_location']"></text-card>
                    </div>
                </div>

                <div class="row">
                    <div class="col text-center">
                        <font-awesome-icon v-if="!expanded" icon="chevron-down" class="fa fa-xs"></font-awesome-icon>
                        <font-awesome-icon v-if="expanded" icon="chevron-up" class="fa fa-xs text-secondary"></font-awesome-icon>
                    </div>
                </div>

            </div>
        </div>

        <modal-inventory-movement :product_sku="showMovementSku" />

    </div>
</template>

<script>
import loadingOverlay from '../../mixins/loading-overlay';
import BarcodeInputField from "./../SharedComponents/BarcodeInputField";
import api from "../../mixins/api";
import helpers from "../../mixins/helpers";
import url from "../../mixins/url";
import ModalInventoryMovement from '../SharedComponents/ModalInventoryMovement';

export default {
      name: 'SuggestionRecord',

      mixins: [loadingOverlay, url, api, helpers],

      components: {
          BarcodeInputField,
          ModalInventoryMovement
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
              showMovementSku: null
          };
      },

      computed: {
          productPrice: function() {
            return this.record['product']['prices'][this.record['inventory']['warehouse_code']]['price'];
          }
      },


      methods: {
          toggleDetails() {
              this.expanded = !this.expanded;
          },


          showModalMovement(product_sku) {
            this.showMovementSku = product_sku
            this.$bvModal.show('show-inventory-movements')
          }
      },
  }
</script>

<style lang="scss" scoped>
</style>
