<div class="chatter-alert alert alert-dismissible alert-danger rounded-0 font-weight-bold" role="alert">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <p><strong><i class="chatter-alert-danger"></i> @lang('chatter::alert.danger.title')</strong> @lang('chatter::alert.danger.reason.errors')</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <i class="chatter-close fas fa-times-circle"></i>
    </button>
</div>