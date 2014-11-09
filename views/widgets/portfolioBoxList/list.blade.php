@foreach($pages as $page)
    <div class="{{ $options['itemBoxClass'] }}">
        <a class="{{ $options['itemClass'] }}" href="{{$page->url}}">
            <img src="{{{ $page->imageUrl }}}" alt="" class="portifolio">
            <div class="portifolio-description">
                {{{ $page->title }}}
            </div>
        </a>
    </div>
@endforeach