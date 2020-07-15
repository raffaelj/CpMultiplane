
            <div class="gallery">
              @foreach($gallery as $img)
                <a href="@image($img['meta']['asset'])" class="thumbs" title="{{ $img['meta']['title'] ?? 'image' }}">
                    <img src="@thumbnail($img['meta']['asset'])" alt="{{ $img['meta']['title'] ?? 'image' }}" />
                  @if(!empty($img['meta']['title']))
                    <span>{{ $img['meta']['title'] }}</span>
                  @endif
                </a>
              @endforeach
            </div>
