<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Order Statuses
                    </span>
                    <a tabindex="-1" class="action-link" v-b-modal.invite-modal>
                        Add new
                    </a>
                </div>
            </div>

            <div class="card-body">
                <table v-if="orderStatuses.length > 0" class="table table-borderless table-responsive mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Reserves Stock</th>
                            <th>Order Active</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(orderStatus, i) in orderStatuses" :key="i">
                            <td>{{ orderStatus.id }}</td>
                            <td>{{ orderStatus.name }}</td>
                            <td>{{ orderStatus.code }}</td>
                            <td align="center">
                                <font-awesome-icon
                                    :icon="orderStatus.reserves_stock ? 'check-circle' : 'times-circle'"
                                    :class="orderStatus.reserves_stock ? 'text-success' : 'text-danger'"
                                >
                                </font-awesome-icon>
                            </td>
                            <td align="center">
                                <font-awesome-icon
                                    :icon="orderStatus.order_active ? 'check-circle' : 'times-circle'"
                                    :class="orderStatus.order_active ? 'text-success' : 'text-danger'"
                                >
                                </font-awesome-icon>
                            </td>
                            <td>
                                <a @click.prevent="onEditClick(orderStatus)">
                                    <font-awesome-icon icon="edit"></font-awesome-icon>
                                </a>
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
        <!-- <b-modal id="invite-modal" title="Invite User" @ok="handleInviteOk">
            <create-modal ref="inviteForm"></create-modal>
        </b-modal> -->
        <edit-modal :orderStatus="selectedOrderStatus" id="editForm" @onUpdated="updateOrderStatus"></edit-modal>
    </div>
</template>

<script>
import { find } from 'lodash';

// import Invite from './Users/Invite';
import EditModal from './OrderStatus/EditModal';
import api from "../../mixins/api.vue";

export default {
    mixins: [api],
    components: {
        // 'invite-modal': Invite,
        'edit-modal': EditModal,
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
        onEditClick(orderStatus) {
            this.selectedOrderStatus = orderStatus;
            $('#editForm').modal('show');
        },
        updateOrderStatus(newValue) {
            const indexOrderStatus = this.orderStatuses.findIndex(orderStatus => orderStatus.id == newValue.id)
            this.$set(this.orderStatuses, indexOrderStatus, newValue)
        },
        addOrderStatus(orderStatus){
            this.orderStatuses.push(orderStatus)
        }
    },
}
</script>
