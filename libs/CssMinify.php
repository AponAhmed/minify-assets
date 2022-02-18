<?php

namespace MFY\libs;

use MatthiasMullie\Minify;

/**
 * Description of CssMinify
 *
 * @author Apon
 */
class CssMinify {

    Private static string $siteUrl;

    //put your code here
    static function minify($cssFileContent, $FileDir = false) {
        self::$siteUrl = get_site_url();
        $cssFileContent = preg_replace('/(?!<\")\/\*[^\*]+\*\/(?!\")/', ' ', $cssFileContent);
        $cssFileContent = self::staticAssetPathFix($cssFileContent, $FileDir);
        //return $cssFileContent;
        $minifier = new Minify\CSS($cssFileContent);
        $css = $minifier->minify();
        return $css;
        //return self::afterFilter($css);
    }

    static function staticAssetPathFix($cssFileContent, $FileDir) {
        //echo "<pre>";
        $re = '@url\(([^\)]*)@m';
        $FileDir = self::$siteUrl . "/" . $FileDir;
        $FileDir = preg_replace('/([^:])(\/{2,})/', '$1/', $FileDir);

        $dir = pathinfo($FileDir);
        $filePathArr = explode("/", $FileDir);
        //var_dump($filePathArr);
        $tParts = count($filePathArr);

        preg_match_all($re, $cssFileContent, $matches, PREG_SET_ORDER, 0);
        if (count($matches) > 0) {
            $findArray = array();
            $RepArray = array();
            foreach ($matches as $inf) {
                //$url = $inf[1];
                $url = str_replace(array('"', "'"), "", $inf[1]);
                if (!filter_var($url, FILTER_VALIDATE_URL)) {
                    //If Static Resource URL not Valid 
                    $filePathArrRes = explode("/", $url);
                    $vals = array_count_values($filePathArrRes);

                    $prfx = $dir['dirname'] . "/";
                    $resFile = $url;
                    if (isset($vals['..'])) {
                        $cutStr = "";
                        $cutResStr = "";
                        for ($i = $vals['..']; $i >= 1; $i--) {
                            $indx = ($tParts - 1) - $i;
                            //var_dump($indx);
                            $cutStr .= $filePathArr[$indx] . "/";
                            $cutResStr .= '../';
                        }
                        $prfx = str_replace($cutStr, "", $prfx);
                        $resFile = str_replace($cutResStr, "", $url);
                    }
                    $ResorceNewUrl = $prfx . $resFile;
                    $ResorceNewUrl = str_replace("./", "", $ResorceNewUrl);
                    $ResorceNewUrl = preg_replace('/([^:])(\/{2,})/', '$1/', $ResorceNewUrl);
                    $findArray[] = $url;
                    $RepArray[] = $ResorceNewUrl;
                }
            }
            //var_dump($findArray, $RepArray);
            $cssFileContent = str_replace($findArray, $RepArray, $cssFileContent);
        }
        return $cssFileContent;
    }

    public static function afterFilter($css) {
        $re = '@url\(([^\)]*)@m';
        preg_match_all($re, $css, $matches, PREG_SET_ORDER, 0);
        if (count($matches) > 0) {
            $findArray = array();
            $RepArray = array();
            foreach ($matches as $inf) {
                $url = $inf[1];
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    $findArray[] = $url;
                    $RepArray[] = "'$url'";
                }
            }
            $css = str_replace($findArray, $RepArray, $css);
        }
        return $css;
    }

}
