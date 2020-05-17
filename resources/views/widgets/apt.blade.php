<div class="card text-center">
  <div class="card-body">
    <h4 class="card-title">30 DAY APT</h5>
    <h6 class="card-title">
        @foreach($statuses as $status)
            {{$status}},
        @endforeach
    </h6>
    <h2 class="card-text"><strong>{{  $apt_string }}m</strong></h2>
  </div>
</div>
