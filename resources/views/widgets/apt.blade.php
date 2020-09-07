<div class="widget-tools-container">
    <a style="cursor:pointer;" data-toggle="modal" data-target="#aptConfigurationModal"><font-awesome-icon icon="cog"></font-awesome-icon></a>
    <font-awesome-icon icon="question-circle" name="apt-help-icon"></font-awesome-icon>
    <template>
        <tippy to="apt-help-icon" arrow>
            <p>
                APT - Average Processing Time<br>
                <br>
                This is the average time difference between time when order has been placed
                and time when status was first changed to something different that "processsing" <br>
                <br>
                Only orders with one of the following statuses are taken into calculations:<br>
                @foreach($statuses as $status)
                - {{$status}}<br>
                @endforeach
            </p>
        </tippy>
    </template>
</div>


<div>
{{--    <h4 class="card-title text-center">30 DAY APT</h4>--}}
    <h6 class="card-title text-center"></h6>
    <div class="row">
        <div class="col-8 offset-2">
            <table class="table table-borderless">
                <thead>
                <tr>
                    <th scope="col">30 Day APT</th>
                    <th scope="col" class="text-right">{{ $apt_string }}</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>



<!-- Modal -->
<apt-configuration-modal
    id='aptConfigurationModal'
    name='apt'
    :statuses='@json($statuses)'
    :widget-config='@json($config)'
    @if($widget_id)
        :widget-id="{{ $widget_id }}"
    @endif
></apt-configuration-modal>
