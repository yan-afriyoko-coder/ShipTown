<div class="widget-tools-container">
    <font-awesome-icon icon="question-circle" name="status-help-icon-completed-orders-by-date"></font-awesome-icon>
    <template>
        <tippy to="status-help-icon-completed-orders-by-date" arrow>
            <p>This is the number of orders with a given status</p>
        </tippy>
    </template>
</div>

<div>
{{--    <h4 class="card-title text-center">COM ORDERS</h4>--}}
{{--    <h6 class="card-title text-center">LAST 7 DAYS</h6>--}}
    <div class="row">
        <div class="col-8 offset-2">
            <table class="table table-borderless">
                <thead>
                <tr>
                    <th scope="col">Completed orders - last 7 days</th>
                    <th scope="col" class="text-right">{{ $total_count }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($data as $date)
                    <tr>
                        <td>{{ $date->date_closed_at }}</td>
                        <td class="text-right">{{ $date->order_count }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
