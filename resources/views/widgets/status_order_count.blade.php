<div class="widget-tools-container">
    <font-awesome-icon icon="question-circle" name="status-help-icon"></font-awesome-icon>
    <template>
        <tippy to="status-help-icon" arrow>
            <p>This is the number of orders with a given status</p>
        </tippy>
    </template>
</div>
<div class="card">
    <div class="card-body">
        <div>
            <h4 class="card-title text-center">ACTIVE ORDERS</h4>
            <div class="row">
                <div class="col-8 offset-2">
                    <table class="table table-borderless">
                        <thead>
                        <tr>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-right">Count</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($status_order_counts as $status)
                            @if (in_array($status->status_code, ['picking','processing','holded']))
                                <tr>
                                    <td>{{ $status->status_code }}</td>
                                    <td class="text-right">{{ $status->order_count }}</td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div>
            <h4 class="card-title text-center">LAST 30 DAY COMPLETED ORDERS</h4>
            <div class="row">
                <div class="col-8 offset-2">
                    <table class="table table-borderless">
                        <thead>
                        <tr>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-right">Count</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($status_order_counts as $status)
                            @if (! in_array($status->status_code, ['picking','processing','holded']))
                                <tr>
                                    <td>{{ $status->status_code }}</td>
                                    <td class="text-right">{{ $status->order_count }}</td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
