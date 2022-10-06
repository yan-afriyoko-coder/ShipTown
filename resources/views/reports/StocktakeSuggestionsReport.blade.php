<div class="row col">
    <table class="table table-borderless">
        <thead>
        <tr>
            <th scope="col">{{ __($report_name) }}  </th>
            <th scope="col" class="text-right"></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($data as $record)
            <tr>
                <td>
                    <a :href="route('stocktaking')">
                        {{ data_get($record, $fields[0]) }}
                    </a>
                </td>
                <td class="text-right">{{ data_get($record, $fields[1]) === 0 ? '-' : data_get($record, $fields[1])}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
