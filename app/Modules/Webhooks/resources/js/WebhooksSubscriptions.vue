<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Webhooks Subscriptions
                    </span>
                    <a tabindex="-1" class="action-link" @click="showAddSubscriptionModal">
                        Add
                    </a>
                </div>
            </div>

            <div class="card-body">
                <table v-if="subscriptions.length" class="table table-borderless table-responsive mb-0">
                    <thead>
                        <tr>
                            <th>ARN</th>
                            <th>Endpoint</th>
                        </tr>
                    </thead>
                    <tbody>
                    <template v-for="subscription in subscriptions">
                        <tr key="1">
                            <td>{{ subscription['SubscriptionArn'] }}</td>
                            <td>{{ subscription['Endpoint'] }}</td>
                        </tr>
                    </template>
                    </tbody>
                </table>

                <p v-else class="mb-0">
                    No Subscriptions found
                </p>
            </div>
        </div>

        <!-- The modal -->
        <b-modal ref="updateFormModal" id="new-subscription-modal" title="Subscribe" @ok="handleModalOk">
            <input class="form-control" :placeholder="'URL'"
                   id="quantity-request-input"
                   type="url"
                   v-model="new_subscriptions_url"
                   inputmode="numeric"
            />
        </b-modal>
    </div>
</template>

<script>
    import api from "../../../../../resources/js/mixins/api";
    import helpers from "../../../../../resources/js/mixins/helpers";

    export default {

        mixins: [api, helpers],

        data: () => ({
            subscriptions: {},
            new_subscriptions_url: null,
        }),

        mounted() {
            this.getConfiguration();
        },

        methods: {
            getConfiguration() {
                this.apiGet('/api/modules/webhooks/subscriptions', {})
                    .then(({ data }) => {
                        this.subscriptions = data.data['response']['Subscriptions'];
                    }).catch((error) => {
                        this.has_configuration = false;
                    });
            },

            showAddSubscriptionModal() {
                this.$bvModal.show('new-subscription-modal');
            },

            handleModalOk(bvModalEvt) {
                bvModalEvt.preventDefault();

                this.apiPost('/api/modules/webhooks/subscriptions', {'endpoint': this.new_subscriptions_url})
                    .then(() => {
                        this.notifySuccess(this.new_subscriptions_url);
                        this.getConfiguration()
                        this.$bvModal.hide('new-subscription-modal');
                    })
                    .catch((error) => {
                        this.displayApiCallError(error);
                    });

                this.getConfiguration();
            },
        }
    }

</script>

<style lang="scss" scoped>
    .action-link {
        cursor: pointer;
    }
</style>
