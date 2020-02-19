<?php

// to do:
// * fallback to content snippet, if no description is present ???
// * og:image:type
// * og:image:width
// * og:image:height
// * og:video
// * social media connections (FB, Twitter, Google...)
// * custom markup on 404 ???

$seo = mp()->getSeoMeta($page);
?>

        <title>{{{ $seo['title'] }}}</title>
        <meta name="description" content="{{{ $seo['description'] }}}" />

@if(!empty($seo['og']))
  @foreach($seo['og'] as $i)
    @foreach($i as $k => $v)
      @if(is_string($v))
        <meta property="og:{{$k}}" content="{{$v}}" />
      @elseif($k == 'image' && is_array($v))
        @foreach($v as $vv)
          @foreach($vv as $kkk => $vvv)
            @if($kkk === 'url')
        <meta property="og:image" content="{{$vvv}}" />
            @else
        <meta property="og:image:{{$kkk}}" content="{{$vvv}}" />
            @endif
          @endforeach
        @endforeach
      @endif
    @endforeach
  @endforeach
@endif

@if(!empty($seo['twitter']))
  @foreach($seo['twitter'] as $k => $v)
    @if(is_string($v))
        <meta name="twitter:{{$k}}" content="{{$v}}" />
    @endif
  @endforeach
@endif

@if(!empty($seo['schemas']))
  @foreach($seo['schemas'] as $s)
        <script type="application/ld+json">{{ json_encode($s) }}</script>
  @endforeach
@endif

@if(!empty($seo['robots']))
        <meta name="robots" content="{{ implode(', ', $seo['robots']) }}" />
@endif

@if(!empty($seo['canonical']))
        <link rel="canonical" href="{{ $seo['canonical'] }}" />
@endif
