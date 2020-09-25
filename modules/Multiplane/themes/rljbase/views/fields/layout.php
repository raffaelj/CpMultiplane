<?php

// content is a layout field
if (!$content || !is_array($content)) return;
?>

@foreach($content as $component)
  @render('views:blocks/'.$component['component'].'.php', $component)
@endforeach
