/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2022 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */
jQuery(window).on("load",(function(){var e=jQuery(".sp-simpleportfolio-items"),s=e.find(".shuffle__sizer");e.shuffle({itemSelector:".sp-simpleportfolio-item",sequentialFadeDelay:150,sizer:s}),jQuery(".sp-simpleportfolio-filter li a").on("click",(function(e){e.preventDefault();var s=jQuery(this),i=jQuery(this).parent();i.hasClass("active")||(s.closest("ul").children().removeClass("active"),s.parent().addClass("active"),s.closest(".sp-simpleportfolio").children(".sp-simpleportfolio-items").shuffle("shuffle",i.data("group")))}))}));