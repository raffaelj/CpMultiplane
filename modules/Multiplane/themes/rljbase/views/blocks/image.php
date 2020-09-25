<?php if(empty($settings['image']['path'])) return; ?>

<img src="@thumbnail($settings['image']['path'])" alt="{{ $settings['image']['title'] ?? 'image' }}" />
