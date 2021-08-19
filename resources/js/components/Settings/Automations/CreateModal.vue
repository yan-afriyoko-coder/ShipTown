<template>
    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div ref="loadingContainer2" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Automation</h5>
                </div>
                <div class="modal-body">
                    <ValidationObserver ref="form">
                        <form class="form" @submit.prevent="submit" ref="loadingContainer">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label for="create-name">Automation Name</label>
                                        <ValidationProvider vid="name" name="name" v-slot="{ errors }">
                                            <input v-model="automation.name" :class="{
                                                'form-control': true,
                                                'is-invalid': errors.length > 0,
                                            }" id="create-name" required>
                                            <div class="invalid-feedback">
                                                {{ errors[0] }}
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="create-priority">Priority</label>
                                        <ValidationProvider vid="priority" name="priority" v-slot="{ errors }">
                                            <input v-model="automation.priority" :class="{
                                                'form-control': true,
                                                'is-invalid': errors.length > 0,
                                            }" id="create-priority" type="number">
                                            <div class="invalid-feedback">
                                                {{ errors[0] }}
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group float-right">
                                        <label for="create-enabled">Enabled</label>
                                        <ValidationProvider vid="enabled" name="enabled" v-slot="{ errors }">
                                            <div class="custom-control custom-switch mt-2" :class="{'is-invalid' : errors.length}">
                                                <input type="checkbox" v-model="automation.enabled"
                                                    id="create-enabled"
                                                    class="custom-control-input"
                                                    required>
                                                <label class="custom-control-label" for="create-enabled"></label>
                                            </div>
                                            <div class="invalid-feedback">
                                                {{ errors[0] }}
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                </div>
                            </div>

                            <div class="block">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="block-title">When</div>
                                    </div>
                                    <div class="col-md-7">
                                        <ValidationProvider vid="event_class" name="event_class" v-slot="{ errors }">
                                            <select v-model="automation.event_class"
                                                :class="{
                                                    'form-control': true,
                                                    'is-invalid': errors.length > 0,
                                                }"
                                            >
                                                <option v-for="event, index in events" :key="index" :value="event.class">{{ event.description }}</option>
                                            </select>
                                            <div class="invalid-feedback">
                                                {{ errors[0] }}
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                </div>
                            </div>

                            <div class="block">
                                <div class="row" v-for="condition, index in automation.conditions" :key="condition.id">
                                    <div class="col-md-2">
                                        <div class="block-title" v-if="!index">If</div>
                                    </div>
                                    <div class="col-md-5">
                                        <ValidationProvider :vid="`conditions.${index}.validation_class`" :name="`conditions.${index}.validation_class`" v-slot="{ errors }">
                                            <select v-model="condition.validation_class" :class="{
                                                    'form-control': true,
                                                    'is-invalid': errors.length > 0,
                                                }"
                                            >
                                                <template v-if="selectedEvent">
                                                    <option v-for="validation, indexOption in selectedEvent.validators" :key="indexOption" :value="validation.class">{{ validation.description }}</option>
                                                </template>
                                            </select>
                                            <div class="invalid-feedback">
                                                {{ errors[0] }}
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-md-5">
                                        <ValidationProvider :vid="`conditions.${index}.condition_value`" :name="`conditions.${index}.condition_value`" v-slot="{ errors }">
                                            <div class="input-group mb-1">
                                                <input v-model="condition.condition_value" :class="{
                                                    'form-control': true,
                                                    'is-invalid': errors.length > 0,
                                                }">
                                                <div class="input-group-append">
                                                    <button class="btn btn-danger" type="button" @click="removeCondition(index)">-</button>
                                                </div>
                                                <div class="invalid-feedback">
                                                    {{ errors[0] }}
                                                </div>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-2">&nbsp;</div>
                                    <div class="col-md-10">
                                        <a href="#" @click="addCondition">+ Add Condition</a>
                                    </div>
                                </div>
                            </div>

                            <div class="block">
                                <div class="row" v-for="execution, index in automation.executions" :key="execution.id">
                                    <div class="col-md-2">
                                        <div class="block-title" v-if="!index">Then</div>
                                    </div>
                                    <div class="col-md-5">
                                        <ValidationProvider :vid="`executions.${index}.execution_class`" :name="`executions.${index}.execution_class`" v-slot="{ errors }">
                                            <select v-model="execution.execution_class" :class="{
                                                    'form-control': true,
                                                    'is-invalid': errors.length > 0,
                                                }"
                                            >
                                                <template v-if="selectedEvent">
                                                    <option v-for="executor, indexOption in selectedEvent.executors" :key="indexOption" :value="executor.class">{{ executor.description }}</option>
                                                </template>
                                            </select>
                                            <div class="invalid-feedback">
                                                {{ errors[0] }}
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-md-5">
                                        <ValidationProvider :vid="`executions.${index}.execution_value`" :name="`executions.${index}.execution_value`" v-slot="{ errors }">
                                            <div class="input-group mb-1">
                                                <input v-model="execution.execution_value" :class="{
                                                    'form-control': true,
                                                    'is-invalid': errors.length > 0,
                                                }">
                                                <div class="input-group-append">
                                                    <button class="btn btn-danger" type="button" @click="removeAction(index)">-</button>
                                                </div>
                                                <div class="invalid-feedback">
                                                    {{ errors[0] }}
                                                </div>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-2">&nbsp;</div>
                                    <div class="col-md-10">
                                        <a href="#" @click="addAction">+ Add Action</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </ValidationObserver>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="closeModal" class="btn btn-default">Cancel</button>
                    <button type="button" @click="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ValidationObserver, ValidationProvider } from "vee-validate";

import Loading from "../../../mixins/loading-overlay";
import api from "../../../mixins/api";

export default {
    name: "CreateModal",

    mixins: [api, Loading],

    components: {
        ValidationObserver, ValidationProvider
    },

    props: ['events'],

    data() {
        return {
            automation: {
                name: '',
                event_class: '',
                enabled: true,
                priority: 1,
                conditions: [],
                executions: []
            },
        }
    },

    computed: {
        selectedEvent(){
            return this.events.find(event => event.class === this.automation.event_class);
        }
    },

    mounted() {
        this.addCondition();
        this.addAction();
    },

    methods: {

        addCondition(){
            this.automation.conditions.push({
                id: Date.now(),
                validation_class: '',
                condition_value: ''
            });
        },

        addAction(){
            this.automation.executions.push({
                id: Date.now(),
                execution_class: '',
                execution_value: ''
            });
        },

        removeCondition(index){
            this.automation.conditions.splice(index, 1);
            if(!this.automation.conditions.length){
                this.addCondition();
            }
        },

        removeAction(index){
            this.automation.executions.splice(index, 1);
            if(!this.automation.executions.length){
                this.addAction();
            }
        },

        submit() {
            this.showLoading();
            this.apiPostAutomations(this.automation)
                .then(({ data }) => {
                    this.$snotify.success('Automation created.');
                    this.closeModal();
                    this.resetForm()
                    this.$emit('onCreated', data.data);
                })
                .catch((error) => {
                    if (error.response) {
                        if (error.response.status === 422) {
                            this.$refs.form.setErrors(error.response.data.errors);
                        }
                    }
                })
                .finally(this.hideLoading);
        },

        resetForm(){
            this.automation = {
                name: '',
                event_class: '',
                enabled: true,
                priority: 1,
                conditions: [],
                executions: []
            }
        },

        closeModal() {
            $(this.$el).modal('hide');
        }
    },
}
</script>

<style scoped>
.block {
    background-color: #f1f1f1;
    border-radius: 8px;
    padding: .5rem;
    margin: .5rem 0;
}
.block-title {
    padding: 7px 0 0 10px;
    font-weight: 600;
}
</style>
