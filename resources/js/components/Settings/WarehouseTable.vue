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
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(warehouse, i) in warehouses" :key="i">
                            <td>{{ warehouse.id }}</td>
                            <td>{{ warehouse.code }}</td>
                            <td>{{ warehouse.name }}</td>
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
        <edit-modal :warehouse="selectedWarehouse" id="editForm" @onUpdated="updateWarehouse"></edit-modal>
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
        this.apiGetWarehouses()
            .then(({ data }) => {
                this.warehouses = data.data;
            })
    },

    data: () => ({
        warehouses: [],
        selectedWarehouse: {}
    }),

    methods: {
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
        updateWarehouse(newValue) {
            const indexWarehouse = this.warehouses.findIndex(warehouse => warehouse.id == newValue.id)
            this.$set(this.warehouses, indexWarehouse, newValue)
        },
        confirmDelete(warehouse) {
            const indexWarehouse = this.warehouses.findIndex(menu => warehouse.id == menu.id)
            this.$snotify.confirm('After delete data cannot restored', 'Are you sure?', {
                position: 'centerCenter',
                buttons: [
                    {
                        text: 'Yes',
                        action: (toast) => {
                            this.delete(warehouse.id, indexWarehouse)
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
