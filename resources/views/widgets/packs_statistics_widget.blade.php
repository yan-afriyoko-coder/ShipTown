{{--<div class="widget-tools-container">--}}
{{--    <font-awesome-icon icon="question-circle" name="user-packing-counts-help-icon"></font-awesome-icon>--}}
{{--    <template>--}}
{{--        <tippy to="user-packing-counts-help-icon" arrow>--}}
{{--            <p>--}}
{{--                Total number of picks in last 7 days per user<br>--}}
{{--            </p>--}}
{{--        </tippy>--}}
{{--    </template>--}}
{{--</div>--}}
<div>
{{--    <h4 class="card-title text-center">PACKING STATISTICS</h4>--}}
{{--    <h6 class="card-title text-center">LAST 7 DAYS</h6>--}}
    <div class="row">
        <div class="col-8 offset-2">
            <table class="table table-borderless">
                <thead>
                <tr>
                    <th scope="col">Packer</th>
                    <th scope="col" class="text-right">{{ $total_count }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($count_per_user as $count)
                    <tr>
                        <td>{{ $count['name'] }}</td>
                        <td class="text-right">{{ $count['total'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
