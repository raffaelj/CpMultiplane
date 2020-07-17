<?php
// content field is a repeater
if (!$content || !is_array($content)) return;

$children = $children ?? false;

$c = count($content);
?>

@foreach($content as $i => $block)
@if($children)<div class="item-{{$i+1}}-{{$c}}">@else<section>@endif
  @if($block['field']['type'] == 'asset')
    @render('views:partials/featured-image.php', ['image' => $block['value'], 'format' => 'bigthumbnail'])
  @elseif($block['field']['type'] == 'markdown')
    {{ $app('fields')->markdown($block['value']) }}
  @elseif($block['field']['type'] == 'wysiwyg')
    {{ $block['value'] }}
  @elseif(in_array($block['field']['type'], ['gallery', 'simple-gallery']))
    @render('views:partials/gallery.php', ['gallery' => $block['value']])
  @elseif($block['field']['type'] == 'videolink')
    @render('views:partials/video.php', ['video' => $block['value']])
  @elseif($block['field']['type'] == 'repeater')
    @render('views:fields/repeater.php', ['content' => $block['value'], 'children' => true])
  @endif
@if($children)</div>@else</section>@endif
@endforeach
