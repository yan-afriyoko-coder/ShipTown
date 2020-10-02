<div class="widget-tools-container">
    <font-awesome-icon icon="question-circle" name="status-help-icon-new-orders"></font-awesome-icon>
    <template>
        <tippy to="status-help-icon-new-orders" arrow>
            <p>This is the number of orders with a given status</p>
        </tippy>
    </template>
</div>

<div>
    <h4 class="card-title text-center">NEW ORDERS</h4>
    <h6 class="card-title text-center">LAST 7 DAYS</h6>
    <div class="row">
        <div class="col-8 offset-2">
            <table class="table table-borderless">
                <thead>
                <tr>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-right">{{ $total_count }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($new_order_counts as $status)
{{--                    @if (! in_array($status->status_code, ['picking','processing','holded']))--}}
                        <tr>
                            <td>{{ $status->status_code }}</td>
                            <td class="text-right">{{ $status->order_count }}</td>
                        </tr>
{{--                    @endif--}}
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
