<?php
defined( '_JEXEC' ) or die( 'Restricted index access' );

// jQuery
$wa->useScript('template.jquery');

// Mobile Menu
if($mobile_menu_type == "slicknav") {
    if($this->params->get('hornavPosition') == 'slicknav') { 
        if ($this->countModules( 'hornav' )) :
            $wa->useScript('template.slicknav');
            $document->addScriptDeclaration('
                jQuery(\'.hornav\').slicknav();
            ');
        endif; 
    } else {
        $wa->useScript('template.slicknav');
        $document->addScriptDeclaration('
            jQuery(document).ready(function() {
                jQuery(\'.hornav\').slicknav();
            });
        ');
    } 
} elseif($mobile_menu_type == "slideout") {
    $wa->useScript('template.slideout');
    $document->addScriptDeclaration('
        jQuery(window).on("load", function() {

            var slideout = new Slideout({
                \'panel\': document.getElementById(\'body_panel\'),
                \'menu\': document.getElementById(\'slideout\'),
                \'padding\': -256,
                \'tolerance\': 70,

            });

            document.querySelector(\'.slideout-toggle-open\').addEventListener(\'click\', function() {
                slideout.open();
            });
            document.querySelector(\'.slideout-toggle-close\').addEventListener(\'click\', function() {
                slideout.close();
            });

            // jQuery
            jQuery(\'.menu li:not(.parent) a\').on(\'click\', function() {
                slideout.close();
            });

            jQuery(\'.slideout-menu li.parent > a\').on(\'click\', function(){
                jQuery(this).removeAttr(\'href\');
                var element = jQuery(this).parent(\'li\');
                if (element.hasClass(\'open\')) {
                    element.removeClass(\'open\');
                    element.find(\'li\').removeClass(\'open\');
                    element.find(\'ul\').slideUp();
                }
                else {
                    element.addClass(\'open\');
                    element.children(\'ul\').slideDown();
                    element.siblings(\'li\').children(\'ul\').slideUp();
                    element.siblings(\'li\').removeClass(\'open\');
                    element.siblings(\'li\').find(\'li\').removeClass(\'open\');
                    element.siblings(\'li\').find(\'ul\').slideUp();
                }
            });
        });
    ');
}

// Animate on Scroll
$wa->useScript('template.waypoints');

if($sidecol_responsive_pos == 'after') {
    // Responsive stacking order
    $document->addStyleDeclaration('
    @media only screen and (max-width: 767px) {
        .sidecol_a, .sidecol_b {
            order: 1 !important;
        }
    }
    ');
}

if ($this->params->get('sticky_sw')) {
    // Sticky Div
    $wa->useScript('template.sticky');
    $document->addScriptDeclaration('
        jQuery(window).on("load", function(){
            jQuery("#container_header").sticky({ 
                topSpacing: 0
            });
        });
    ');
}

// Load scripts.js
$document->addScriptOptions('j51_template', array(
    'scrolltoOffset' => $this->params->get('scrollto_offset', -55),
    'mobileMenuPosition' => $this->params->get('mobile_menu_position', 'left')
));
$wa->useScript('template.script');
