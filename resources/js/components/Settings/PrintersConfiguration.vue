<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Printer Selection
                    </span>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-responsive mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th class="w-100 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="errorMessage">
                            <td colspan="5" class="alert-danger">{{ errorMessage }}</td>
                        </tr>
                        <tr v-if="(!errorMessage && printers.length === 0)">
                            <td colspan="5">No printers found.</td>
                        </tr>
                        <tr v-for="printer in printers" :key="printer.id" :class="{'table-primary': isDefaultPrinter(printer.id)}">
                            <td>
                                {{ printer.name }} <br>
                                <span class="small text-secondary">
                                    {{ printer.computer.name }}<br>
                                    <status-icon :status="printer.state === 'online'" class="small" /> {{ printer.id }}
                                </span>
                            </td>
                            <td class="text-right">
                                <button type="button" @click.prevent="printTestPage(printer)" class="btn btn-primary btn-sm mb-1">PDF Test</button>
                                <button type="button" @click.prevent="printEplTestPage(printer)" class="btn btn-primary btn-sm mb-1">EPL Test</button>
                                <button type="button" @click.prevent="setUserPrinter(printer.id)" v-if="!isDefaultPrinter(printer.id)" class="btn btn-primary btn-sm mb-1">Use</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import api from "../../mixins/api";
import Vue from "vue";
import helpers from "../../mixins/helpers";
import StatusIcon from "./Automations/StatusIcon";

export default {
    components: {
        'status-icon': StatusIcon
    },

    mixins: [api, helpers],

    mounted() {
        this.defaultPrinter = Vue.prototype.$currentUser['printer_id'];

        this.apiGetPrintNodePrinters()
            .then(({ data }) => {
                this.printers = data.data;
            })
            .catch(e => {
                this.displayApiCallError(e);
            });
    },

    data: () => ({
        defaultPrinter: null,
        printers: [],
        errorMessage: null,
    }),

    methods: {
        setUserPrinter(printerId) {
            this.apiPostUserMe({
                    'printer_id': printerId
                })
                .then(({ data }) => {
                    this.defaultPrinter = data.printer_id;
                })
                .catch(e => {
                    this.displayApiCallError(e);
                });
        },

        printTestPage(printer) {
            let data = {
                'printer_id': printer.id,
                'pdf_url': 'https://api.printnode.com/static/test/pdf/label_4in_x_6in.pdf'
            };

            this.apiPostPrintnodePrintJob(data)
                .then(() => {
                    this.$snotify.info('Test page sent to PrintNode');
                })
                .catch(e => {
                    this.displayApiCallError(e);
                });
        },

        printEplTestPage(printer) {
            let data = {
                'printer_id': printer.id,
                'content_type': 'raw_base64',
                'base64raw_url': 'http://management.products.api.test/label_4in_x_6in.epl'
            };

            this.apiPostPrintnodePrintJob(data)
                .then(() => {
                    this.$snotify.info('Test page sent to PrintNode');
                })
                .catch(e => {
                    this.displayApiCallError(e);
                });
        },

        isDefaultPrinter(printerId) {
            return printerId === this.defaultPrinter && this.defaultPrinter != null;
        }
    }
}
</script>
