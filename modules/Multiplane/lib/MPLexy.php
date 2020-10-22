<?php
/**
 * Extend Lexy template parser to call compilers after all extensions
 * and core compilers are done.
 *
 * @see https://github.com/agentejo/cockpit/blob/next/lib/Lexy.php
 */

class MPLexy extends \Lexy {

    public $debug = false;

    protected $extensionsAfter = [];

    // construct with existing extensions from core Lexy
    public function __construct($lexy = null) {

        if ($lexy) {
            $this->extensions = $lexy->extensions;
            $this->cachePath  = $lexy->cachePath;
            $this->srcinfo    = $lexy->srcinfo;
        }

        // add compiler to the end of the list
        $this->compilers[] = 'after';

    }

    // like extend function
    public function after($compiler) {
        $this->extensionsAfter[] = $compiler;
    }

    // like compile_extensions function, but called at the end
    protected function compile_after($value) {

        foreach ($this->extensionsAfter as &$compiler) {
            $value = call_user_func($compiler, $value);
        }

        return $value;
    }

    /**
     * [file description]
     * @param  [type]  $file [description]
     * @param  array   $params  [description]
     * @param  boolean $sandbox [description]
     * @return [type]           [description]
     */
    public function file($file, $params = array(), $sandbox=false) {

        if ($this->cachePath) {

            $cachedfile = $this->get_cached_file($file, $sandbox);

            if ($cachedfile) {

                ob_start();

                lexy_include_with_params($cachedfile, $params, $file);

                $output = ob_get_clean();

                // add html comment with current template if in debug mode
                if ($this->debug) {

                    $template = \str_replace(cockpit()->path('multiplane:'), '', $file);

                    // don't add html comment before doctype
                    if (!\preg_match('/^<!doctype/i', $output)) {
                        return "<!-- START $template -->\r\n$output\r\n<!-- END $template -->\r\n";
                    }
                    else {
                        $lines = \preg_split('/\r\n|\r|\n/', $output, 2);
                        return $lines[0] . "\r\n<!-- START $template -->\r\n" . $lines[1] . "\r\n<!-- END $template -->";
                    }
                }

                return $output;
            }
        }


        return $this->execute(file_get_contents($file), $params, $sandbox, $file);
    }

}
