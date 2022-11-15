<?php

/**
 * Plugin Name: Minify
 * Plugin URI: https://siatexltd.com/wp/plugins/minify
 * Description: To Minify And Manage all of CSS and JavaScripts assets.
 * Author: SiATEX
 * Author URI: https://www.siatex.com
 * Version: 2.2.4
 * Text Domain: mfy-minify-assets;
 */

namespace MFY;

use MFY\libs\AdminController;
use MFY\libs\Options;
use MFY\libs\FrontEnd;
use MFY\libs\FileGenerator;

//Plugin Config
define('__MFY_DIR', dirname(__FILE__));
define('__MFY_ASSETS', plugin_dir_url(__FILE__) . "assets/");
define('__MFY_STORAGE_DIR', WP_CONTENT_DIR . "/minify/");
define('__MFY_STORAGE_URL', WP_CONTENT_URL . "/minify/");
//Autoloader 
require 'vendor/autoload.php';

/**
 * To Minify all of assets like Css and JavaScripts
 *
 * @author Apon
 */
class Minify {

    public object $option;

    //put your code here
    public function __construct() {
        $this->option = Options::getOption();
        if (is_admin()) {
            $this->adminController = new AdminController($this->option);
        } else {
            $this->frontEnd = new FrontEnd($this->option);
        }
        register_deactivation_hook(__FILE__, [$this, 'minify_Unstall']);
        //add_action('style_loader_tag', [$this, 'styleTag'], 100, 4);
    }

    private function setMinifier() {
        $this->JsMinify = new JsMinify($this->option);
        $this->CssMinify = new CssMinify($this->option);
    }

    public static function init() {
        return new Minify();
    }

    function minify_Unstall() {
        delete_option('wp_styles');
        delete_option('wp_styles');
        Options::destroyOption();
        $this->rrmdir(__MFY_STORAGE_DIR);
    }

    function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . "/" . $object))
                        $this->rrmdir($dir . DIRECTORY_SEPARATOR . $object);
                    else
                        unlink($dir . DIRECTORY_SEPARATOR . $object);
                }
            }
            rmdir($dir);
        }
    }

}

function deQueAll() {
    $styles = wp_styles();
    $scripts = wp_scripts();
    delete_option('wp_styles');
    delete_option('wp_scripts');
    update_option('wp_styles', $styles);
    update_option('wp_scripts', $scripts);
}

add_action('init', function () {
    if (!is_admin()) {
        add_action('wp_enqueue_scripts', 'MFY\deQueAll', 9990);
        add_action('wp_enqueue_styles', 'MFY\deQueAll', 9990);
    }
});

function hide_admin_bar_from_front_end() {
    if (is_blog_admin()) {
        return true;
    }
    return false;
}

add_filter('show_admin_bar', 'MFY\hide_admin_bar_from_front_end');

add_action('init', function () {
    $minify = Minify::init();
});

