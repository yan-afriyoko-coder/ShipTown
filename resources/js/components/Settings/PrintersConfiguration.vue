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
                            <th>ID</th>
                            <th>Computer</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="error || (!error && printers.length === 0)">
                            <td colspan="5">No printers found.</td>
                        </tr>
                        <tr v-for="printer in printers" :key="printer.id" :class="{
                            'table-primary': isDefaultPrinter(printer.id)
                        }">
                            <td>{{ printer.id }}</td>
                            <td>{{ printer.computer.name }}</td>
                            <td>{{ printer.name }}</td>
                            <td>{{ printer.state }}</td>
                            <td>
                                <a v-if="!isDefaultPrinter(printer.id)" href="#" @click.prevent="setDefault(printer.id)">Use</a>
                                <a href="#" @click.prevent="printTest(printer)">Print Test</a>
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

export default {
    mixins: [api],

    created() {
        this.apiGetUserMe()
            .then(({ data }) => {
                this.defaultPrinter = data.data.printer_id;
            });

        this.apiGetPrintNodePrinters()
            .then(({ data }) => {
                this.printers = data.data;
            }).catch(e => {
                this.error = true;
            });
    },

    data: () => ({
        defaultPrinter: null,
        printers: [],
        error: false,
    }),

    methods: {
        setDefault(printerId) {
            this.apiPostUserMe({
                    'printer_id': printerId
                })
                .then(({ data }) => {
                    this.defaultPrinter = data.printer_id;
                });
        },

        printTest(printer) {
            let data = {
                'printer_id': printer.id,
                'pdf_url': 'https://api.printnode.com/static/test/pdf/label_4in_x_6in.pdf'
            };
            this.apiPostPrintnodePrintJob(data);
        },

        isDefaultPrinter(printerId) {
            return printerId === this.defaultPrinter && this.defaultPrinter != null;
        }
    }
}
</script>
