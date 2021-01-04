<div class="row post mb-3" data-id="{{ $post->id }}">
    <!-- Left -->
    <div class="left col-12 col-sm-2 py-3">
        <div class="avatar">
            @if(Config::get('chatter.user.avatar_image_database_field'))
                <!-- If the user db field contains http:// or https:// we don't need to use the relative path to the image assets -->
                @if( (substr($post->user->getAttribute(Config::get('chatter.user.avatar_image_database_field')), 0, 7) == 'http://') || (substr($post->user->getAttribute(Config::get('chatter.user.avatar_image_database_field')), 0, 8) == 'https://') )
                <img src="{{ $post->user->getAttribute(Config::get('chatter.user.avatar_image_database_field'))  }}" {!! $post->user->main_group ? 'style="border-color:' . $post->user->main_group->color . ' !important"' : '' !!} class="img-fluid d-block mx-auto rounded">
                @else
                <img src="{{ Config::get('chatter.user.relative_url_to_image_assets') . $post->user->getAttribute(Config::get('chatter.user.avatar_image_database_field'))  }}" class="img-fluid d-block mx-auto rounded">
                @endif
            @else
                <p class="text-center lead">
                    <span class="p-2 chatter_avatar_circle" style="background-color:#{{ \SkyRaptor\Chatter\Helpers\ChatterHelper::stringToColorCode($post->user->getAttribute(Config::get('chatter.user.database_field_with_user_name'))) }}">
                        {{ strtoupper(substr($post->user->getAttribute(Config::get('chatter.user.database_field_with_user_name')), 0, 1)) }}
                    </span>
                </p>
            @endif
        </div>
    </div>
    <!-- Left -->
    <!-- Right -->
    <div class="right col-12 col-sm-10 py-3">
        <div class="d-flex flex-column flex-grow-1 h-100">
            <!-- Post Details -->
            <div class="details text-center text-sm-left text-softwhite mb-2">
                <a class="lead" href="{{ \SkyRaptor\Chatter\Helpers\ChatterHelper::userLink($post->user) }}" {{ $post->user->group ? 'style=color:' . $post->user->group->color : ''}}>{{ ucfirst($post->user->{Config::get('chatter.user.database_field_with_user_name')}) }}</a> <span class="ago">{{ \Carbon\Carbon::createFromTimeStamp(strtotime($post->created_at))->diffForHumans() }}</span>
            </div>

            <!-- Content -->
            <div class="content d-flex flex-column flex-grow-1">
                <!-- Post Content -->
                <div class="main text-white">
                    <div class="body text-break">
                        {!! \GrahamCampbell\Markdown\Facades\Markdown::convertToHtml($post->body) !!}
                    </div>
                </div>
            </div>
            <!-- /Content -->

            <!-- Actions -->
            <div class="actions pt-2 d-flex align-items-center justify-content-end">
                @if(!Auth::guest() && (Auth::user()->id == $post->user->id))
                <!-- Default Post actions -->
                <div class="chatter_post_actions">
                    <button class="btn btn-secondary chatter_edit_btn">
                        <i class="fas fa-edit"></i> @lang('chatter::messages.words.edit')
                    </button>
                    <button class="btn btn-danger chatter_delete_btn" data-toggle="modal" data-target="#modal-delete-post-{{ $post->id }}">
                        <i class="fas fa-trash-alt"></i> @lang('chatter::messages.words.delete')
                    </button>

                    <form class="post-edit-form d-none" action="{{ route('chatter.posts.update', $post) }}" method="POST">
                        @method('PATCH')
                        @csrf
                        <input type="hidden" name="body">
                    </form>

                    <form class="post-delete-form d-none" action="{{ route('chatter.posts.destroy', $post)}}" method="POST">
                        @method('DELETE')
                        @csrf
                    </form>

                    <div class="modal fade" id="modal-delete-post-{{ $post->id }}" tabindex="-1" role="dialog" aria-labelledby="modal-delete-post-{{ $post->id }}-label" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-delete-post-{{ $post->id }}-label">@lang('chatter::messages.response.confirm')</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    ...
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('chatter::messages.response.no_confirm')</button>
                                    <button type="button" class="btn btn-primary btn-delete-post" post-id="{{ $post->id }}">@lang('chatter::messages.response.yes_confirm')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Editing actions -->
                <div class="chatter_update_actions d-none">
                    <button class="btn btn-secondary cancel_chatter_edit">@lang('chatter::messages.words.cancel')</button>
                    <button class="btn btn-success update_chatter_edit"><i class="fas fa-check"></i>@lang('chatter::messages.response.update')</button>
                </div>
                @endif
            </div>
            <!-- /Actions -->
        </div>
    </div>
    <!-- /Right -->
</div>