<div class="widget-tools-container">
    <font-awesome-icon icon="question-circle" name="user-pick-counts-help-icon"></font-awesome-icon>
    <template>
        <tippy to="user-pick-counts-help-icon" arrow>
            <p>
                Total number of picks in last 7 days per user<br>
            </p>
        </tippy>
    </template>
</div>
<div class="card text-center">
    <div class="card-body">
        <h4 class="card-title text-left">Picks per user</h4>
        <h6 class="card-title text-left">Last 7 days</h6>
        @foreach ($count_per_user as $count)
            <div class="row">
                <div class="col-4 offset-2 text-left">
                    {{ $count['name'] }}
                </div>
                <div class="col-4 text-right">
                    {{ $count['total'] }}
                </div>
            </div>
        @endforeach
    </div>
</div>
