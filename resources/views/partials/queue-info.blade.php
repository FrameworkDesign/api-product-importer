@if(isset($queueSize))
    <div class="banner">
        <p>Jobs in Queue: <strong>{{ $queueSize }}</strong></p>
    </div>
@endif
