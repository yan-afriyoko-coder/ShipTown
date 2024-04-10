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
                <table v-if="warehouses.length > 0" class="table table-hover table-borderless table-responsive mb-0">
                    <thead>
                        <tr>
<!--                            <th>ID</th>-->
                            <th>Code</th>
                            <th>Name</th>
                            <th>Tags</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(warehouse, i) in warehouses" :key="i" @click.prevent="showEditForm(warehouse)">
<!--                            <td>{{ warehouse.id }}</td>-->
                            <td>{{ warehouse.code }}</td>
                            <td>{{ warehouse.name }}</td>
                            <td>
                                <template v-for="tag in warehouse.tags">
                                    <a class="badge text-uppercase" :key="tag.id"> {{ tag.name }} </a>
                                </template>
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
                'per_page': 100,
                'sort': 'code',
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
    },
}
</script>
