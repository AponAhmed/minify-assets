<?php

namespace MFY\libs;

use MatthiasMullie\Minify;

/**
 * Description of JsMinify
 *
 * @author Apon
 */
class JsMinify {

    //put your code here
    public function __construct() {
        
    }

    static function minify($js) {
        $minifier = new Minify\JS($js);
        return $minifier->minify();
    }

}
