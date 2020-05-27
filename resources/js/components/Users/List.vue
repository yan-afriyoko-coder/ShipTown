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
                            <td></td>
                        </tr>
                    </tbody>
                </table>

                <p v-else class="mb-0">
                    No user found.
                </p>
            </div>
        </div>
        <!-- The modal -->
        <b-modal ref="formModal" id="invite-modal" title="Invite User" @ok="handleModalOk">
            <invite-modal ref="form"></invite-modal>   
        </b-modal>
    </div>
</template>

<script>
import Invite from './Invite';

export default {
    components: {
        'invite-modal': Invite,
    },

    mounted() {
        this.loadUsers();
    },

    data: () => ({
        users: [],
    }),

    methods: {
        loadUsers() {
            axios.get('/api/users')
                .then(({ data }) => {
                    this.users = data.data;
                });
        },
        
        handleModalOk(bvModalEvt) {
            bvModalEvt.preventDefault();
            this.$refs.form.submit();
        },
    }
}
</script>