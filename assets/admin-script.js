//console.log(minifyObj);//Localized Global object

jQuery(document).ready(function ($) {
    $("#pageTemplateSel").val("");
    $('.shortable').sortable({
        connectWith: ".filter-fields-list"
    });
    $(".templateAsset-tab a").click(function (event) {
        event.preventDefault();
        let targetClickd = $(event.target);
        let targetElm = targetClickd.attr('href');
        if ($(targetElm).length > 0) {
            $(targetClickd).parent().find('a').removeClass('active');
            targetClickd.addClass('active');
            $(".template-asset-tab-pan").removeClass("active");
            $(targetElm).addClass('active');
        }
    });

    /**
     * Add New Asset In Template Block Hookup
     */
    $(".AddNewAsset").click(function (event) {
        let clickedEl = $(event.target);
        let typ = $(clickedEl).attr('data-type');
        let pos = $(clickedEl).attr('data-pos');
        let tmp = $("#pageTemplateSel").val();
        console.log(typ);
        var data = {action: "AddNewAssetInTemplate", 'assetType': typ};
        let optData = JSON.parse(localStorage.getItem('minifyOptions'));

        jQuery.post(minifyObj.ajax_url, data, function (response) {
            $('body').append('<div class="new-asset-popup">' + response + '<span class="add-asset-close" onclick="jQuery(this).parent().remove()">×</span></div>');
            $(".res-list-add-able li label").click(function () {
                let hndl = $(this).attr("data-handle");
                let Dep;
                if (optData) {
                    Dep = optData.wp_scripts.registered[hndl];
                    if (typ == 'css') {
                        Dep = optData.wp_styles.registered[hndl];
                    }
                } else {
                    Dep = {src: ""}
                }
                let htmItem = "<li class='asset-item " + typ + "-asset' title='" + Dep.src + "'><a href='" + Dep.src + "' target='_new'>" + hndl + "</a><input name='templateAsset[" + tmp + "][" + typ + "][" + pos + "][]' type='hidden' value=\"" + hndl + "\"><span onclick='jQuery(this).parent().remove()' class='remove-temp-asset'>×</span></li>";
                clickedEl.closest('.asset-wrap-inner').find('ul').append(htmItem);
            })
        });
    });

});
/**
 * Update Minify Option
 * @param {DOM Object} _this
 * @returns {void}
 */
function updateMinifyOption(_this) {
    let btn = jQuery(_this);
    btn.find(".dashicons").remove();
    btn.prepend('<span class="dashicons dashicons-update loading"></span>');
    var data = {action: "updateMinifyOption", data: jQuery(_this).closest('form').serialize()};
    jQuery.post(minifyObj.ajax_url, data, function (response) {
        btn.find('.loading').removeClass('loading dashicons-update').addClass('dashicons-saved');
        if (response != 0) {
            if (typeof (localStorage) != 'undefined') {
                localStorage.setItem('minifyOptions', response);
            }
        }

    });
}

/**
 * Generate Minify Files
 * @param {DOM Object} _this
 * @returns {void}
 */
function minifiGenerate(_this) {
    let btn = jQuery(_this);
    btn.find(".dashicons").remove();
    btn.prepend('<span class="dashicons dashicons-update loading"></span>');
    var data = {action: "minifiGenerate"};
    jQuery.post(minifyObj.ajax_url, data, function (response) {
        btn.find('.loading').removeClass('loading dashicons-update').addClass('dashicons-saved');
        console.log(response);
    });
}
/**
 * 
 * @param {DOM Object} _this Select element
 * @returns {undefined}
 */
function GetTemplateAssets(_this) {
    //let select = jQuery(_this);
    //select.find(".dashicons").remove();
    // btn.prepend('<span class="dashicons dashicons-update loading"></span>');
    let tmplt = jQuery("#pageTemplateSel").val();
    jQuery(".asset-wrap-inner ul").html("");
    var data = {action: "GetTemplateAssets", template: tmplt};
    let optData = JSON.parse(localStorage.getItem('minifyOptions'));
    jQuery.post(minifyObj.ajax_url, data, function (response) {
        let resObj = JSON.parse(response);
        if (resObj) {
            for (var typ in resObj) {
                let asstType = resObj[typ];
                for (var pos in asstType) {
                    let pAssets = asstType[pos];
                    let wrpID = `#assets-${typ}-${pos}`;
                    for (var itm in pAssets) {
                        let hndl = pAssets[itm];
                        let Dep;
                        if (optData) {
                            Dep = optData.wp_scripts.registered[hndl];
                            if (typ == 'css') {
                                Dep = optData.wp_styles.registered[hndl];
                            }
                        } else {
                            Dep = {src: ""}
                        }

                        //console.log(Dep);
                        jQuery(wrpID).append(`<li class="asset-item ${typ}-asset" title='${Dep.src}'><a href='${Dep.src}' target='_new'>${hndl}</a><input name="templateAsset[${tmplt}][${typ}][${pos}][]" type="hidden" value="${hndl}"><span class='remove-temp-asset' onclick='jQuery(this).parent().remove()'>×</span></li>`);
                    }
                }
            }
        }
        //select.find('.loading').removeClass('loading dashicons-update').addClass('dashicons-saved');
    });
}
/**
 * Load Frontend Asset as Wp_scripts or Wp_styles
 * just a ajax Request to frontend
 * @returns {void}
 */
function loadAssets(_this) {
    let btn = jQuery(_this);
    btn.find(".dashicons").remove();
    btn.prepend('<span class="dashicons dashicons-update loading"></span>');
    var data = {action: "loadAssets"};
    jQuery.post(minifyObj.ajax_url, data, function (response) {
        btn.find('.loading').removeClass('loading dashicons-update').addClass('dashicons-saved');
        window.location.reload();
    });
}


function checkAllEnable(_this) {
    if (jQuery(_this).is(":checked")) {
        console.log('checked');
        jQuery(_this).closest('.assets-wrap').find('.enableCheck').each(function () {
            jQuery(this).prop('checked', true);
        });
    } else {
        console.log('Unchecked');
        jQuery(_this).closest('.assets-wrap').find('.enableCheck').each(function () {
            jQuery(this).prop('checked', false);
        });
    }
}