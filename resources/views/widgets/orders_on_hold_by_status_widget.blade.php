@if($total_count > 0)
    <table class="table table-borderless">
        <thead>
        <tr>
            <th scope="col">Orders - On Hold</th>
            <th scope="col" class="text-right">{{ $total_count }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($order_status_counts as $order_status)
            <tr>
                <td><a href="/orders?sort=order_placed_at&status={{$order_status->status_code }}">{{$order_status->status_code }}</a></td>
                <td class="text-right">{{ $order_status->order_count }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
