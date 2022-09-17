<?php

namespace MFY\libs;

use MFY\libs\HtmlMinify;

/**
 * Description of FrontEnd
 *
 * @author Apon
 */
class FrontEnd {

    //put your code here
    private object $option;
    private $wp_scripts;
    private $wp_styles;
    private string $pageTemplate;
    public $homePageID;

    public function __construct($option) {
        global $post, $wp_filter;
        if (!isset($wp_filter['final_output_cache'])) {
            self::FrontEndInit();
        }
        $this->option = $option;
        //$this->deQueAll();
        $this->wp_scripts = get_option('wp_scripts'); //
        $this->wp_styles = get_option('wp_styles');

        $this->homePageID = get_option('page_on_front');

        add_action('wp_enqueue_scripts', [$this, 'deQueAll'], 9995);
        add_action('wp_head', [$this, 'deQueAll'], 9995);
        //
        /**
         * if individual True then include all assets minified version
         * else
         * include common file
         * //
         * find Current temp[late
         * if Template basis then include by template  as condition of individual
         */
        if ($this->option->css['enable'] == '1') {
            if ($this->option->css['includeType'] == 'external') {
                //External Css
                if ($this->option->css['individual'] == '1') {
                    //$this->CssExternalIndividual();
                    add_action('wp_enqueue_scripts', [$this, 'CssExternalIndividualHeader'], 9999);
                    add_action('get_footer', [$this, 'CssExternalIndividualFooter'], 9999);
                } else {
                    add_action('wp_enqueue_scripts', [$this, 'CssExternalCommonHeader'], 9999);
                    add_action('get_footer', [$this, 'CssExternalCommonFooter'], 9999);
                    //$this->CssExternalCommon();
                }
            } else {
                $this->CssInternalCommon();
            }
        }
        //echo "<pre>";
        //var_dump($this->wp_scripts);
        /**
         * Including Logic of JavaScript
         */
        if ($this->option->js['enable'] == '1') {
            add_action('wp_enqueue_scripts', [$this, 'extraJS'], 9996);

            if ($this->option->js['includeType'] == 'external') {
                //External Css
                if ($this->option->js['individual'] == '1') {
                    //$this->CssExternalIndividual();
                    add_action('wp_enqueue_scripts', [$this, 'JsExternalIndividualHeader'], 9999);
                    add_action('get_footer', [$this, 'JsExternalIndividualFooter'], 9999);
                } else {
                    add_action('wp_enqueue_scripts', [$this, 'JsExternalCommonHeader'], 9999);
                    add_action('get_footer', [$this, 'JsExternalCommonFooter'], 9999);
                    //$this->CssExternalCommon();
                }
            } else {
                $this->JsInternalCommon();
            }
        }

        if ($this->option->html['enable'] == '1') {
            add_filter('final_output_cache', [$this, 'minifyHtml'], 9998);
//            ob_start();
//            add_action('shutdown', function () {
//                $final = '';
//                // We'll need to get the number of ob levels we're in, so that we can iterate over each, collecting
//                // that buffer's output into the final output.
//                $levels = ob_get_level();
//                for ($i = 0; $i < $levels; $i++) {
//                    $final .= ob_get_clean();
//                }
//                // Apply any filters to the final output
//                //echo apply_filters('final_output', $final);
//            }, 0);
        }
    }

    function minifyHtml($final) {
        return HtmlMinify::minify($final);
    }

    public static function FrontEndInit() {
        ob_start();
        add_action('shutdown', function () {
            $final = '';
            $levels = ob_get_level();
            for ($i = 0; $i < $levels; $i++) {
                $final .= ob_get_clean();
            }
            echo apply_filters('final_output_cache', $final);
        }, 0);
    }

    function templatebasisFile($type = 'css', $pos = 'header') {
        $fileName = str_replace(".php", "", $this->pageTemplate . "-" . $pos . ".$type");
        return $fileName;
    }

    function templateBasis() {
        global $post;

        $ret = false;
        if ($this->option->templateBasis == "1") {
            if ($post->ID == $this->homePageID) {
                $cTemplate = "home.php";
            } else {
                $cTemplate = get_post_meta($post->ID, '_wp_page_template', true);
            }
            if (!empty($cTemplate)) {
                $this->pageTemplate = $cTemplate;
                if (isset($this->option->templateAsset[$this->pageTemplate])) {
                    $this->templateAssets = $this->option->templateAsset[$this->pageTemplate];
                    $ret = true;
                }
            }
        }
        return $ret;
    }

    /**
     * Enqueue  Css Individual External
     */
    public function CssExternalIndividualHeader() {

        if ($this->templateBasis()) { //Template Basis
            if (isset($this->templateAssets['css']['header']) && is_array($this->templateAssets['css']['header']) &&
                    count($this->templateAssets['css']['header']) > 0) {
                foreach ($this->templateAssets['css']['header'] as $handle) {
                    $fileID = $this->option->css['resource'][$handle]['id'];
                    wp_enqueue_style($handle, __MFY_STORAGE_URL . $fileID);
                }
            }
        } else {
            foreach ($this->option->css['resource'] as $handle => $info) {
                if ($info['position'] == 'header' && isset($info['enable']) && $info['enable'] == '1') {
                    wp_enqueue_style($handle, __MFY_STORAGE_URL . $info['id']);
                }
            }
        }
    }

    /**
     * Enqueue  Css Individual External
     */
    public function CssExternalIndividualFooter() {
        if ($this->templateBasis()) { //Template Basis
            if (isset($this->templateAssets['css']['footer']) && is_array($this->templateAssets['css']['footer']) &&
                    count($this->templateAssets['css']['footer']) > 0) {
                foreach ($this->templateAssets['css']['footer'] as $handle) {
                    $fileID = $this->option->css['resource'][$handle]['id'];
                    wp_enqueue_style($handle, __MFY_STORAGE_URL . $fileID);
                }
            }
        } else {
            foreach ($this->option->css['resource'] as $handle => $info) {
                if ($info['position'] == 'footer' && isset($info['enable']) && $info['enable'] == '1') {
                    wp_enqueue_style($handle, __MFY_STORAGE_URL . $info['id']);
                }
            }
        }
    }

    function extraJS() {
        $extraHeaderjs = "/*Extra JS - MFY*/\n";
        foreach ($this->option->js['resource'] as $handle => $info) {
            if (isset($this->wp_scripts->registered[$handle]) && !empty($this->wp_scripts->registered[$handle]->deps)) {
                $depObj = $this->wp_scripts->registered[$handle];
                if (!empty($depObj->extra) && isset($depObj->extra['data'])) {
                    $extraHeaderjs .= $depObj->extra['data'] . "\n";
                }
            }
        }
        echo "\n<script id='minify-Js-extra'>$extraHeaderjs</script>\n";
    }

    /**
     * Enqueue External JavaScript Individually for Header
     */
    public function JsExternalIndividualHeader() {
        if ($this->templateBasis()) { //Template Basis
            if (isset($this->templateAssets['js']['header']) && is_array($this->templateAssets['js']['header']) &&
                    count($this->templateAssets['js']['header']) > 0) {
                foreach ($this->templateAssets['js']['header'] as $handle) {
                    $fileID = $this->option->js['resource'][$handle]['id'];
                    $dep = [];
                    if (isset($this->wp_scripts->registered[$handle]) && !empty($this->wp_scripts->registered[$handle]->deps)) {
                        $depObj = $this->wp_scripts->registered[$handle];
                        $dep = $depObj->deps;
                    }
                    wp_enqueue_script($handle, __MFY_STORAGE_URL . $fileID, $dep, false, false);
                }
            }
        } else {
            foreach ($this->option->js['resource'] as $handle => $info) {
                if ($info['position'] == 'header' && isset($info['enable']) && $info['enable'] == '1') {
                    $dep = [];
                    if (isset($this->wp_scripts->registered[$handle]) && !empty($this->wp_scripts->registered[$handle]->deps)) {
                        $depObj = $this->wp_scripts->registered[$handle];
                        $dep = $depObj->deps;
                    }
                    wp_enqueue_script($handle, __MFY_STORAGE_URL . $info['id'], $dep, false, false);
                }
            }
        }
    }

    /**
     * Enqueue External JavaScript Individually for Footer
     */
    public function JsExternalIndividualFooter() {
        if ($this->templateBasis()) { //Template Basis
            if (isset($this->templateAssets['js']['footer']) &&
                    is_array($this->templateAssets['js']['footer']) &&
                    count($this->templateAssets['js']['footer']) > 0) {
                foreach ($this->templateAssets['js']['footer'] as $handle) {
                    $fileID = $this->option->js['resource'][$handle]['id'];
                    $dep = [];
                    if (isset($this->wp_scripts->registered[$handle]) && !empty($this->wp_scripts->registered[$handle]->deps)) {
                        $depObj = $this->wp_scripts->registered[$handle];
                        $dep = $depObj->deps;
                    }
                    wp_enqueue_script($handle, __MFY_STORAGE_URL . $fileID, $dep, false, false);
                }
            }
        } else {
            foreach ($this->option->js['resource'] as $handle => $info) {
                if ($info['position'] == 'footer' && isset($info['enable']) && $info['enable'] == '1') {
                    $dep = [];
                    if (isset($this->wp_scripts->registered[$handle]) && !empty($this->wp_scripts->registered[$handle]->deps)) {
                        $dep = $this->wp_scripts->registered[$handle]->deps;
                    }
                    wp_enqueue_script($handle, __MFY_STORAGE_URL . $info['id'], $dep, false, true);
                }
            }
        }
    }

    /**
     * Enqueue  Css External common File 
     */
    public function CssExternalCommonHeader() {
        if ($this->templateBasis()) { //Template Basis
            $fileName = $this->templatebasisFile('css', 'header');
            if ($fileName != "") {
                if (file_exists(__MFY_STORAGE_DIR . $fileName)) {
                    wp_enqueue_style("header-style-template", __MFY_STORAGE_URL . $fileName);
                }
            }
        } else {
            if (file_exists(__MFY_STORAGE_DIR . "header-style.css")) {
                wp_enqueue_style("header-style", __MFY_STORAGE_URL . "header-style.css");
            }
        }
    }

    function CssExternalCommonFooter() {
        if ($this->templateBasis()) { //Template Basis
            $fileName = $this->templatebasisFile('css', 'footer');
            if ($fileName != "") {
                if (file_exists(__MFY_STORAGE_DIR . $fileName)) {
                    wp_enqueue_style("footer-style-template", __MFY_STORAGE_URL . $fileName);
                }
            }
        } else {
            if (file_exists(__MFY_STORAGE_DIR . "footer-style.css")) {
                wp_enqueue_style("footer-style", __MFY_STORAGE_URL . "footer-style.css");
            }
        }
    }

    /**
     * Enqueue  JS External common File 
     */
    public function JsExternalCommonHeader() {
        if ($this->templateBasis()) { //Template Basis
            $fileName = $this->templatebasisFile('js', 'header');
            if ($fileName != "") {
                if (file_exists(__MFY_STORAGE_DIR . $fileName)) {
                    wp_enqueue_script("footer-script-template", __MFY_STORAGE_URL . $fileName, ['jquery'], false, false);
                }
            }
        } else {
            if (file_exists(__MFY_STORAGE_DIR . "header-script.js")) {
                wp_enqueue_script('header-script', __MFY_STORAGE_URL . "header-script.js", ['jquery'], false, false);
            }
        }
    }

    public function JsExternalCommonFooter() {
        if ($this->templateBasis()) { //Template Basis
            $fileName = $this->templatebasisFile('js', 'footer');
            if ($fileName != "") {
                if (file_exists(__MFY_STORAGE_DIR . $fileName)) {
                    wp_enqueue_script("footer-script-template", __MFY_STORAGE_URL . $fileName, ['jquery'], false, true);
                }
            }
        } else {
            if (file_exists(__MFY_STORAGE_DIR . "footer-script.js")) {
                wp_enqueue_script('footer-script', __MFY_STORAGE_URL . "footer-script.js", ['jquery'], false, true);
            }
        }
    }

    /**
     * internal Css Common File
     */
    public function CssInternalCommon() {

        if ($this->option->css['internal'] == "both" || $this->option->css['internal'] == "header") {
            //---Template basis internal header Css
            add_action('wp_head', function () {
                $fileName = "header-style.css";
                if ($this->templateBasis()) {
                    $fileName = $this->templatebasisFile('css', 'header');
                }
                $headerCss = __MFY_STORAGE_DIR . $fileName;
                if (file_exists($headerCss)) {
                    echo "<style>" . file_get_contents($headerCss) . "</style>";
                }
            });
        } else {
            if ($this->option->css['individual'] == '1') {
                add_action('wp_enqueue_scripts', [$this, 'CssExternalIndividualHeader'], 9999);
            } else {
                add_action('wp_enqueue_scripts', [$this, 'CssExternalCommonHeader'], 9999);
            }
        }
        if ($this->option->css['internal'] == "both" || $this->option->css['internal'] == "footer") {
            add_action('wp_footer', function () {
                $fileName = "footer-style.css";
                if ($this->templateBasis()) {
                    $fileName = $this->templatebasisFile('css', 'footer');
                }
                $footerFile = __MFY_STORAGE_DIR . $fileName;
                if (file_exists($footerFile)) {
                    echo "<style>" . file_get_contents($footerFile) . "</style>";
                }
            });
        } else {
            if ($this->option->css['individual'] == '1') {
                add_action('get_footer', [$this, 'CssExternalIndividualFooter'], 9999);
            } else {
                add_action('get_footer', [$this, 'CssExternalCommonFooter'], 9999);
            }
        }
    }

    /**
     * internal Css Common File
     */
    public function JsInternalCommon() {
        if ($this->option->js['internal'] == "both" || $this->option->js['internal'] == "header") {
            $fileName = "header-script.js";
            if ($this->templateBasis()) { //Template Basis
                $fileName = $this->templatebasisFile('js', 'header');
            }
            $headerScriptfile = __MFY_STORAGE_DIR . $fileName;
            $intHeaderScript = "";
            if (file_exists($headerScriptfile)) {
                $intHeaderScript = file_get_contents($headerScriptfile);
            }
            if (!empty($intHeaderScript)) {
                add_action('wp_enqueue_scripts', function () {
                    wp_enqueue_script('jquery');
                }, 9999);
                add_action('wp_head', function () use ($headerScriptfile) {
                    echo "<script id='minify-header-js'>" . file_get_contents($headerScriptfile) . "</script>";
                });
            }
        } else {
            if ($this->option->js['individual'] == '1') {
                add_action('wp_enqueue_scripts', [$this, 'JsExternalIndividualHeader'], 9999);
            } else {
                add_action('wp_enqueue_scripts', [$this, 'JsExternalCommonHeader'], 9999);
            }
        }
        if ($this->option->js['internal'] == "both" || $this->option->js['internal'] == "footer") {
            $fileName = "footer-script.js";
            if ($this->templateBasis()) { //Template Basis
                $fileName = $this->templatebasisFile('js', 'footer');
            }
            $footerFile = __MFY_STORAGE_DIR . $fileName;
            $intFooterScript = "";
            if (file_exists($footerFile)) {
                $intFooterScript = file_get_contents($footerFile);
            }
            if (!empty($intFooterScript)) {
                add_action('get_footer', function () {
                    wp_enqueue_script('jquery');
                }, 1);
                add_action('wp_footer', function () use ($intFooterScript) {
                    echo "<script id='minify-footer-js'>" . $intFooterScript . "</script>";
                }, 9999);
            }
        } else {
            if ($this->option->js['individual'] == '1') {
                add_action('get_footer', [$this, 'JsExternalIndividualFooter'], 9999);
            } else {
                add_action('get_footer', [$this, 'JsExternalCommonFooter'], 9999);
            }
        }
    }

    /**
     * All Assets Remove
     */
    public function deQueAll() {
        $this->cssDeQue();
        $this->jsDeQue();
    }

    /**
     * Remove All Registered  CSS From Frontend
     */
    function cssDeQue() {
        if ($this->option->css['enable'] == "1") {
            foreach ($this->option->css['resource'] as $hndl => $vv) {
                wp_dequeue_style($hndl);
                wp_deregister_style($hndl);
            }
        }
    }

    /**
     * Remove All Registered JavaScripts from Frontend;
     */
    function jsDeQue() {
        if ($this->option->js['enable'] == "1") {
            foreach ($this->option->js['resource'] as $hndl => $val) {
                //var_dump($hndl);
                wp_dequeue_script($hndl);
                wp_deregister_script($hndl);
            }
        }
    }

}
