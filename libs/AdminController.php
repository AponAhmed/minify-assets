<?php

namespace MFY\libs;

use \WP_Http_Curl;

/**
 * Description of AdminController
 *
 * @author Apon
 */
class AdminController {

    public object $adminView;
    public $wp_styles;
    public $wp_scripts;

    /**
     * Minify Global options
     * @var array
     */
    private object $option;

    //put your code here
    public function __construct($option) {
        $this->option = $option;
        add_action("admin_menu", [$this, "AdminMenu"]);
        add_action('admin_enqueue_scripts', [$this, 'adminScript']);
        //Ajax Actions
        add_action('wp_ajax_updateMinifyOption', [$this, 'updateMinifyOption']);
        add_action('wp_ajax_minifiGenerate', [$this, 'minifiGenerate']);
        add_action('wp_ajax_loadAssets', [$this, 'loadAssets']);
        /**
         * Template basis Ajax Callback
         */
        add_action('wp_ajax_AddNewAssetInTemplate', [$this, 'AddNewAssetInTemplate']);
        add_action('wp_ajax_GetTemplateAssets', [$this, 'GetTemplateAssets']);

        $this->adminView = new AdminView($this->option);
    }

    /**
     * Menu Register for Admin Page
     */
    function AdminMenu() {
        add_submenu_page(
                "tools.php", //$parent_slug
                "Minify", //$page_title
                "Minify", //$menu_title
                "manage_options", //$capability
                "minify", //$menu_slug
                [$this, 'minify_page_callback'] //Calback
        );
    }

    /**
     * Admin Script Init
     */
    function adminScript($hook) {
        if (strpos($hook, 'minify') !== false) {
            wp_enqueue_style('minify-admin-style', __MFY_ASSETS . 'admin-style.css');

            wp_enqueue_script('minify-admin-script', __MFY_ASSETS . 'admin-script.js', array('jquery'), '1.0');
            wp_localize_script('minify-admin-script', 'minifyObj', array('ajax_url' => admin_url('admin-ajax.php'), 'options' => $this->option));
        }
    }

    /**
     * To Update Minify Settings, its an Ajax Callback
     * @return bolean Success or Not
     */
    public function updateMinifyOption() {
        $data = array();
        parse_str($_POST['data'], $data);
        if (Options::setOption($data)) {
            $opt = Options::getOption();
            $scripts = get_option('wp_scripts'); //
            $styles = get_option('wp_styles');
            $opt->wp_scripts = $scripts;
            $opt->wp_styles = $styles;
            echo json_encode($opt);
        } else {
            echo 0;
        }
        wp_die();
    }

    /**
     * Minify File Generate Ajax CallBack
     */
    public function minifiGenerate() {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        array_map('unlink', array_filter((array) glob(__MFY_STORAGE_DIR . "*")));
        $generator = new FileGenerator($this->option);
        $res = $generator->generate();
        echo $res;
        wp_die();
    }

    function assetFilter() {
        global $wp_styles, $wp_scripts;
        $scripts = get_option('wp_scripts'); //
        $styles = get_option('wp_styles');

        if (isset($this->option->js['resource']) && count($this->option->js['resource'])) {
            $kk = array_keys($this->option->js['resource']);
            $df = array_diff($scripts->queue, $kk);
            $scripts->queue = array_merge($kk, $df);
        }
        if (isset($this->option->css['resource']) && count($this->option->css['resource'])) {
            $kk = array_keys($this->option->css['resource']);
            $df = array_diff($styles->queue, $kk);
            $styles->queue = array_merge($kk, $df);
        }
        $this->wp_scripts = $scripts;
        $this->wp_styles = $styles;
    }

    /**
     * Minify Admin page Callback Method
     * Main View of Minify Admin Settings page
     */
    public function minify_page_callback() {
        $this->assetFilter();
        $this->adminView->renderOptionView($this->wp_styles, $this->wp_scripts);
    }

    /**
     * POP Up Ajax callback to Add a New Asset 
     */
    public function AddNewAssetInTemplate() {
        $this->assetFilter();
        $ReqAstType = trim($_POST['assetType']);
        if ($ReqAstType == 'css') {
            $this->CssList();
        } else {
            $this->JsList();
        }
        wp_die();
    }

    function CssList() {
        if (isset($this->wp_styles->queue)) {
            echo "<ul class='res-list-add-able'>";
            foreach ($this->wp_styles->queue as $id) {
                if (!isset($this->wp_styles->registered[$id])) {
                    continue;
                }
                echo "<li><label data-handle='$id'>$id</label></li>";
            }
            echo "</ul>";
        } else {
            echo "<div style='padding:20px 45px 20px 20px'><button type='button' class='button button-default' onclick='loadAssets(this)'>Load Assets</button></div>";
        }
    }

    function JsList() {
        if (isset($this->wp_scripts->queue)) {
            echo "<ul class='res-list-add-able jsList'>";
            foreach ($this->wp_scripts->queue as $id) {
                if (!isset($this->wp_scripts->registered[$id])) {
                    continue;
                }
                echo "<li><label data-handle='$id'>$id</label></li>";
            }
            echo "</ul>";
        } else {
            echo "<div style='padding:20px 45px 20px 20px'><button type='button' class='button button-default' onclick='loadAssets(this)'>Load Assets</button></div>";
        }
    }

    public function GetTemplateAssets() {
        $template = $_POST['template'];
        $data = [];
        if (isset($this->option->templateAsset[$template])) {
            $data = $this->option->templateAsset[$template];
        }
        echo json_encode($data);
        wp_die();
    }

    /**
     * Load Frontend Assets, Ajax callback
     */
    function loadAssets() {
//        ini_set('display_errors', 1);
//        ini_set('display_startup_errors', 1);
//        error_reporting(E_ALL);
        $siteUrl = get_site_url();
        $wpHttp = new WP_Http_Curl();
        $wpHttp->request($siteUrl);
        wp_die();
    }

}
