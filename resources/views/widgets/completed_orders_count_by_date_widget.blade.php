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
