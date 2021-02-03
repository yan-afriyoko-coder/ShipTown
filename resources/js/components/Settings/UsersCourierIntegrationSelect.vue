<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Shipping Labels Configuration
                    </span>
                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <tbody>
                        <tr>
                            <td>Shipping Label</td>
                            <td class="text-right">
                                <select class="w-100">
                                    <option value="">Automatically print address label</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Shipping Number</td>
                            <td class="text-right">
                                <select class="w-100">
                                    <option value="">Ask for shipping number</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Label Template</td>
                            <td class="text-right">
                                <select class="w-100">
                                    <option value="">Generic Address Label</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    created() {
        axios.get('/api/settings/user/me')
            .then(({ data }) => {
                this.defaultPrinter = data.data.printer_id;
                axios.get('/api/settings/modules/printnode/printers')
                    .then(({ data }) => {
                        this.printers = data.data;
                    }).catch(e => {
                        this.error = true;
                    });
            });
    },

    data: () => ({
        defaultPrinter: null,
        printers: [],
        error: false,
    }),

    methods: {
        setDefault(printerId) {
            axios.post('/api/settings/user/me', {
                    'printer_id': printerId
                })
                .then(({ data }) => {
                    this.defaultPrinter = data.printer_id;
                });
        },

        isDefaultPrinter(printerId) {
            return printerId === this.defaultPrinter && this.defaultPrinter != null;
        }
    }
}
</script>
