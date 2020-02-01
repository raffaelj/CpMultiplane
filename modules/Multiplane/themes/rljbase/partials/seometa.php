<?php

// to do:
// * fallback to content snippet, if no description is present ???
// * multiple og images
// * og:image:type
// * og:image:width
// * og:image:height
// * og:video
// * social media connections (FB, Twitter, Google...)
// * custom markup on 404 ???
// * <meta name="robots" content="max-snippet-length=-1,max-image-preview=standard,max-video-preview=-1" />
// * <link rel="canonical" href="" />

$seo = mp()->getSeoMeta($page);
?>

        <title>{{ $seo['title'] }}</title>
        <meta name="description" content="{{ $seo['description'] }}" />

@foreach($seo['og'] as $k => $v)
        <meta property="og:{{$k}}" content="{{$v}}" />
@endforeach
@foreach($seo['twitter'] as $k => $v)
        <meta property="twitter:{{$k}}" content="{{$v}}" />
@endforeach
@foreach($seo['schemas'] as $s)
        <script type="application/ld+json">{{ json_encode($s) }}</script>
@endforeach
