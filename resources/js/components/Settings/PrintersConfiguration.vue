<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        Printer Configuration
                    </span>
                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Computer</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="error || (!error && printers.length === 0)">
                            <td colspan="4">No printers found.</td>
                        </tr>
                        <tr v-for="printer in printers" :key="printer.id" :class="{
                            'table-primary': isDefaultPrinter(printer.id)
                        }">
                            <td>{{ printer.id }}</td>
                            <td>{{ printer.name }}</td>
                            <td>{{ printer.computer.name }}</td>
                            <td>{{ printer.state }}</td>
                            <td><a v-if="!isDefaultPrinter(printer.id)" href="#" @click.prevent="setDefault(printer.id)">Use</a></td>
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
