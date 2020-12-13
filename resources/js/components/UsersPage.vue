<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Users
                    </span>
                    <a tabindex="-1" class="action-link" v-b-modal.invite-modal>
                        Invite User
                    </a>
                </div>
            </div>

            <div class="card-body">
                <table v-if="users.length > 0" class="table table-borderless table-responsive mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Name</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(user, i) in users" :key="i">
                            <td>{{ user.id }}</td>
                            <td>{{ user.email }}</td>
                            <td>{{ user.name }}</td>
                            <td>
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
        <b-modal id="invite-modal" title="Invite User" @ok="handleInviteOk">
            <invite-modal ref="inviteForm"></invite-modal>
        </b-modal>
        <b-modal ref="editModal" id="edit-modal" title="Edit User" @ok="handleEditOk">
            <edit-modal v-if="selectedId" :id="selectedId" :roles="roles" ref="editForm"></edit-modal>
        </b-modal>
    </div>
</template>

<script>
import { find } from 'lodash';

import Invite from './Users/Invite';
import Edit from './Users/Edit';

export default {
    components: {
        'invite-modal': Invite,
        'edit-modal': Edit,
    },

    created() {
        axios.get('/api/settings/user/me')
            .then(({ data }) => {
                this.currentUser = data.data;
            })
    },

    mounted() {
        this.loadUsers();
        this.loadRoles();
    },

    data: () => ({
        currentUser: {},
        users: [],
        roles: [],
        selectedId: null,
    }),

    methods: {
        loadUsers() {
            axios.get('/api/admin/users?per_page=999')
                .then(({ data }) => {
                    this.users = data.data;
                });
        },

        loadRoles() {
            axios.get('/api/admin/user/roles')
                .then(({ data }) => {
                    this.roles = data.data;
                });
        },

        handleInviteOk(bvModalEvt) {
            bvModalEvt.preventDefault();
            this.$refs.inviteForm.submit();
        },

        handleEditOk(bvModalEvt) {
            bvModalEvt.preventDefault();
            this.$refs.editForm.submit();
            this.$refs.editModal.hide();
        },

        onEditClick(id) {
            this.selectedId = id;
            this.$refs.editModal.show();
        },

        onDeleteClick(id) {
            this.$bvModal.msgBoxConfirm('Deactivate this user?')
                .then(value => {
                    if (value === true) {
                        axios.delete(`/api/admin/users/${id}`).then(() => {
                            this.$snotify.success('User deactivated.');
                            let index = find(this.users, ['id', id]);

                            this.users = this.users.splice(index, 1);
                        });
                    }
                })
                .catch(err => {
                    console.log(err);
                });
        }
    },

    computed: {
        isNotSelf() {
            return (id) => this.currentUser.id !== id;
        }
    }
}
</script>
