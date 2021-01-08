<div class="row discussion mb-3">
    <!-- Left -->
    <div class="left col-12 col-sm-2 py-3">
        <div class="avatar">
            @if(Config::get('chatter.user.avatar_image_database_field'))
                <img src="{{ Config::get('chatter.user.relative_url_to_image_assets') . $discussion->user->getAttribute(Config::get('chatter.user.avatar_image_database_field'))  }}" class="img-fluid d-block mx-auto rounded">
            @else
                <p class="text-center lead">
                    <span class="p-2 chatter_avatar_circle" style="background-color:#{{ \SkyRaptor\Chatter\Helpers\ChatterHelper::stringToColorCode($discussion->user->getAttribute(Config::get('chatter.user.database_field_with_user_name'))) }}">
                        {{ strtoupper(substr($discussion->user->getAttribute(Config::get('chatter.user.database_field_with_user_name')), 0, 1)) }}
                    </span>
                </p>
            @endif
        </div>
    </div>
    <!-- /Left -->

    <!-- Right -->
    <div class="right col-12 col-sm-10 py-3">
        <div class="d-flex flex-column flex-grow-1 h-100">
            <!-- Post Title -->
            <div class="title d-flex flex-column flex-sm-row align-items-center mb-2">
                <a class="d-block w-100 text-center text-sm-left" href="{{ route('chatter.discussion.showInCategory', ['category' => $discussion->category->slug, 'slug' => $discussion->slug]) }}">
                    <h3 class="flex-grow-1 mb-0">{{ $discussion->title }}</h3>
                </a>
                <span class="category badge text-white p-2" style="background-color:{{ $discussion->category->color }}">{{ $discussion->category->name }}</span>
            </div>

            <!-- Post Details -->
            <div class="details text-center text-sm-left text-softwhite mb-2">
                <a href="{{ \SkyRaptor\Chatter\Helpers\ChatterHelper::userLink($discussion->user) }}">{{ ucfirst($discussion->user->{Config::get('chatter.user.database_field_with_user_name')}) }}</a> <span class="ago">{{ \Carbon\Carbon::createFromTimeStamp(strtotime($discussion->created_at))->diffForHumans() }}</span>
            </div>

            <!-- Content -->
            <div class="content d-flex flex-row flex-grow-1">
                <!-- Post Content -->
                <div class="main flex-fill d-flex flex-row align-items-center text-white">
                    <div class="body text-break">
                        @php
                        $text = strip_tags($discussion->post()->getBodyAsHtml());
                        @endphp
                        {{ substr($text, 0, 200) }}@if(strlen(strip_tags($text)) > 200){{ '...' }}@endif
                    </div>
                </div>
                <div class="p-3 text-primary text-center">
                    <i class="fas fa-comments"></i>
                    <div class="answer_count">{{ $discussion->posts_count - 1 }}</div>
                </div>
            </div>
            <!-- /Content -->
        </div>
    </div>
    <!-- /Right -->
</div>