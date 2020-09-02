<div class="widget-tools-container">
    <font-awesome-icon icon="question-circle" name="user-pick-counts-help-icon"></font-awesome-icon>
    <template>
        <tippy to="user-pick-counts-help-icon" arrow>
            <p>
{{--                //Total number of picks in last 7 days per user<br>--}}
            </p>
        </tippy>
    </template>
</div>
<div class="card text-center">
    <div class="card-body">
        <h4 class="card-title">Picks</h4>

        <div class="row font-weight-bold">
            <div class="col-2 text-left">
                {{ __('Picked At') }}
            </div>
            <div class="col-2 text-left">
                {{ __('SKU') }}
            </div>
            <div class="col-4 text-left">
                {{ __('Name') }}
            </div>
            <div class="col-2 text-left">
                {{ __('Quantity') }}
            </div>
            <div class="col-2 text-left">
                {{ __('Picker') }}
            </div>
        </div>

        @foreach ($picks as $pick)
            <div class="row">
                <div class="col-2 text-left">
                    {{  \Carbon\Carbon::parse($pick['picked_at'])->format('M d, h:m') }}
                </div>
                <div class="col-2 text-left">
                    {{ $pick['sku_ordered'] }}
                </div>
                <div class="col-4 text-left">
                    {{ $pick['name_ordered'] }}
                </div>
                <div class="col-2 text-left">
                    {{ $pick['quantity_required'] }}
                </div>
                <div class="col-2 text-left">
                    {{ $pick['user']['name'] }}
                </div>
            </div>
        @endforeach
    </div>
</div>
