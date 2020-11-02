<table class="table table-borderless">
    <thead>
    <tr>
        <th scope="col">Time To Zero</th>
        <th scope="col" class="text-right"></th>
    </tr>
    </thead>
    <tbody>
{{--    @foreach ($count_per_user as $count)--}}
        <tr>
            <td>Orders Placed</td>
            <td class="text-right">{{ $data['orders_placed_count'] }}</td>
        </tr>
        <tr>
            <td>Orders Completed</td>
            <td class="text-right">{{ $data['orders_closed_count'] }}</td>
        </tr>
        <tr>
            <td>Balance</td>
            <td class="text-right">{{ $data['balance'] }}</td>
        </tr>
        <tr>
            <td>active_orders_count</td>
            <td class="text-right">{{ $data['active_orders_count'] }}</td>
        </tr>
        <tr>
            <td>periods to zero</td>
            <td class="text-right">{{ $data['periods_to_zero'] }}</td>
        </tr>
{{--    @endforeach--}}
    </tbody>
</table>
