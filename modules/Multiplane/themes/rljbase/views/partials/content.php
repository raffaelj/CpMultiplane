
          @if(is_array($content))
            @render('views:fields/repeater.php', ['content' => $content])
          @else
            {{ $content }}
          @endif 
