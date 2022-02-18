<?php

namespace MFY\libs;

/**
 * Description of Options
 *
 * @author Apon
 */
class Options {

    /**
     * Option Key 
     * @var type
     */
    protected static string $optionKey = "minify_options";
    static array $option;

    /**
     * Update Options
     * @param array $option Options Array Set
     * @return boolean
     */
    static function setOption($option) {
        self::getOption();
//        self::$option = self::mergeOption($option, self::$option);
        if (isset($option['templateAsset'])) {
            $option['templateAsset'] = array_merge(self::$option['templateAsset'], $option['templateAsset']);
        }
        self::$option = $option;
        return update_option(self::$optionKey, self::$option);
    }

    public static function default() {
        return [
            'templateBasis' => '0',
            'templateAsset' => [],
            'html' => [
                'enable' => '0'
            ],
            'css' => [
                'enable' => '0',
                'includeType' => 'external', //external, Internal
                'internal' => 'both', //if Include type Internal - both, header, footer
                'individual' => '0',
                'resource' => []
            ],
            'js' => [
                'enable' => '0',
                'includeType' => 'external', //external, Internal
                'internal' => 'both', //if Include type Internal - both, header, footer
                'individual' => '0',
                'resource' => []
            ],
        ];
    }

    /**
     * To Merge Couple Of Multi Dimensional Array 
     * @param Array $array1 Default Array
     * @param Array $array2 Option Array
     * @return Array
     */
    static Function mergeOption(array $array1, array $array2 = []) {
        $tempArry = [];

        if (is_array($array1) && count($array1) > 0) {
            foreach ($array1 as $k => $value) {
                if (is_array($value)) {
                    $childOfArray1 = isset($array2[$k]) ? $array2[$k] : [];
                    $tempArry[$k] = self::mergeOption($value, $childOfArray1);
                } else {
                    if (array_key_exists($k, $array2)) {
                        $tempArry[$k] = $array2[$k];
                    } else {
                        $tempArry[$k] = $value;
                    }
                }
            }
        } else {
            if (is_array($array2)) {
                foreach ($array2 as $k => $value) {
                    if (is_array($value)) {
                        $childOfArray1 = isset($array1[$k]) ? $array1[$k] : [];
                        $tempArry[$k] = self::mergeOption($value, $childOfArray1);
                    } else {
                        if (array_key_exists($k, $array1)) {
                            $tempArry[$k] = $array1[$k];
                        } else {
                            $tempArry[$k] = $value;
                        }
                    }
                }
            }
        }
        return $tempArry;
    }

    /**
     * Pull Options of Minify 
     * @return object
     */
    static function getOption() {
        $opt = get_option(self::$optionKey);
        if (!is_array($opt)) {
            $opt = [];
        }
        //echo "<pre>";
        //var_dump($opt);
        //return (object) $opt;
        $defOption = self::default();
        self::$option = self::mergeOption($defOption, $opt);
//        foreach ($defOption as $k => $val) {
//            $cOption = isset($opt[$k]) ? $opt[$k] : [];
//            $val = self::mergeOption($cOption, $val);
//            $mergedOption[$k] = $val;
//        }

        return (object) self::$option;
    }

    /**
     * Destroy Options
     */
    public static function destroyOption() {
        delete_option(self::$optionKey);
    }

}
