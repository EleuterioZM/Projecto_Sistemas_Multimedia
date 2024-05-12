<?php 
defined( '_JEXEC' ) or die( 'Restricted index access' );
include ("convert_rgb.php");

$document->addStyleDeclaration('
:root {
	--primary: '.$this->params->get('primary_color').';
	--primary-color: '.$this->params->get('primary_color').';
	--secondary-color: '.$this->params->get('secondary_color').';
	--base-color: '.$this->params->get('body_font_color').';
	--button-color: '.$this->params->get('button_color').';
	--hornav_font_color: '.$this->params->get('hornav_font_color').';
	--mobile-menu-bg: '.$this->params->get('mobile_menu_bg').';
	--mobile-menu-toggle: '.$this->params->get('mobile_menu_color').';
}
body, .hornav ul ul, .hornav ul ul a {
	color: '.$this->params->get('body_font_color').';
}
body, .hornav ul ul, .hornav ul ul a {
	font-family:'.str_replace("+"," ",$body_fontstyle).', Arial, Verdana, sans-serif;
	font-size: '.$this->params->get('body_fontsize').'px;
}
a {
	color: '.$this->params->get('content_link_color').';
}
h1 {
	color: '.$this->params->get('h1head_font_color').';
	font-family:'.str_replace("+"," ",$h1head_fontstyle).', Arial, Verdana, sans-serif; 
}
h2, 
h2 a:link, 
h2 a:visited {
	color: '.$this->params->get('articletitle_font_color').';
	font-family:'.str_replace("+"," ",$articlehead_fontstyle).', Arial, Verdana, sans-serif;
}
h3, 
.module h3, 
.module_menu h3, 
.btn, 
button {
	color: '.$this->params->get('modulehead_font_color').';
	font-family:'.str_replace("+"," ",$modulehead_fontstyle).', Arial, Verdana, sans-serif;
}
h4 {
	font-family:'.str_replace("+"," ",$h4head_fontstyle).', Arial, Verdana, sans-serif;
	color: '.$this->params->get('h4head_font_color').'; 
}
.hornav, .btn, .button, button {
	font-family:'.str_replace("+"," ",$hornav_fontstyle).' 
}
.wrapper960 {
	width: '.$this->params->get('wrapper_width').'px;
}
.logo {
	top: '.$this->params->get('logo_y').'px;
	left: '.$this->params->get('logo_x').'px;
}
.logo-text,
.logo-text a {
	color: '.$this->params->get('logo_font_color').';
	font-family:'.str_replace("+"," ",$logo_fontstyle).';
	font-size: '.$this->params->get('logo_font_size').'px;
}
.logo .logo-slogan {
	color: '.$this->params->get('slogan_font_color').';
	font-size: '.$this->params->get('slogan_font_size').'px;
	text-align: center;
}

.hornav ul li a,
.hornav ul li a:not([href]):not([class]),
.hornav > ul > .parent::after, 
.hornav .menu li [class^="fa-"]::before, 
.hornav .menu li [class*=" fa-"]::before {
	color: '.$this->params->get('hornav_font_color').';
}
.hornav ul ul li a {
	color: '.$this->params->get('hornav_dd_color').';
}
.hornav ul ul {
	background-color: '.$this->params->get('hornav_ddbackground_color').';
}
.hornav ul ul:before {
	border-color: transparent transparent '.$this->params->get('hornav_ddbackground_color').' transparent;
}
.sidecol_a {
	width: '.$this->params->get('sidecola_width').'%;
}
.sidecol_b {
	width: '.$this->params->get('sidecolb_width').'%;
}
.owl-theme .owl-nav [class*="owl-"],
.owl-theme .owl-dots .owl-dot.active span,
.owl-theme .owl-dots .owl-dot:hover span,
ul.dot li::before,
.text-primary,
#container_base h3 {
 	color: '.$this->params->get('primary_color').';
 }
.j51news .hover-overlay,
.background-primary {
	background-color: '.$this->params->get('primary_color').';
}
.blog-alternative .item-image::after {
	border-color: '.$this->params->get('primary_color').';
}
.btn, button, .btn-group.open .btn.dropdown-toggle, .pager.pagenav a, .btn-primary:active, .btn-primary.active, .btn-primary.disabled, .btn-primary[disabled], .btn:hover, .slidesjs-next.slidesjs-navigation, .slidesjs-previous.slidesjs-navigation, .search .icon-search, .dropdown-toggle, .label-info[href], .badge-info[href], .tagspopular a:hover, .module .module_header h3::after, .module_menu .module_header h3::after {
	background-color: '.$this->params->get('button_color').';
	color: #fff;
}
.btn, .button, button {
	background-color: '.$this->params->get('button_color').';
}
.btn:hover, .btn:focus, .btn:active, .btn.active, .btn.disabled, .btn[disabled], .readmore .btn:hover, .dropdown-toggle:hover, 
.search .icon-search:hover, .search .button:hover, .owl-theme .owl-nav [class*="owl-"]:hover {
	background-color: '.$this->params->get('button_hover_color').' !important; color: #ffffff !important;
}
.nav-tabs > .active > a, 
.nav-tabs > .active > a:hover, 
.nav-tabs > .active > a:focus {
	border-bottom-color: '.$this->params->get('button_color').';
}
blockquote {
	border-color: '.$this->params->get('button_color').';
}
.btn:hover, .button:hover, button:hover {
	border-color: '.$this->params->get('button_hover_color').';
}
.owl-theme .owl-controls .owl-buttons div {
	background-color: '.$this->params->get('button_color').' !important;
}
.tags .label-info[href] {
	background-color: transparent;
	color: '.$this->params->get('button_color').';
}
.owl-theme .owl-controls .owl-buttons div {
	background-color: '.$this->params->get('button_color').' !important;
}
body {
	background-color: '.$this->params->get('bgcolor','#fff').';
}
.showcase_seperator svg {
	fill: '.$this->params->get('bgcolor').';
}
.slicknav_menu, .slideout-menu {
	background: '.$this->params->get('mobile_menu_bg').'
}
.slideout-toggle-open .fa-bars::before {
	color: '.$this->params->get('mobile_menu_color').' !important;
}
#container_main, .item-image.pull-none + .item-content {
	background-color: '.$this->params->get('elementcolor3').';
}
#container_footer {
	background-color: '.$this->params->get('footer_color', '#000').';
}

');

// Header
$document->addStyleDeclaration('
	#container_header {
		background-color: '.$this->params->get('header_bg_color').';
	}
	.header_top,
	.is-sticky .header_top {
		background-color: '.$this->params->get('header_top_bg').';
	}
');

// hornav
$document->addStyleDeclaration('
');

// Base Background Image
if($this->params->get('base_bg') != '')  {
	$document->addStyleDeclaration('#container_base {background-image: url('.$this->baseurl.'/'.$this->params->get('base_bg').');}');
}

// Responsive Logo
if($this->params->get('mobilelogoimagefile') != '')  {
	$document->addStyleDeclaration('@media only screen and (max-width: '.$this->params->get('mobilelogo_breakpoint').'px) {.primary-logo-image{display:none;} .mobile-logo-image{display:inline-block;}}');
}

// Responsive Style
if($this->params->get('responsive_sw')) {
	$document->addCustomTag('<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5"/>');
	$document->addStyleDeclaration('
		@media only screen and (max-width: '.$this->params->get('wrapper_width').'px) {
			.module_block, .wrapper960  {
				width:100% !important;
			}
			.content_main {
				border-radius: 0;
			}
		}
		@media only screen and (max-width: '.$this->params->get('hornav_breakpoint').'px) {
			.hornav {display:none !important;}
			.slicknav_menu, .slideout-toggle-open {display:block;}
			#container_header .wrapper960 {flex-wrap: wrap;}
			#socialmedia {
			    position: static;
			    align-self: center;
			    transform: none;
			}
			.header_nav {
				display: flex;
			}
			.header-1 .module,
			.header-2 .module {
				display: flex;
				align-items: center;
			}
		}
	');
} 

// Responsive Switches
if($this->params->get('responsive_sw') == "1") {
	if($this->params->get('res_sidecola_sw') != "1") {
		$document->addStyleDeclaration('@media only screen and ( max-width: 767px ) {.sidecol_a {display:none;}}');
	}
}
if($this->params->get('responsive_sw') == "1") {
	if($this->params->get('res_sidecolb_sw') != "1") {
		$document->addStyleDeclaration('@media only screen and ( max-width: 767px ) {.sidecol_b {display:none;}}');
	}
}
if($this->params->get('responsive_sw') == "1") {
	if($this->params->get('res_header1_sw') != "1") {
		$document->addStyleDeclaration('@media only screen and ( max-width: 767px ) {.header-1 {display:none;}}');
	}
}
if($this->params->get('responsive_sw') == "1") {
	if($this->params->get('res_header2_sw') != "1") {
		$document->addStyleDeclaration('@media only screen and ( max-width: 767px ) {.header-2 {display:none;}}');
	}
}
if($this->params->get('responsive_sw') == "1") {
	if($this->params->get('mobile_showcase_sw') != "1") {
		$document->addStyleDeclaration('@media only screen and ( max-width: 767px ) {.showcase {display:none;} .mobile_showcase {display:inline;}}');
	}
}



