<div class="widget-tools-container">
    <font-awesome-icon icon="question-circle" name="pick-counts-help-icon"></font-awesome-icon>
    <template>
        <tippy to="pick-counts-help-icon" arrow>
            <p>
                Total number of picks in last 7 days<br>
            </p>
        </tippy>
    </template>
</div>
<div class="card text-center">
    <div class="card-body">
        <h4 class="card-title">7 days picks count</h4>
        <h5 class="card-title"></h5>
        <h2 class="card-text"><strong>{{ $last7days_count }}</strong></h2>
    </div>
</div>
