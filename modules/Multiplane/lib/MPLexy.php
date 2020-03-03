<?php
/**
 * Extend Lexy template parser to call compilers after all extensions
 * and core compilers are done.
 *
 * @see https://github.com/agentejo/cockpit/blob/next/lib/Lexy.php
 */

class MPLexy extends \Lexy {

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

}
