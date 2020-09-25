
        @if(is_array($content))
          @if(isset($content[0]['value']))
            @render('views:fields/repeater.php', ['content' => $content])
          @elseif(isset($content[0]['component']))
            @render('views:fields/layout.php', ['content' => $content])
          @endif
        @elseif(is_string($content))
            {{ $content }}
        @endif 
