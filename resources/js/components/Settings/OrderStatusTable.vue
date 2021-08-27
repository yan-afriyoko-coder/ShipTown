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
                <table v-if="orderStatuses.length > 0" class="table table-borderless table-responsive mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Reserves Stock</th>
                            <th>Order Active</th>
                            <th>Sync Ecommerce</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(orderStatus, i) in orderStatuses" :key="i">
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
                            <td>
                                <a @click.prevent="showEditForm(orderStatus)">
                                    <font-awesome-icon icon="edit"></font-awesome-icon>
                                </a>
                                <a @click.prevent="confirmDelete(orderStatus)">
                                    <font-awesome-icon icon="archive"></font-awesome-icon>
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
        <create-modal id="createForm" @onCreated="addOrderStatus"></create-modal>
        <edit-modal :orderStatus="selectedOrderStatus" id="editForm" @onUpdated="updateOrderStatus"></edit-modal>
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
        confirmDelete(orderStatus) {
            const indexOrderStatuses = this.orderStatuses.findIndex(status => status.id == orderStatus.id)
            this.$snotify.confirm(`wants to archive ${orderStatus.name}`, 'Are you sure?', {
                position: 'centerCenter',
                buttons: [
                    {
                        text: 'Yes',
                        action: (toast) => {
                            this.delete(orderStatus.id, indexOrderStatuses)
                            this.$snotify.remove(toast.id);
                        }
                    },
                    {text: 'Cancel'},
                ]
            });
        },
        delete(id, index) {
            this.apiDeleteOrderStatus(id)
                .then(() => {
                    Vue.delete(this.orderStatuses, index);
                    this.$snotify.success('Automation archived.');
                })
                .catch(error => {
                    if (error.response) {
                        if (error.response.status === 401) {
                            this.$snotify.error(error.response.data.message);
                        }
                    }
                });
        }
    },
}
</script>
