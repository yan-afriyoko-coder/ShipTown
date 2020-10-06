{{--<div class="widget-tools-container">--}}
{{--    <font-awesome-icon icon="question-circle" name="status-help-icon-orders-by-age-widget"></font-awesome-icon>--}}
{{--    <template>--}}
{{--        <tippy to="status-help-icon-orders-by-age-widget" arrow>--}}
{{--            <p>This is the number of orders with a given status</p>--}}
{{--        </tippy>--}}
{{--    </template>--}}
{{--</div>--}}

<div>
{{--    <h4 class="card-title text-center">ACTIVE ORDERS BY AGE</h4>--}}
    <div class="row">
        <div class="col-8 offset-2">
            <table class="table table-borderless">
                <thead>
                <tr>
                    <th scope="col">Order by age (all days)</th>
                    <th scope="col" class="text-right">{{ $total_count }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($orders_per_days_age as $days_age)
                    <tr>
                        <td>{{ $days_age['days_age'] }}</td>
                        <td class="text-right">{{ $days_age['order_count'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
