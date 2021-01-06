@extends(Config::get('chatter.master_file_extend'))

@section('content')
<section class="discussion-header bg-dark">
	<div class="container">
		<div class="row">
			<div class="col-12 d-none d-md-flex flex-column flex-md-row align-items-center pt-3 pb-3">
				<a class="btn btn-secondary p-2 rounded-circle mr-3 lh-normal" href="{{ route('chatter.home') }}"><i class="fas fa-chevron-left d-flex align-items-center"></i></a>
				<h1 class="mb-0 flex-grow-1 text-primary">{{ $discussion->title }}</h1>
				<span class="text-softwhite"> @lang('chatter::messages.discussion.head_details')<a class="badge p-2 text-white ml-2" href="{{ route('chatter.category.show', ['slug' => $discussion->category->slug]) }}" style="background-color:{{ $discussion->category->color }}">{{ $discussion->category->name }}</a></span>
			</div>
			<div class="col-12 d-flex d-md-none flex-column flex-md-row align-items-center pt-3 pb-3">
				<div class="d-flex d-flex-row align-items-center">
					<a class="btn btn-secondary p-2 rounded-circle mr-3 lh-normal" href="{{ route('chatter.home') }}"><i class="fas fa-chevron-left d-flex align-items-center"></i></a>
					<span class="text-softwhite"> @lang('chatter::messages.discussion.head_details')<a class="badge p-2 text-white ml-2" href="{{ route('chatter.category.show', ['slug' => $discussion->category->slug]) }}" style="background-color:{{ $discussion->category->color }}">{{ $discussion->category->name }}</a></span>
				</div>
				<h1 class="mb-0 flex-grow-1 text-primary">{{ $discussion->title }}</h1>
			</div>
		</div>
	</div>
</section>

@if(config('chatter.errors'))
<section class="alerts">
	@if(Session::has('chatter_alert'))
		@include('chatter::includes.alert')
	@endif

	@if (count($errors) > 0)
		@include('chatter::includes.error-alert')
	@endif
</section>
@endif

<section class="bg-darker forum pt-5 pb-5">
	<div class="container">
		<div class="row">
			<div class="col posts">
			@foreach($posts as $post)
				@include('chatter::includes.post')
			@endforeach
			</div>
		</div>
		
		<div class="row">
			<div class="col">
				<!-- Pagination -->
				{{ $posts->links() }}
			</div>
		</div>

		<div class="row">
			<div class="col">
				<hr class="mt-5 mb-5"/>

				@auth
				<h2 class="mb-4">@lang('forum.newResponse')</h2>
				<div class="new-response">
					<div class="form">
						<form id="chatter_form_editor" action="{{ route('chatter.posts.store') }}" method="POST">
							@csrf

							<!-- Body -->
							<div id="editor">
								<textarea id="new-response-textarea" name="body" placeholder="@lang('chatter::messages.editor.tinymce_placeholder')">{{ old('body') }}</textarea>
							</div>

							<!-- Discussion -->
							<input type="hidden" name="chatter_discussion_id" value="{{ $discussion->id }}">
						</form>
					</div><!-- #new_response -->

					<div class="footer p-2">
						<button id="submit_response" class="btn btn-success float-right"><i class="fas fa-plus-circle"></i> @lang('chatter::messages.response.submit')</button>
						<div class="clearfix"></div>
					</div>
				</div>
				@endauth

				@guest
				<div id="login_or_register" class="text-white text-center">
					<p>
						@lang('forum.messages.auth', ['login' => route(config('chatter.routes.login'))])
					</p>
				</div>
				@endguest
			</div>
		</div>
	</div>
</section>

<input type="hidden" id="current_path" value="{{ Request::path() }}">
@endsection

@push(Config::get('chatter.stacks.style'))
<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
<script src="{{ mix('css/chatter.css', 'vendor/skyraptor/chatter') }}"></script>
@endpush

@push(Config::get('chatter.stacks.script'))
<script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
<script src="{{ mix('js/chatter-discussion.js', 'vendor/skyraptor/chatter') }}"></script>
@endpush