@foreach($data as $key => $message)
    <div class="row">
        <div class="col-md-12">
            <label class="control-label">
                <i class="fa fa-check"></i> {!! $message['display_message'] !!}
            </label>
        </div>
    </div>
@endforeach
