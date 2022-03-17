<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Order Statuses
                    </span>
                    <button type="button" class="action-link btn btn-sm btn-light" @click="showCreateForm()">
                        Add New
                    </button>
                </div>
            </div>

            <div class="card-body">
                <table v-if="orderStatuses.length > 0" class="table table-hover table-borderless table-responsive mb-0">
                    <thead>
                        <tr class="small">
                            <th>id</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Reserves Stock</th>
                            <th>Order Active</th>
                            <th>Sync Ecommerce</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(orderStatus, i) in orderStatuses" :key="i" @click.prevent="showEditForm(orderStatus)">
                            <td>{{ orderStatus.id }}</td>
                            <td>{{ orderStatus.name }}</td>
                            <td>{{ orderStatus.code }}</td>
                            <td align="center">
                                <status-icon :status="orderStatus.reserves_stock" />
                            </td>
                            <td align="center">
                                <status-icon :status="orderStatus.order_active" />
                            </td>
                            <td align="center">
                                <status-icon :status="orderStatus.sync_ecommerce" />
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p v-else class="mb-0">
                    No order statuses found.
                </p>
            </div>
        </div>
        <!-- The modals -->
        <create-modal id="createForm" @onCreated="addOrderStatus"></create-modal>
        <edit-modal :orderStatus="selectedOrderStatus" id="editForm" @onUpdated="updateOrderStatus" @onDeleted="removeOrderStatus"></edit-modal>
    </div>
</template>

<script>

import CreateModal from './OrderStatus/CreateModal';
import EditModal from './OrderStatus/EditModal';
import StatusIcon from './OrderStatus/StatusIcon';
import api from "../../mixins/api.vue";

export default {
    mixins: [api],
    components: {
        'create-modal': CreateModal,
        'edit-modal': EditModal,
        'status-icon': StatusIcon
    },

    mounted() {
        this.apiGetOrderStatus()
            .then(({ data }) => {
                this.orderStatuses = data.data;
            })
    },

    data: () => ({
        orderStatuses: [],
        selectedOrderStatus: {}
    }),

    methods: {
        showCreateForm(){
            $('#createForm').modal('show');
        },
        showEditForm(orderStatus) {
            this.selectedOrderStatus = orderStatus;
            $('#editForm').modal('show');
        },
        updateOrderStatus(newValue) {
            const indexOrderStatus = this.orderStatuses.findIndex(orderStatus => orderStatus.id == newValue.id)
            this.$set(this.orderStatuses, indexOrderStatus, newValue)
        },
        addOrderStatus(orderStatus){
            this.orderStatuses.push(orderStatus)
        },
        removeOrderStatus(id){
            const indexOrderStatuses = this.orderStatuses.findIndex(status => status.id == id)
            Vue.delete(this.orderStatuses, indexOrderStatuses);
        },
    },
}
</script>
