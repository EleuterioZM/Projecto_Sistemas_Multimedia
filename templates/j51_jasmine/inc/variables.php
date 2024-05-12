<?php
defined( '_JEXEC' ) or die( 'Restricted index access' );

//Font Face Styling
$body_fontstyle = $this->params->get('body_fontstyle');
$h1head_fontstyle = $this->params->get('h1head_fontstyle');
$articlehead_fontstyle = $this->params->get('articlehead_fontstyle');
$modulehead_fontstyle = $this->params->get('modulehead_fontstyle');
$h4head_fontstyle = $this->params->get('h4head_fontstyle');
$hornav_fontstyle = $this->params->get('hornav_fontstyle');
$logo_fontstyle = $this->params->get('logo_fontstyle');

//Logo and slogan
$logoimagefile = $this->params->get('logoimagefile');
$logoimagefile_sm = $this->params->get('logoimagefile_sm');
$defaultlogoimage = "templates/<?php echo $this->template?>/images/logo.png";

//Layout
$sidecola_width = $this->params->get('sidecola_width');
$sidecolb_width = $this->params->get('sidecolb_width');
$column_layout = $this->params->get('column_layout');

//Hornav Menu
$renderer = $document->loadRenderer( 'module' );
$module	 = JModuleHelper::getModule( 'mod_menu', "hornav_menu" );
$menu_name = $this->params->get("hornav_menu", "mainmenu");
$module->params	= "menutype=$menu_name\nshowAllChildren=1";
$hornav = $renderer->render( $module);

//Hornav Menu Mobile
$renderer = $document->loadRenderer( 'module' );
$module     = JModuleHelper::getModule( 'mod_menu', "hornav_menu" );
$menu_name = $this->params->get("hornav_menu", "mainmenu");
$module->params    = "menutype=$menu_name\nshowAllChildren=1\ntag_id=mobile";
$hornav_mobile = $renderer->render( $module);

//Footer Menu
$renderer = $document->loadRenderer( 'module' );
$module	 = JModuleHelper::getModule( 'mod_menu', "footer_menu" );
$menu_name = $this->params->get("footer_menu", "mainmenu");
$module->params	= "menutype=$menu_name\nendLevel=1";
$footermenu = $renderer->render( $module);
$footermenu_onoff = $this->params->get('footermenu_onoff');

//Responsive Options
$responsive_sw = $this->params->get('responsive_sw');
$sidecol_responsive_pos = $this->params->get('sidecol_responsive_pos');
$mobile_showcase_sw = $this->params->get('mobile_showcase_sw');
$mobile_showcase = $this->params->get('mobile_showcase');
$hornav_breakpoint = $this->params->get('hornav_breakpoint');
$mobile_menu_type = $this->params->get('mobile_menu_type');
$mobile_menu_color = $this->params->get('mobile_menu_color');
$mobile_menu_bg = $this->params->get('mobile_menu_bg');

$nav_google_sw = $this->params->get('nav_google_sw');

$fontawesome_sw = $this->params->get('fontawesome_sw');
$animatecss_sw = $this->params->get('animatecss_sw');
$header_grad = $this->params->get('header_grad');

$font_subset = $this->params->get('font_subset', 'latin');
$font_weights = $this->params->get('font_weights', '400,700');

$headerslidewidth = $this->params->get('headerslidewidth');
$headerslideheight = $this->params->get('headerslideheight');
$headerslideshow = $this->params->get('headerslideshow');
$header_slide_image = $this->params->get('header_slide_image');

$particle_block = $this->params->get('particle_block');
$particle_color = $this->params->get('particle_color');

$headerslideinterval = $this->params->get('headerslideinterval');

$min_css = $this->params->get('min_css', '1');

?>