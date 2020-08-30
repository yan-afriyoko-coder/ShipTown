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
        <h4 class="card-title">7 days picks count per user</h4>
        @foreach ($count_per_user as $count)
        <h2 class="card-text">
            <strong>{{ $count['name'] }} : {{ $count['total'] }}</strong>
        </h2>
        @endforeach
    </div>
</div>
