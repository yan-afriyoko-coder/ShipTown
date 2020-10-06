<table class="table table-borderless">
    <thead>
    <tr>
        <th scope="col">Active Orders By Age</th>
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
