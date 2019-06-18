<?php $id = $options['id'] ?? cockpit('multiplane')->currentFormId; ?>

<aside class="widget contactform">
{{ $app('form')->form(cockpit('multiplane')->contact, ['id' => $id]) }} 
</aside>
