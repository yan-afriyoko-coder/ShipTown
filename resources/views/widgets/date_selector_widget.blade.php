<table class="table table-borderless">
    <thead>
    </thead>
    <tbody>
    <tr>
        <td scope="col" class="text-right small">from <b>{{ $config['starting_date'] }}</b> to <b>{{ $config['ending_date'] }}</b>
            <select id="selectbox" name="" onchange="javascript:location.href = this.value;">
                <option value="" selected></option>
                <option value="?between_dates=today,now">Today</option>
                <option value="?between_dates=yesterday,today">Yesterday</option>
                <option value="?between_dates=-7days,now">Last 7 days</option>
                <option value="?between_dates=this week monday,now">This week</option>
                <option value="?between_dates=last week monday,this week monday">Last Week</option>
            </select>
        </td>
    </tr>
    </tbody>
</table>
