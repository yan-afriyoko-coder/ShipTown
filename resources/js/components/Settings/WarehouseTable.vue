<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Warehouse
                    </span>
                    <button type="button" class="action-link btn btn-sm btn-light" @click="showCreateForm()">
                        Add New
                    </button>
                </div>
            </div>

            <div class="card-body">
                <table v-if="warehouses.length > 0" class="table table-borderless table-responsive mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(warehouse, i) in warehouses" :key="i">
                            <td>{{ warehouse.id }}</td>
                            <td>{{ warehouse.code }}</td>
                            <td>{{ warehouse.name }}</td>
                            <td>

                                <template v-for="tag in warehouse.tags">
                                    <a class="badge text-uppercase" :key="tag.id"> {{ tag.name }} </a>
                                </template>

                            </td>
                            <td>
                                <a @click.prevent="showEditForm(warehouse)">
                                    <font-awesome-icon icon="edit"></font-awesome-icon>
                                </a>
                                <a @click.prevent="confirmDelete(warehouse)">
                                    <font-awesome-icon icon="trash"></font-awesome-icon>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p v-else class="mb-0">
                    No warehouses found...
                </p>
            </div>
        </div>
        <!-- The modals -->
        <create-modal id="createForm" @onCreated="addWarehouse"></create-modal>
        <edit-modal :warehouse="selectedWarehouse" id="editForm" @onUpdated="warehouseUpdatedEvent"></edit-modal>
    </div>
</template>

<script>

import CreateModal from './Warehouse/CreateModal';
import EditModal from './Warehouse/EditModal';
import api from "../../mixins/api.vue";

export default {
    mixins: [api],
    components: {
        'create-modal': CreateModal,
        'edit-modal': EditModal,
    },

    mounted() {
        this.fetchWarehouses();
    },

    data: () => ({
        warehouses: [],
        selectedWarehouse: {}
    }),

    methods: {
        fetchWarehouses: function () {
            this.apiGetWarehouses({
                'include': 'tags'
            })
                .then(({data}) => {
                    this.warehouses = data.data;
                })
        },

        showCreateForm(){
            $('#createForm').modal('show');
        },

        showEditForm(warehouse) {
            this.selectedWarehouse = warehouse;
            $('#editForm').modal('show');
        },

        addWarehouse(orderStatus){
            this.warehouses.push(orderStatus)
        },

        warehouseUpdatedEvent(newValue) {
            this.fetchWarehouses();
        },

        confirmDelete(selectedWarehouse) {
            const indexWarehouse = this.warehouses.findIndex(warehouse => warehouse.id === selectedWarehouse.id)
            this.$snotify.confirm('After delete data cannot restored', 'Are you sure?', {
                position: 'centerCenter',
                buttons: [
                    {
                        text: 'Yes',
                        action: (toast) => {
                            this.delete(selectedWarehouse.id, indexWarehouse)
                            this.$snotify.remove(toast.id);
                        }
                    },
                    {text: 'Cancel'},
                ]
            });
        },

        delete(id, index) {
            this.apiDeleteWarehouses(id)
                .then(() => {
                    Vue.delete(this.warehouses, index);
                    this.$snotify.success('Warehouse deleted.');
                });
        }
    },
}
</script>
