<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Users
                    </span>
                    <a tabindex="-1" class="action-link" v-b-modal.create-modal>
                        Add User
                    </a>
                </div>
            </div>

            <div class="card-body">
                <table v-if="users.length > 0" class="table table-borderless table-responsive mb-0">
                    <tbody>
                        <tr v-for="(user, i) in users" :key="i">
                            <td>
                                <strong>{{ user.name }}</strong>
                                <br>
                                {{ user.email }}
                            </td>
                            <td style="vertical-align:middle">{{ user.role_name }}</td>
                            <td style="vertical-align:middle">
                                <a @click.prevent="onEditClick(user.id)">
                                    <font-awesome-icon icon="user-edit"></font-awesome-icon>
                                </a>
                                <a v-if="isNotSelf(user.id)" @click.prevent="onDeleteClick(user.id)">
                                    <font-awesome-icon icon="user-minus"></font-awesome-icon>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p v-else class="mb-0">
                    No user found.
                </p>
            </div>
        </div>
        <!-- The modals -->
        <b-modal ref="createModal" id="create-modal" title="Add User" @ok="handleAddOk">
            <create-modal
                ref="createForm"
                :roles="roles"
                :warehouses="warehouses"
                @onCreated=addedUser
            >
            </create-modal>
        </b-modal>
        <b-modal ref="editModal" id="edit-modal" title="Edit User" @ok="handleEditOk">
            <edit-modal
                ref="editForm"
                v-if="selectedId"
                :id="selectedId"
                :roles="roles"
                :warehouses="warehouses"
                @onUpdated=updatedUser
            >
            </edit-modal>
        </b-modal>
    </div>
</template>

<script>
import { find } from 'lodash';

import Create from './Users/Create';
import Edit from './Users/Edit';
import api from "../mixins/api";

export default {
    mixins: [api],
    components: {
        'create-modal': Create,
        'edit-modal': Edit,
    },

    created() {
        this.apiGetUserMe()
            .then(({ data }) => {
                this.currentUser = data.data;
            })
    },

    mounted() {
        this.loadUsers();
        this.loadRoles();
        this.loadWarehouses();
    },

    data: () => ({
        currentUser: {},
        users: [],
        roles: [],
        warehouses: [],
        selectedId: null,
    }),

    methods: {
        loadUsers() {
            this.apiGetUsers({
                    'per_page': 999
                })
                .then(({ data }) => {
                    this.users = data.data;
                });
        },

        loadRoles() {
            this.apiGetUserRoles()
                .then(({ data }) => {
                    this.roles = data.data;
                });
        },

        loadWarehouses() {
            this.apiGetWarehouses()
                .then(({ data }) => {
                    this.warehouses = data.data;
                });
        },

        hideModal(ref) {
            setTimeout(() => {
                this.$refs[ref].hide();
            }, 500);
        },

        addedUser(){
            this.hideModal('createModal');
            this.loadUsers();
        },

        updatedUser(){
            this.hideModal('editModal');
            this.loadUsers();
        },

        handleAddOk(bvModalEvt) {
            bvModalEvt.preventDefault();
            this.$refs.createForm.submit();
        },

        handleEditOk(bvModalEvt) {
            bvModalEvt.preventDefault();
            this.$refs.editForm.submit();
        },

        onEditClick(id) {
            this.selectedId = id;
            this.$refs.editModal.show();
        },

        onDeleteClick(id) {
            this.$bvModal.msgBoxConfirm('Deactivate this user?')
                .then(value => {
                    if (value === true) {
                        this.apiDeleteUser(id)
                            .then(() => {
                                this.$snotify.success('User deactivated.');
                                this.loadUsers()
                            });
                    }
                })
                .catch(err => {
                    console.log(err);
                });
        },
    },

    computed: {
        isNotSelf() {
            return (id) => this.currentUser.id !== id;
        }
    }
}
</script>
