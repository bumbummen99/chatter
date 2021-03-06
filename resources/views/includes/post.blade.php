<div class="row no-gutters post mb-3" data-id="{{ $post->id }}">
    <!-- Left -->
    <div class="left col-12 col-lg-2 p-3 d-flex flex-row flex-lg-column align-items-center">
        <div class="avatar mb-lg-3 mr-3 mr-lg-0">
            @if(Config::get('chatter.user.avatar_image_database_field'))
                <!-- If the user db field contains http:// or https:// we don't need to use the relative path to the image assets -->
                @if( (substr($post->user->getAttribute(Config::get('chatter.user.avatar_image_database_field')), 0, 7) == 'http://') || (substr($post->user->getAttribute(Config::get('chatter.user.avatar_image_database_field')), 0, 8) == 'https://') )
                <img src="{{ $post->user->getAttribute(Config::get('chatter.user.avatar_image_database_field'))  }}" class="img-fluid d-block mx-auto rounded">
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
        <div class="user-info w-100 flex-fill text-center text-light overflow-hidden">
            <a class="lead mb-lg-2 d-block text-truncate text-decoration-none" href="{{ \SkyRaptor\Chatter\Helpers\ChatterHelper::userLink($post->user) }}">{{ ucfirst($post->user->{Config::get('chatter.user.database_field_with_user_name')}) }}</a>
            <!-- Add more user info/details here -->
        </div>
    </div>
    <!-- Left -->
    <!-- Right -->
    <div class="right col-12 col-lg-10 p-3">
        <div class="d-flex flex-column flex-grow-1 h-100">
            <!-- Content -->
            <div class="content d-flex flex-column flex-grow-1">
                <!-- Post Content -->
                <div class="main text-white">
                    <div class="body text-break" markdown="{{ $post->body }}">
                        {!! $post->getBodyAsHtml() !!}
                    </div>
                </div>
            </div>
            <!-- /Content -->

            <div class="footer d-flex flex-column flex-md-row align-items-center">
                <!-- Post Details -->
                <div class="details text-center text-md-left text-softwhite flex-fill">
                    <span class="ago">{{ \Carbon\Carbon::createFromTimeStamp(strtotime($post->created_at))->diffForHumans() }}{{ !$post->created_at->equalTo($post->updated_at) ? ', ' . __('forum.post.last-edited') . ' ' . \Carbon\Carbon::createFromTimeStamp(strtotime($post->updated_at))->diffForHumans() : '' }}</span>
                </div>

                @if(!Auth::guest() && (Auth::user()->id == $post->user->id))
                <!-- Actions -->
                <div class="actions d-flex align-items-center justify-content-end">
                    <!-- Default Post actions -->
                    <div class="chatter_post_actions text-nowrap">
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
                    
                </div>
                <!-- /Actions -->
                @endif
            </div>
        </div>
    </div>
    <!-- /Right -->
</div>