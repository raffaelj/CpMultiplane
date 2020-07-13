<?php
 return array (
  '_id' => 'contact',
  'name' => 'contact',
  'label' => 'Contact',
  'save_entry' => true,
  'in_menu' => false,
  'email_forward' => '',
  '_created' => 1560339887,
  '_modified' => 1593346314,
  'icon' => 'paperplane.svg',
  'fields' => 
  array (
    0 => 
    array (
      'name' => 'name',
      'label' => 'Name',
      'type' => 'text',
      'default' => '',
      'info' => '',
      'group' => '',
      'localize' => false,
      'options' => 
      array (
        'validate' => 
        array (
          'type' => 
          array (
            'url' => false,
            'mail' => false,
          ),
        ),
      ),
      'width' => '1-1',
      'lst' => true,
      'acl' => 
      array (
      ),
      'required' => true,
      'validate' => true,
    ),
    1 => 
    array (
      'name' => 'phone',
      'label' => 'Telephone',
      'type' => 'text',
      'default' => '',
      'info' => '',
      'group' => '',
      'localize' => false,
      'options' => 
      array (
        'validate' => 
        array (
          'type' => 
          array (
            'url' => false,
            'phone' => true,
            'mail' => false,
          ),
        ),
      ),
      'width' => '1-2',
      'lst' => true,
      'acl' => 
      array (
      ),
      'required' => false,
      'validate' => true,
    ),
    2 => 
    array (
      'name' => 'mail',
      'label' => 'Mail Address',
      'type' => 'text',
      'default' => '',
      'info' => '',
      'group' => '',
      'localize' => false,
      'options' => 
      array (
        'validate' => 
        array (
          'type' => 
          array (
            'url' => false,
            'mail' => true,
          ),
        ),
      ),
      'width' => '1-2',
      'lst' => true,
      'acl' => 
      array (
      ),
      'required' => true,
      'validate' => true,
    ),
    3 => 
    array (
      'name' => 'subject',
      'label' => 'Subject',
      'type' => 'text',
      'default' => '',
      'info' => '',
      'group' => '',
      'localize' => false,
      'options' => 
      array (
      ),
      'width' => '1-1',
      'lst' => true,
      'acl' => 
      array (
      ),
      'required' => true,
    ),
    4 => 
    array (
      'name' => 'message',
      'label' => 'Message',
      'type' => 'textarea',
      'default' => '',
      'info' => '',
      'group' => '',
      'localize' => false,
      'options' => 
      array (
      ),
      'width' => '1-1',
      'lst' => true,
      'acl' => 
      array (
      ),
      'required' => true,
    ),
    5 => 
    array (
      'name' => 'check',
      'label' => 'Spam protection',
      'type' => 'text',
      'default' => '',
      'info' => 'Please enter the word  <strong>Robot</strong>.',
      'group' => '',
      'localize' => false,
      'options' => 
      array (
        'validate' => 
        array (
          'type' => 
          array (
            'url' => false,
            'mail' => false,
          ),
          'equalsi' => 'Robot',
        ),
      ),
      'width' => '1-1',
      'lst' => true,
      'acl' => 
      array (
      ),
      'validate' => true,
      'required' => true,
    ),
    6 => 
    array (
      'name' => 'confirm_terms_and_conditions',
      'label' => 'I agree to my personal data being stored and processed electronically.',
      'type' => 'boolean',
      'default' => '',
      'info' => '',
      'group' => '',
      'localize' => false,
      'options' => 
      array (
        'attr' => 
        array (
          'value' => 'ja',
        ),
        'link' => 
        array (
          '_id' => '5e8c5377616236b2cc0003b6',
          'link' => 'pages',
          'display' => 'Privacy Page',
          'text_before' => 'See also',
          'text_after' => '.',
        ),
      ),
      'width' => '1-1',
      'lst' => true,
      'acl' => 
      array (
      ),
      'required' => true,
      'validate' => false,
    ),
    7 => 
    array (
      'name' => 'honeypot',
      'label' => '',
      'type' => 'honeypot',
      'default' => '',
      'info' => '',
      'group' => '',
      'localize' => false,
      'options' => 
      array (
        'attr' => 
        array (
          'name' => 'confirm',
          'id' => 'confirm',
          'value' => '1',
          'style' => 'display:none !important',
          'tabindex' => '-1',
        ),
        'validate' => 
        array (
          'honeypot' => 
          array (
            'fieldname' => 'confirm',
            'expected_value' => '0',
            'response' => 'Spam bots are not welcome here.',
          ),
        ),
      ),
      'width' => '1-1',
      'lst' => true,
      'acl' => 
      array (
      ),
      'validate' => true,
    ),
    8 => 
    array (
      'name' => 'referer',
      'label' => '',
      'type' => 'text',
      'default' => '',
      'info' => '',
      'group' => '',
      'localize' => false,
      'options' => 
      array (
      ),
      'width' => '1-1',
      'lst' => false,
      'acl' => 
      array (
      ),
    ),
  ),
  'validate' => true,
  'allow_extra_fields' => false,
  'email_subject' => '[{{app.name}}] {{subject}}',
  'reply_to' => 'mail',
  'email_text_before' => 'New message on {{site_url}} from {{name}}',
  'email_text_after' => '',
  'experimental_settings' => false,
  'gui_in_header' => false,
);
