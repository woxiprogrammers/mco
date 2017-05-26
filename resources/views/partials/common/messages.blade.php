@if (count($errors) > 0)
<div class="alert alert-danger">
    <button class="close" data-close="alert"></button>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@if(Session::has('error'))
<div class="alert alert-danger">
    <button class="close" data-close="alert"></button>
    <span>{{ Session::get('error') }}</span>
</div>
@endif
@if(Session::has('success'))
<div class="alert alert-success">
    <button class="close" data-close="alert"></button>
    <span>{{ Session::get('success') }}</span>
</div>
@endif