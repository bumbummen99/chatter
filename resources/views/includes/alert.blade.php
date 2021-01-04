<div class="chatter-alert alert alert-dismissible alert-{{ Session::get('chatter_alert_type') }} rounded-0 font-weight-bold" role="alert">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <strong><i class="chatter-alert-{{ Session::get('chatter_alert_type') }}"></i> {{ Config::get('chatter.alert_messages.' . Session::get('chatter_alert_type')) }}</strong>
                {{ Session::get('chatter_alert') }}
            </div>
        </div>
    </div>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <i class="chatter-close fas fa-times-circle"></i>
    </button>
</div>
<div class="chatter-alert-spacer"></div>