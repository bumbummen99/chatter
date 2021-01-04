@extends(Config::get('chatter.master_file_extend'))

@section('content')
@include('chatter::includes.hero')

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

<section class="bg-darker forum text-break pt-5">
	<div class="container">
		<div class="row">
			<!-- Sidebar -->
			<div class="col-lg-3 mb-3">
				<!-- New Discussion Button -->
				<button class="btn btn-block btn-primary mb-3" id="new_discussion_btn"><i class="fas fa-plus-circle"></i> @lang('chatter::messages.discussion.new')</button>

				<!-- All Discussions Button -->
				<a href="{{ route('chatter.home') }}"><i class="chatter-bubble"></i> @lang('chatter::messages.discussion.all')</a>

				<!-- Category Filter Nav -->
				<div class="category-nav">
					@include('chatter::includes.categories-menu', ['categories' => $categories->whereNull('parent_id')])
				</div>
			</div>
			<!-- /Sidebar -->

			<!-- Discussions -->
			<div class="col-lg-9 discussions">
				@foreach($discussions as $discussion)
					@include('chatter::includes.discussion-excerpt')
				@endforeach

				<!-- Pagination -->
				<div class="row">
					<div class="col d-flex justify-content-center">
						{{ $discussions->links() }}
					</div>
				</div>
			</div>
			<!-- /Discussions -->
		</div>
	</div>
</section>

<div id="new-discussion" class="fixed-bottom" style="display:none;">
	<div class="container bg-dark mh-50 overflow-auto pt-3 pb-3">
		<form id="chatter_form_editor" action="{{ route('chatter.discussion.store') }}" method="POST">
			@csrf

			<div class="row mb-3 align-items-center">
				<div class="col-md-2 mb-3 mb-md-0 order-md-3">
					<button class="btn btn-block btn-danger cancel-discussion fas fa-times-circle" type="button"></button>
				</div>

				<div class="col-md-6 mb-3 mb-md-0">
					<!-- TITLE -->
					<input type="text" class="form-control" id="title" name="title" placeholder="@lang('chatter::messages.editor.title')" value="{{ old('title') }}" >
				</div>

				<div class="col-md-4 mb-3 mb-md-0">
					<!-- CATEGORY -->
					<select id="chatter_category_id" class="form-control" name="chatter_category_id">
						<option value="">@lang('chatter::messages.editor.select')</option>
						@foreach($categories as $category)
							<option value="{{ $category->id }}" {{(old('chatter_category_id') == $category->id) || (!empty($current_category_id) && $current_category_id == $category->id) ? 'selected' : ''}}>{{ $category->name }}</option>
						@endforeach
					</select>
				</div>
			</div><!-- .row -->

			<!-- BODY -->
			<div class="row mb-3">
				<div class="col-12">
					<textarea id="new-discussion-textarea" name="body" placeholder="@lang('chatter::messages.editor.tinymce_placeholder')">{{ old('body') }}</textarea>
				</div>
			</div>

			<div class="row">
				<div class="col-12">
					<button class="btn btn-secondary cancel-discussion" type="button">@lang('chatter::messages.words.cancel')</button>
					<button id="submit_discussion" class="btn btn-success float-right" type="submit"><i class="fas fa-plus-circle"></i> @lang('chatter::messages.discussion.create')</button>
					<div class="clearfix"></div>
				</div>
			</div>
		</form>
	</div>
</div>

<input type="hidden" id="current_path" value="{{ Request::path() }}">
@endsection

@push(Config::get('chatter.stacks.style'))
<script src="{{ mix('css/chatter.css', 'vendor/skyraptor/chatter') }}"></script>
@endpush

@push(Config::get('chatter.stacks.script'))
<script src="{{ mix('js/chatter-home.js', 'vendor/skyraptor/chatter') }}"></script>
<script>
	$('document').ready(function() {
		for (const element of document.querySelectorAll('.cancel-discussion')) {
			element.addEventListener('click', event => {
				$('#new-discussion').slideUp();
			});
		}
	});
</script>
@if (Auth::user())
<script>
	$('document').ready(function(){
		document.querySelector('#new_discussion_btn').addEventListener('click', event => {
			$('#new-discussion').slideDown();
			$('#title').focus();
		});
	});
</script>
@else
<script>
	$('document').ready(function(){
		document.querySelector('#new_discussion_btn').addEventListener('click', event => {
			window.location.href = "{{ route(Config::get('chatter.routes.login')) }}";
		});
	});
</script>
@endif

@if (count($errors) > 0)
<script>
	$('document').ready(function(){
		$('#new-discussion').slideDown();
		$('#title').focus();
	});
</script>
@endif
@endpush