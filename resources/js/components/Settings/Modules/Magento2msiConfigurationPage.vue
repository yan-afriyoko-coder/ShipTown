<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;" class="btn" @click="show['create_new_connection_tab'] = ! show['create_new_connection_tab']">
                    <span>
                        New Connection
                    </span>
                    <span class="btn-outline-light text-dark font-weight-bold small">
                        <span v-if="! show['create_new_connection_tab']"><</span>
                    </span>
                </div>
            </div>

            <div class="card-body" v-if="show['create_new_connection_tab']">
                <form class="form" @submit.prevent="submit" ref="loadingContainer">
                    <div class="form-group">
                        <label class="form-label" for="base_url">Base URL</label>
                        <input v-model="newConnection.base_url" class="form-control" id="create-base_url" type="url" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="api_access_token">Access Token</label>
                        <input v-model="newConnection.api_access_token" class="form-control" id="api_access_token" required>
                    </div>

                    <div class="form-group">
                        <button class="btn btn-primary fa-pull-right" @click.prevent="submit">Connect</button>
                    </div>
                </form>
            </div>
        </div>

        <table style="width: 100%" class="table table-fit table-hover table-borderless table-responsive mb-0 rounded">
            <thead>
                <tr class="small table-active w-auto">
                    <th class="">URL</th>
                    <th class="w-100"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="(!connections) || connections.length === 0">
                    <td colspan="2">
                        You have not created any Magento Api connections.
                    </td>
                </tr>
                <tr v-for="connection in connections" :key="connection.id" @click.prevent="">
                    <td class="w-auto">{{ connection.base_url }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <button class="btn btn-primary" @click.prevent="runJob('MODULE_Magento2msi_FetchStockItemsJob')">MODULE_Magento2msi_FetchStockItemsJob</button>

<!--        <create-modal @onCreated="reloadConnections"></create-modal>-->
<!--        <edit-modal :connection="selectedConnection" id="editForm" @onUpdated="conncetionUpdatedEvent"></edit-modal>-->
    </div>
</template>

<script>

import CreateModal from "../Automations/CreateModal";
import EditModal from "../Automations/EditModal";
import api from "../../../mixins/api";

export default {
    components: {
        CreateModal, EditModal
    },

    mixins: [api],

    data: () => ({
        newConnection: {
            base_url: '',
            api_access_token: '',
        },
        show: {
            create_new_connection_tab: false,
        },
        selectedConnection: null,
        connections: [],
    }),

    mounted() {
        this.reloadConnections();
    },

    methods: {
        reloadConnections() {
            this.apiGetMagento2msiConnections({
                    'per_page': 100,
                    'include': 'tags,warehouse'
                })
                .then(({ data }) => {
                    this.connections = data.data;
                })
                .catch((error) => {
                    this.displayApiCallError(error);
                });
        },

        confirmDelete(connection_id){
            this.$snotify.confirm('After delete data cannot restored', 'Are you sure?', {
                position: 'centerCenter',
                buttons: [
                    {
                        text: 'Yes',
                        action: (toast) => {
                            this.handleDelete(connection_id)
                            this.$snotify.remove(toast.id);
                        }
                    },
                    {text: 'Cancel'},
                ]
            });
        },

        submit() {
            this.apiPostMagento2msiConnection({...this.newConnection})
                .then(() => {
                    this.newConnection = {
                        base_url: '',
                        api_access_token: '',
                    };
                    this.show.create_new_connection_tab = false;
                })
                .catch((error) => {
                    this.displayApiCallError(error);
                })
                .finally(() => {
                    this.reloadConnections();
                });
        },

        conncetionUpdatedEvent() {
            this.reloadConnections();
        },
    },
}

</script>

<style lang="scss" scoped>
.action-link {
    cursor: pointer;
}
</style>
