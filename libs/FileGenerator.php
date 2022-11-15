<?php

namespace MFY\libs;

use MFY\libs\JsMinify;
use MFY\libs\CssMinify;
use MatthiasMullie\Minify;

/**
 * Description of FileGenerator
 *
 * @author Apon
 */
class FileGenerator {

    //put your code here
    private object $option;
    private array $assets;
    public object $dependency;

    public function __construct($option = object) {
        $this->option = $option;

        $this->wp_scripts = get_option('wp_scripts'); //
        $this->wp_styles = get_option('wp_styles');
        //Create Directory if Not Exists
        if (!is_dir(__MFY_STORAGE_DIR)) {
            mkdir(__MFY_STORAGE_DIR, 777, true);
            chmod(__MFY_STORAGE_DIR, 0777);
        }

        $this->assetDistribute();
    }

    /**
     * Assets Distribute
     */
    function assetDistribute() {
        //CSS
        $this->assets['css'] = [
            'header' => [],
            'footer' => []
        ];
        if ($this->option->css['enable'] == "1") {

            foreach ($this->option->css['resource'] as $handle => $css) {
                if (!isset($css['enable'])) {
                    continue;
                }
                if ($css['position'] == 'header') {
                    $this->assets['css']['header'][] = $handle;
                } else {
                    $this->assets['css']['footer'][] = $handle;
                }
            }
        }
        //JS
        $this->assets['js'] = [
            'header' => [],
            'footer' => []
        ];
        if ($this->option->js['enable'] == "1") {
            foreach ($this->option->js['resource'] as $handle => $js) {
                if (!isset($js['enable'])) {
                    continue;
                }
                if ($js['position'] == 'header') {
                    $this->assets['js']['header'][] = $handle;
                } else {
                    $this->assets['js']['footer'][] = $handle;
                }
            }
        } else {
            $this->assets['js'] = [];
        }
    }

    /**
     * 
     * @return string Path
     */
    function filePath() {
        $https = false;
        $this->site_url = get_site_url();
        //SSL
        if (strpos($this->site_url, 'https://') !== false) {
            $https = true;
        }
        //SSL Fix
        if (!$https) {
            $this->dependency->src = str_replace("https://", "http://", $this->dependency->src);
        }

        $arr = [
            'local' => false,
            'path' => $this->dependency->src,
        ];
        if (strpos($this->dependency->src, $this->site_url) !== false) {
            $arr['local'] = true;
            $arr['path'] = str_replace($this->site_url, "", $this->dependency->src);
        }
        $this->dep_info = $arr;
    }

    /**
     * Get Content of File
     */
    function getContent() {
        $fileContent = "";
        if ($this->dependency && $this->dep_info) {
            if ($this->dep_info['local']) {
                $path = ABSPATH . $this->dep_info['path'];
                $path = preg_replace('/([^:])(\/{2,})/', '$1/', $path);
            } else {
                $path = $this->dep_info['path'];
            }
            //var_dump($path);
            if (!empty($path)) {
                $fileContent = file_get_contents($path);
            }
        }
        return $fileContent;
    }

    /**
     * Generate Files
     * @return boolean
     */
    public function generate() {
        //echo "<pre>";
        $asset_exmple = [
            'css' => [
                'header' => [],
                'footer' => [],
            ],
            'js' => [
                'header' => [],
                'footer' => [],
            ]
        ];
        //Exmple for Development purpose

        foreach ($this->assets['css'] as $position => $arr) {
            //if(isset($this->wp_styles))
            $filenameAll = "$position-style.css";
            $css = "";
            if (is_array($arr)) {
                foreach ($arr as $handle) {
                    if (isset($this->wp_styles->registered[$handle])) {
                        $this->dependency = $this->wp_styles->registered[$handle];
                        $this->filePath();
                        var_dump($handle);
                        $singleFileContent = CssMinify::minify($this->getContent(), $this->dep_info['path']);
                        if (empty($singleFileContent)) {
                            continue;
                        }

                        $singleFileName = $this->option->css['resource'][$handle]['id'];
                        file_put_contents(__MFY_STORAGE_DIR . $singleFileName, $singleFileContent);

                        //$css .= "\n/*$handle*/";
                        $css .= $singleFileContent;
                    }
                }
            }
            if (!empty($css)) {
                $minifier = new Minify\CSS($css);
                $css = $minifier->minify();
                file_put_contents(__MFY_STORAGE_DIR . $filenameAll, $css);
            }
        }

        foreach ($this->assets['js'] as $position => $arr) {
            //if(isset($this->wp_styles))
            $filenameAll = "$position-script.js";
            $jsCommon = "";
            if (is_array($arr)) {
                foreach ($arr as $handle) {
                    if (isset($this->wp_scripts->registered[$handle])) {
                        $this->dependency = $this->wp_scripts->registered[$handle];
                        $this->filePath();

                        $singleFileScript = JsMinify::minify($this->getContent());
                        if (empty($singleFileScript)) {
                            continue;
                        }

                        $singleFileName = $this->option->js['resource'][$handle]['id'];
                        file_put_contents(__MFY_STORAGE_DIR . $singleFileName, $singleFileScript);

                        $jsCommon .= "\n/*$handle*/\n";
                        $jsCommon .= $singleFileScript;
                    }
                }
            }
            if (!empty($jsCommon)) {
                file_put_contents(__MFY_STORAGE_DIR . $filenameAll, $jsCommon);
            }
        }
        $this->generateTemplateAsset();

        return true;
    }

    function generateTemplateAsset() {
        if (!empty($this->option->templateAsset)) {
            //echo "<pre>";
            //var_dump($this->option->templateAsset);
            //exit;
            $templateAssets = $this->option->templateAsset;
            foreach ($templateAssets as $template => $assets) {
                foreach ($assets as $typ => $asstPosAr) {
                    foreach ($asstPosAr as $pos => $items) {
                        $fileName = str_replace(".php", "", $template . "-" . $pos . ".$typ");
                        $comonFileContent = "";
                        foreach ($items as $handle) {

                            if ($typ == 'css') {
                                $ssStor = $this->wp_styles;
                            } else {
                                $ssStor = $this->wp_scripts;
                            }
                            if (isset($ssStor->registered[$handle])) {
                                $this->dependency = $ssStor->registered[$handle];
                                //echo $handle;
                                $this->filePath();
                                if ($typ == 'css') {
                                    $singleFileScript = CssMinify::minify($this->getContent());
                                } else {
                                    $singleFileScript = JsMinify::minify($this->getContent());
                                }
                                $comonFileContent .= "\n/*$handle*/";
                                $comonFileContent .= $singleFileScript;
                            }
                        }
                        if (!empty($comonFileContent)) {
                            if ($typ == 'css') {
                                $minifier = new Minify\CSS($comonFileContent);
                                $comonFileContent = $minifier->minify();
                            }
                            file_put_contents(__MFY_STORAGE_DIR . $fileName, $comonFileContent);
                        }
                    }
                }
            }
        }
    }

}
