<?php

namespace MFY\libs;

/**
 * Description of AdminView
 *
 * @author Apon
 */
class AdminView {

    /**
     * Minify Global options
     * @var array
     */
    private object $option;
    private static array $minifyAble = ['CSS', 'JavaScript', 'HTML', 'Template'];

    public function __construct($option = object) {
        $this->option = $option;
//        $this->wp_styles = $styles;
//        $this->wp_scripts = $scripts;
    }

    static function title() {
        return "<h1>" . get_admin_page_title() . "</h1><hr>";
    }

    function Template() {
        ob_start();
        $themeObj = wp_get_theme();
        $templates = $themeObj->get_page_templates();
        ?>
        <div class="template-assets-manager" >
            <nav class="templateAsset-tab">
                <a href="#templateCss" class="active">CSS</a>
                <a href="#templateJs">JS</a>
                &nbsp;&nbsp;&nbsp;<label><input type="checkbox" name="templateBasis" value="1" <?php echo $this->option->templateBasis == '1' ? "checked" : "" ?>>&nbsp;Enable</label>
                <div class="template-select-area">
                    <select id='pageTemplateSel' onchange="GetTemplateAssets(this)" autocomplete="false">
                        <option value="" selected>Select Template</option>
                        <option value="home.php">Home Page</option>
                        <?php
                        foreach ($templates as $file => $name) {
                            echo "<option value='$file'>$name</option>";
                        }
                        ?>
                    </select>
                </div>
            </nav>
            <div id="templateCss" class="template-asset-tab-pan active">
                <div class="asset-wrap">
                    <div class="asset-wrap-header asset-wrap-inner">
                        <ul id="assets-css-header" class="shortable template-header-assets template-assets-css"></ul>
                        <span class="AddNewAsset" data-type="css" data-pos="header">+</span>
                    </div>
                    <div class="asset-wrap-footer asset-wrap-inner">
                        <ul id="assets-css-footer" class="shortable template-footer-assets template-assets-css "></ul>
                        <span class="AddNewAsset" data-type="css" data-pos="footer">+</span>
                    </div>
                </div>
            </div>
            <div id="templateJs" class="template-asset-tab-pan">
                <div class="asset-wrap">
                    <div class="asset-wrap-header asset-wrap-inner">
                        <ul id="assets-js-header" class="shortable template-header-assets template-assets-js"></ul>
                        <span class="AddNewAsset" data-type="js" data-pos="header">+</span>
                    </div>
                    <div class="asset-wrap-footer asset-wrap-inner">
                        <ul id="assets-js-footer" class="shortable template-footer-assets template-assets-js"></ul>
                        <span class="AddNewAsset" data-type="js" data-pos="footer">+</span>
                    </div>
                </div>
            </div>
            <?php
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * HTML Tab HTML
     * @return HTML
     */
    public function HTML() {
        ob_start();
        ?>
        <div class="minify-option html-minify">
            <div class="option-wrap">
                <label>Enable</label>
                <select name="html[enable]">
                    <option value="1" <?php echo $this->option->html['enable'] == "1" ? "selected" : "" ?>>True</option>
                    <option value="0" <?php echo $this->option->html['enable'] == "0" ? "selected" : "" ?>>False</option>
                </select>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * CSS Tab HTML
     * @return HTML
     */
    public function CSS() {
        ob_start();
        ?>
        <div class="opt-wrap">
            <div class="minify-option html-minify">
                <div class="option-wrap">
                    <label>Enable</label>
                    <select name="css[enable]" class="custom-select custom-select-sm w80">
                        <option value="1" <?php echo $this->option->css['enable'] == "1" ? "selected" : "" ?>>True</option>
                        <option value="0" <?php echo $this->option->css['enable'] == "0" ? "selected" : "" ?>>False</option>
                    </select>
                </div>
                <div class="option-wrap">
                    <label>Type</label>
                    <select name="css[includeType]" class="custom-select custom-select-sm w80" onchange="jQuery(this).val() == 'internal' ? jQuery('#internalCss').show() : jQuery('#internalCss').hide()">
                        <option value="external" <?php echo $this->option->css['includeType'] == 'external' ? "selected" : "" ?>>External</option>
                        <option value="internal" <?php echo $this->option->css['includeType'] == 'internal' ? "selected" : "" ?>>Internal</option>
                    </select>
                    <select 
                        id="internalCss" 
                        name="css[internal]" 
                        class="custom-select custom-select-sm w80 <?php echo ($this->option->css['includeType'] == 'external' || $this->option->css['includeType'] = "") ? "collapse" : "" ?>">
                        <option value="both" <?php echo $this->option->css['internal'] == 'both' ? "selected" : "" ?>>Both</option>
                        <option value="header" <?php echo $this->option->css['internal'] == 'header' ? "selected" : "" ?>>Header CSS</option>
                        <option value="footer" <?php echo $this->option->css['internal'] == 'footer' ? "selected" : "" ?>>Footer CSS</option>
                    </select> 
                </div>
                <div class="option-wrap">
                    <label>Individual</label>
                    <select name="css[individual]" class="custom-select custom-select-sm w80">
                        <option value="1" <?php echo $this->option->css['individual'] == "1" ? "selected" : "" ?>>True</option>
                        <option value="0" <?php echo $this->option->css['individual'] == "0" ? "selected" : "" ?>>False</option>
                    </select>
                </div> 
            </div>
            <div class="assets-wrap">
                <ul class="shortable assets-list">
                    <?php $this->AssetsView(); ?>
                </ul>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * JavaScript Tab HTML
     * @return HTML
     */
    public function JavaScript() {
        ob_start();
        ?>
        <div class="opt-wrap">
            <div class="minify-option html-minify">
                <div class="option-wrap">
                    <label>Enable</label>
                    <select name="js[enable]" class="custom-select custom-select-sm w80">
                        <option value="1" <?php echo $this->option->js['enable'] == "1" ? "selected" : "" ?>>True</option>
                        <option value="0" <?php echo $this->option->js['enable'] == "0" ? "selected" : "" ?>>False</option>
                    </select>
                </div>
                <div class="option-wrap">
                    <label>Type</label>
                    <select  name="js[includeType]" class="custom-select custom-select-sm w80"  onchange="jQuery(this).val() == 'internal' ? jQuery('#internalJs').show() : jQuery('#internalJs').hide()">
                        <option value="external" <?php echo $this->option->js['includeType'] == 'external' ? "selected" : "" ?>>External</option>
                        <option value="internal" <?php echo $this->option->js['includeType'] == 'internal' ? "selected" : "" ?>>Internal</option>
                    </select>
                    <select 
                        id="internalJs" 
                        name="js[internal]" 
                        class="custom-select custom-select-sm w80
                        <?php echo ($this->option->js['includeType'] == 'external' || $this->option->js['includeType'] = "") ? "collapse" : "" ?>">
                        <option value="both" <?php echo $this->option->js['internal'] == 'both' ? "selected" : "" ?>>Both</option>
                        <option value="header" <?php echo $this->option->js['internal'] == 'header' ? "selected" : "" ?>>Header JS</option>
                        <option value="footer" <?php echo $this->option->js['internal'] == 'footer' ? "selected" : "" ?>>Footer JS</option>
                    </select> 
                </div>
                <div class="option-wrap">
                    <label>Individual</label>
                    <select name="js[individual]" class="custom-select custom-select-sm w80">
                        <option value="1" <?php echo $this->option->js['individual'] == "1" ? "selected" : "" ?>>True</option>
                        <option value="0" <?php echo $this->option->js['individual'] == "0" ? "selected" : "" ?>>False</option>
                    </select>
                </div> 
            </div>
            <div class="assets-wrap">
                <ul class="shortable assets-list">
                    <?php $this->AssetsView(); ?>
                </ul>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function actionBtn() {
        ob_start();
        ?>
        <button type='button' class='button button-primary' onclick='updateMinifyOption(this)'>Update Settings</button>&nbsp;&nbsp;
        <button type='button' class='button button-primary' onclick='minifiGenerate(this)'>Generate</button>
        <?php
        return ob_get_clean();
    }

    /**
     * Minify All Stages Binding
     * 
     */
    public function minifyView() {
        //$this->assetFilter();
        //echo "<pre>";
        //var_dump($this->option);
        //echo "</pre>";

        $tab = "<nav class=\"nav-tab-wrapper minify-tab\">";
        $tabContent = "<div class='tab-content'>";
        $n = 0;
        foreach (self::$minifyAble as $part) {
            $this->type = $part;
            if (method_exists($this, $part)) {
                $n++;
                $firstElemet = "";
                if ($n == 1) {
                    $firstElemet = "nav-tab-active";
                }
                $tab .= "<a href=\"#minifyOption$part\" class=\"nav-tab $firstElemet\">$part</a>";
                $tabContent .= "<div id=\"minifyOption$part\" class='tab-pane'>" . $this->$part() . "</div>";
            }
        }
        $actBtns = self::actionBtn();
        $tab .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$actBtns<strong class='minify-tab-title'>Minify Option</strong></nav>";
        $tabContent .= "</div>";

        echo "<form ><div class='tab-wrap'>";
        echo $tab;
        echo $tabContent;
        echo "</div>";
        echo "<hr><button type='button' class='button button-primary' onclick='updateMinifyOption(this)'>Update Settings</button>";
        echo "</form>";
    }

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
        $path = $this->dependency->src;
        if (strpos($this->dependency->src, $this->site_url) !== false) {
            $path = str_replace($this->site_url, "", $this->dependency->src);
        }

        $path = preg_replace('/([^:])(\/{2,})/', '$1/', $path);
        $path = str_replace(["https://", '/'], ["", "-"], trim($path, "/"));
        return trim($path);
    }

    /**
     * Assets List
     * @global object $wp_styles
     */
    public function AssetsView() {
        //echo "<pre>";
        if ($this->type == "CSS") {

            if (is_a($this->wp_styles, 'WP_Styles')) {
                //var_dump($this->wp_styles->queue);
                foreach ($this->wp_styles->queue as $id) {
                    if (!isset($this->wp_styles->registered[$id])) {
                        continue;
                    }
                    $this->dependency = $this->wp_styles->registered[$id];
                    if (!$this->wp_styles->registered[$id]->src)
                        continue;
                    $pathID = $this->filePath();
                    //$pathID = isset($this->option->css['resource'][$id]['id']) ? $this->option->css['resource'][$id]['id'] : $pathID;
                    ?>
                    <li>
                        <div class="asset-item css-asset">
                            <label title="<?php echo $this->dependency->src ?>">
                                <input name="css[resource][<?php echo $id ?>][enable]" <?php echo isset($this->option->css['resource'][$id]['enable']) && $this->option->css['resource'][$id]['enable'] == '1' ? "checked" : "" ?> value="1" type="checkbox">
                                <?php
                                echo "<a href='" . $this->dependency->src . "' target='_new'>$id</a>";
                                //echo "<pre>";
                                //var_dump($this->option->css['resource'][$id]['id']);
                                echo "<input type='hidden' name='css[resource][$id][id]' value='$pathID'>";
                                ?>
                            </label>

                            <div class="asset-control">
                                <select name="css[resource][<?php echo $id ?>][position]">
                                    <option <?php echo isset($this->option->css['resource'][$id]['position']) && $this->option->css['resource'][$id]['position'] == 'header' ? "selected" : "" ?> value="header">Header</option>
                                    <option  <?php echo isset($this->option->css['resource'][$id]['position']) && $this->option->css['resource'][$id]['position'] == 'footer' ? "selected" : "" ?> value="footer">Footer</option>
                                </select>
                            </div>
                        </div>
                    </li>
                    <?php
                }
            } else {
                echo "<button type='button' class='button button-default' onclick='loadAssets(this)'>Load Assets</button>";
            }
        }

        if ($this->type == "JavaScript") {
            if (is_a($this->wp_scripts, 'WP_Scripts')) {
                foreach ($this->wp_scripts->queue as $id) {
                    if (!isset($this->wp_scripts->registered[$id])) {
                        continue;
                    }
                    $this->dependency = $this->wp_scripts->registered[$id];
                    if (!$this->wp_scripts->registered[$id]->src)
                        continue;
                    $pathID = $this->filePath();
                    //$pathID = isset($this->option->js['resource'][$id]['id']) ? $this->option->js['resource'][$id]['id'] : $pathID;
                    ?>
                    <li>
                        <div class="asset-item css-asset">
                            <label>
                                <input name="js[resource][<?php echo $id ?>][enable]" <?php echo isset($this->option->js['resource'][$id]['enable']) && $this->option->js['resource'][$id]['enable'] == '1' ? "checked" : "" ?> value="1" type="checkbox">
                                <?php
                                echo "<a href='" . $this->dependency->src . "' target='_new'>$id</a>";
//                                echo "<pre>";
//                                var_dump($this->option->js['resource'][$id]);
//                                echo "</pre>";
                                //var_dump($this->option->js['resource'][$id]['id']);
                                echo "<input type='hidden' name='js[resource][$id][id]' value='$pathID'>";
                                ?>
                            </label>
                            <div class="asset-control">
                                <select name="js[resource][<?php echo $id ?>][position]">
                                    <option <?php echo isset($this->option->js['resource'][$id]['position']) && $this->option->js['resource'][$id]['position'] == 'header' ? "selected" : "" ?> value="header">Header</option>
                                    <option  <?php echo isset($this->option->js['resource'][$id]['position']) && $this->option->js['resource'][$id]['position'] == 'footer' ? "selected" : "" ?> value="footer">Footer</option>
                                </select>
                            </div>
                        </div>
                    </li>
                    <?php
                }
            } else {
                echo "<button type='button' class='button button-default' onclick='loadAssets(this)'>Load Assets</button>";
            }
        }
    }

    /**
     * Minify Admin HTML Render
     * @return HTML of Admin View
     */
    public function renderOptionView($styles, $scripts) {
        $this->wp_scripts = $scripts;
        $this->wp_styles = $styles;
        echo "<div class='wrap'>";
        echo self::title();
        $this->minifyView();
        echo "</div>";
    }

}
