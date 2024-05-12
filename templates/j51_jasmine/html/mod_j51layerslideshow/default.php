<?php
/**
* J51_LayerSlideshow
* Version		: 1.1
* Created by	: Joomla51
* Email			: info@joomla51.com
* URL			: www.joomla51.com
* License GPLv2.0 - http://www.gnu.org/licenses/gpl-2.0.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('stylesheet', 'mod_j51layerslideshow/style.css', array('relative' => 'auto'));

if (file_exists('media/j51_assets/css/tiny-slider.min.css')) { 
    HTMLHelper::_('stylesheet', 'j51_assets/tiny-slider.min.css', array('relative' => 'auto', 'version' => 'auto')); 
} else { 
    HTMLHelper::_('stylesheet', 'mod_j51layerslideshow/tiny-slider.min.css', array('relative' => 'auto', 'version' => 'auto'));
}

if (file_exists('media/j51_assets/js/tiny-slider.min.js')) { 
    HTMLHelper::_('script', 'j51_assets/tiny-slider.min.js', array('relative' => 'auto', 'version' => 'auto')); 
} else { 
    HTMLHelper::_('script', 'mod_j51layerslideshow/tiny-slider.min.js', array('relative' => 'auto', 'version' => 'auto'));
}

if (file_exists('media/j51_assets/js/imagesloaded.pkgd.min.js')) { 
    JHtml::_('script', 'j51_assets/imagesloaded.pkgd.min.js', array('relative' => true, 'version' => 'auto'), array('defer' => true, 'async' => false));
} else { 
    JHtml::_('script', 'mod_j51news/imagesloaded.pkgd.min.min.js', array('relative' => true, 'version' => 'auto'), array('defer' => true, 'async' => false));
}

$document->addScriptDeclaration('
    document.addEventListener("DOMContentLoaded", function() {
        var slider = tns({
            container: "#layerslideshow'.$j51_moduleid.'",
            mode: "gallery",
            items: 1,
            slideBy: "page",
            controls: true,
            autoplay: '.$autoplay.',
            autoplayTimeout: '.$autoplaySpeed.',
            speed: '.$speed.',
            controlsContainer: "#layerslideshow'.$j51_moduleid.'-controls",
            autoplayButton: "#layerslideshow'.$j51_moduleid.'-play",
            navContainer: "#layerslideshow'.$j51_moduleid.'-nav",
        });
    });
'); 

if($j51_header_overlap)  {
    $document->addStyleDeclaration('
        @media only screen and (min-width: 768px) {
            #container_header,
            .sticky-wrapper {
                position: absolute;
            }
            .layerslideshow'.$j51_moduleid.' .info {
                opacity: 0;
                transition: opacity .2s ease;
            }
        }
    ');
    $document->addScriptDeclaration('
        document.addEventListener("DOMContentLoaded", function() {
			var elem = document.querySelector("#layerslideshow'.$j51_moduleid.'");
			imagesLoaded( elem, function() {
				var headerHeight = document.getElementById("container_header").clientHeight;
            var nextArrow = document.getElementById("layerslideshow-next");
            var prevArrow = document.getElementById("layerslideshow-prev");

            if (nextArrow) nextArrow.style.marginTop = ((headerHeight * .5) - 20)+"px"; 
            if (prevArrow) prevArrow.style.marginTop = ((headerHeight * .5) - 20)+"px";
            var ele = document.querySelectorAll(".layerslideshow'.$j51_moduleid.' .info");
            var i;
            for (i = 0; i < ele.length; i++) {
                ele[i].style.paddingTop = headerHeight+"px";
                ele[i].style.opacity = "1";
            } 
			});
		});
    ');
};

$document->addStyleDeclaration('
    .layerslideshow'.$j51_moduleid.' .layerslideshow-title {
        color:' . $j51_title_color . ';
    }
    .layerslideshow'.$j51_moduleid.' .layerslideshow-caption {
        color:' . $j51_text_color . ';
    }
    .layerslideshow' . $j51_moduleid . ' .img-fill {
        max-height: ' . $j51_max_height . 'px;
    }
    .layerslideshow' . $j51_moduleid . ' .info-wrapper,
    .layerslideshow .NextArrow,
    .layerslideshow .PrevArrow {
        background-color: ' . $j51_text_bg_color . ';
    }
    .layerslideshow' . $j51_moduleid . ' .active .layerslideshow-title,
    .layerslideshow' . $j51_moduleid . ' .active .layerslideshow-caption {
        animation-duration: ' . $j51_anim_speed . 'ms;
    }
    @media only screen and (max-width: ' . $j51_title_breakpoint . 'px) {
        .layerslideshow'.$j51_moduleid.' .layerslideshow-title {display: none;}
    }
    @media only screen and (max-width: ' . $j51_caption_breakpoint . 'px) {
        .layerslideshow'.$j51_moduleid.' .layerslideshow-caption {display: none;}
    }
    @media only screen and (max-width: ' . min($j51_title_breakpoint, $j51_caption_breakpoint) . 'px) {
        .layerslideshow'.$j51_moduleid.' .info-wrapper{display: none;}
    }
');

if($j51_overflow_hidden) {
    $document->addStyleDeclaration('
        .layerslideshow-title-container,
        .layerslideshow-caption-container {
            overflow: hidden;
        }
    ');
};

echo '<div class="layerslideshow layerslideshow'.$j51_moduleid.'">';
    echo '<div id="layerslideshow'.$j51_moduleid.'">';
    if(!empty($j51slideimages)) {
        foreach ($j51slideimages as $item) {
            if (strpos($item->j51_slidelink, 'vimeo.com') !== false || strpos($item->j51_slidelink, 'youtube.com') !== false) {
                $slidetype = 'item-video';
            } else {
                $slidetype = 'item';
            };
            echo '<div class="'.$slidetype.' item-align-'.$item->j51_slidetextalign.'">';
            echo '<div class="img-fill">';
            echo '<img src="'.$item->j51_slideimage.'" alt="'.$item->j51_slidetitle.'">';
            if (!empty($item->j51_slidetitle) || !empty($item->j51_slidecaption)) {
                echo '<div class="layerslideshow'.$j51_moduleid.'__info info wrapper960">';
                    echo '<div class="info-wrapper" style="max-width: ' . $item->j51_slidetextwidth . 'px;">';
                    if (!empty($item->j51_slidetitle)) {
                        echo '<div class="layerslideshow-title-container">';
                        echo '<'.$j51_title_tag.' class="layerslideshow-title ';
                        if ($item->j51_override_anim) { echo $item->j51_title_anim; } else { echo $j51_title_anim_default; };
                        echo '" style="animation-delay: ' . $j51_title_delay . 'ms">';
                        echo $item->j51_slidetitle;
                        echo '</'.$j51_title_tag.'>';
                        echo '</div>';
                    }
                    if (!empty($item->j51_slidecaption)) {
                        echo '<div class="layerslideshow-caption-container">';
                        echo '<'.$j51_text_tag.' class="layerslideshow-caption ';
                        if ($item->j51_override_anim) { echo $item->j51_caption_anim; } else { echo $j51_caption_anim_default; };
                        echo '" style="animation-delay: ' . $j51_caption_delay . 'ms">';
                        echo $item->j51_slidecaption;
                        echo '</'.$j51_text_tag.'>';
                        echo '</div>';
                    }
                    echo '</div>';
                echo '</div>';
            }
            if (strpos($item->j51_slidelink, 'vimeo.com') !== false || strpos($item->j51_slidelink, 'youtube.com') !== false) {
                echo '<a class="owl-video" href="'.$item->j51_slidelink.'"></a>';
            }
            if (!empty($item->j51_slidelink)) {
                echo '<a href="'.$item->j51_slidelink.'" class="slidelink"></a>';
            }
            echo '</div></div>';
        } 
    }
    echo '</div>';

    echo '<div id="layerslideshow'.$j51_moduleid.'-controls" class="layerslideshow-controls">';

    echo '<a type="button" role="presentation" id="layerslideshow-prev" class="layerslideshow-prev"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M5.41 11H21a1 1 0 0 1 0 2H5.41l5.3 5.3a1 1 0 0 1-1.42 1.4l-7-7a1 1 0 0 1 0-1.4l7-7a1 1 0 0 1 1.42 1.4L5.4 11z"/></svg></a><a type="button" role="presentation" id="layerslideshow-next" class="layerslideshow-next"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.59 13H3a1 1 0 0 1 0-2h15.59l-5.3-5.3a1 1 0 1 1 1.42-1.4l7 7a1 1 0 0 1 0 1.4l-7 7a1 1 0 0 1-1.42-1.4l5.3-5.3z"/></svg></a>';
    
    echo '<div id="layerslideshow'.$j51_moduleid.'-play" style="display:none;"></div>';
    echo '<div class="j51-nav-dots" id="layerslideshow'.$j51_moduleid.'-nav">';
        if(!empty($j51slideimages)) {
            foreach ($j51slideimages as $item) {
                echo '<span role="button" class="j51-nav-dot"><span></span></span>'; 
            }
        } 
    echo '</div>';
    echo '</div>';
echo '</div>';

?>
