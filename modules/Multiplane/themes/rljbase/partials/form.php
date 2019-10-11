<?php $id = $options['id'] ?? mp()->currentFormId; ?>

<form id="{{ $id }}" method="post" action="@base('/form/submit/' . $form . '?anchor='.$id)">

    <fieldset>
        <legend>@lang(!empty($options['title']) ? $options['title'] : 'Contact Me')</legend>

        @if(!empty($message['error']))
        <p class="message error alarm">
            <strong>@lang('Something went wrong').</strong><br>
            {{ $message['error'] }}
        </p>
        @endif

        @if(!empty($message['success']))
        <p class="message success">@lang($message['success'])</p>
        @endif

        @if(!empty($message['notice']))
        <p class="message error">@lang($message['notice'])</p>
        @endif

    @foreach($fields as $field)
      @if(!isset($field['lst']) || $field['lst'] == true)
<?php if (isset($options['fields'][$field['name']]) && $options['fields'][$field['name']] === false) continue; ?>
        @render('views:formfields/'.($field['type'] ?? 'text').'.php with views:formfields/field-wrapper.php', ['field' => $field])

      @endif
    @endforeach

        <div><input name="{{ mp()->formSubmitButtonName }}" type="submit" value="@lang('Send')" /></div>
    </fieldset>

</form>
