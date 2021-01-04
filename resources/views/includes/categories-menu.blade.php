<ul class="nav nav-pills nav-stacked">
    @foreach ($categories as $category) {
        <li>
            <a href="{{ route('chatter.category.show', ['slug' => $category->slug]) }}">
                <div class="chatter-box" style="background-color:' . $category['color'].'"></div>
                $category->name
            </a>

            @if ($category->children->count())
                @include('chatter::includes.categories-menu', [
                    'categories' => $category->children
                ])
            @endif
        </li>;
    @endforeach
</ul>