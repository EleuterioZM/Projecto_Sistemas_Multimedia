<?php
/*================================================================*\
|| # Copyright (C) 2019  Joomla51. All Rights Reserved.           ||
|| # license - PHP files are licensed under  GNU/GPL V2           ||
|| # license - CSS  - JS - IMAGE files are Copyrighted material   ||
|| # Website: http://www.joomla51.com                             ||
\*================================================================*/

defined('_JEXEC') or die;

use \Joomla\CMS\Factory;

// The application
$app = Factory::getApplication();
$wa  = $this->getWebAssetManager();

// Loading the autoload file of composer
JLoader::import($app->getTemplate() . '.vendor.autoload', JPATH_THEMES);

$document        = Factory::getDocument();
$user            = Factory::getUser();
$this->language  = $document->language;
$this->direction = $document->direction;

$app->getCfg('sitename');
$siteName = $this->params->get('siteName');

$document->setHtml5(true);
$params = $app->getTemplate(true)->params;

// Detecting Active Variables
$option    = $app->input->getCmd('option', '');
$view      = $app->input->getCmd('view', '');
$layout    = $app->input->getCmd('layout', '');
$task      = $app->input->getCmd('task', '');
$itemid    = $app->input->getCmd('Itemid', '');
$sitename  = $app->get('sitename');
$menu      = $app->getMenu()->getActive();
$menuParams = new \Joomla\Registry\Registry();
$pageclass = $menuParams->get('pageclass_sfx','');
$editing   = false;
if (($option === 'com_config' && $view === 'modules') || ($layout === 'edit')) {
	$editing = true;
}

require_once("inc/helper.php");
require_once("inc/variables.php");

\JHtml::_('behavior.core');
\JHtml::_('bootstrap.framework');

require_once("vendor/ciar4n/j51_framework/src/Helper/BlockHelper.php");
$helper = new \J51\Helper\BlockHelper();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
<head>
<jdoc:include type="metas" />
  <jdoc:include type="styles" />
  <jdoc:include type="scripts" />
	<?php include ("inc/head.php");?>
	<?php include ("inc/scripts.php");?>
	<?php echo $this->params->get('head_custom_code'); ?>
</head>
<body class="site <?php echo $option
	. ' view-' . $view
	. ($layout ? ' layout-' . $layout : ' no-layout')
	. ($task ? ' task-' . $task : ' no-task')
	. ($itemid ? ' itemid-' . $itemid : '')
	. ' ' . $pageclass
	. ($params->get('fluidContainer') ? ' fluid' : '');
	echo ($this->direction === 'rtl' ? ' rtl' : '');
?>">

	<?php if($mobile_menu_type == "slideout") { ?>
	<div id="slideout" style="display:none;">
		<a class="slideout-toggle-close"><i class="fa fa-bars" aria-hidden="true"></i></a>
		<?php if($this->params->get('hornavPosition') == '1') : ?>
		        <jdoc:include type="modules" name="hornav" />
		<?php else : ?>
		        <?php echo $hornav; ?>
		<?php endif; ?>
	</div>
	<?php } ?>
	<?php if($mobile_menu_type == "slideout") { ?><div id="body_panel"><?php } ?>
		<header id="container_header">
			<div class="wrapper960">
				<div class="header_top">
					<?php if ($this->countModules( 'header-1' )) : ?>
				        <div class="header-1">
				            <jdoc:include type="modules" name="header-1" style="mod_simple" />
				        </div>
				    <?php endif; ?>
				    <?php if ($this->countModules( 'header-2' )) : ?>
				        <div class="header-2">
				            <jdoc:include type="modules" name="header-2" style="mod_simple" />
				        </div>
				    <?php endif; ?>
					<?php require("inc/layouts/social_icons.php"); ?>
				</div>
				<div class="header_main">
					<?php require("inc/layouts/logo.php"); ?>
					<div class="header_nav">
						<?php require("inc/layouts/hornav.php"); ?>
				    	
						<?php if($mobile_menu_type == "slideout") { ?>
							<a class="slideout-toggle-open"><i class="fa fa-bars" aria-hidden="true"></i></a>
						<?php } ?>
					</div>
				</div>
			</div>
		</header>

		<?php if ($helper->blockExists($this, 'showcase-1')) { ?>
		<div id="container_showcase1_modules" class="module_block border_block">
			<div class="wrapper960">
				<?php $helper->renderBlock($this, 'showcase-1'); ?>
			</div>
		</div>
		<?php } ?>

		<main class="content_main">
			<?php if ($helper->blockExists($this, 'top-1')) { ?>
			<div id="container_top1_modules" class="module_block <?php if($this->params->get('top1_parallax') == "1") {echo 'jarallax';} ?>" style="background-position: 50% 0">
				<?php if($particle_block == "top1") {echo $particles;} ?>
				<div class="wrapper960">
					<?php $helper->renderBlock($this, 'top-1'); ?>
				</div>
			</div>
			<?php }?>
			<?php if ($helper->blockExists($this, 'top-2')) { ?>
			<div id="container_top2_modules" class="module_block <?php if($this->params->get('top2_parallax') == "1") {echo 'jarallax';} ?>" style="background-position: 50% 0">
				<?php if($particle_block == "top2") {echo $particles;} ?>
				<div class="wrapper960">
					<?php $helper->renderBlock($this, 'top-2'); ?>
				</div>
			</div>
			<?php }?>
			<?php if ($this->countModules('breadcrumb') || $helper->blockExists($this, 'top-3')) { ?>
			<div id="container_top3_modules" class="module_block <?php if($this->params->get('top3_parallax') == "1") {echo 'jarallax';} ?>" style="background-position: 50% 0">
				<?php if($particle_block == "top3") {echo $particles;} ?>
				<div class="wrapper960">
					<?php $helper->renderBlock($this, 'top-3'); ?>
					<?php if ($this->countModules( 'breadcrumb' )) : ?>
					<jdoc:include type="modules" name="breadcrumb" style="mod_simple" />
					<?php endif; ?>
				</div>
			</div>
			<?php }?>
			<?php if($this->params->get('hide_component') == "0") { ?>
			<div id="container_main">
				<div class="wrapper960">
					<?php require("inc/layouts/main.php"); ?>
				</div>
			</div>
			<?php } ?>
			<?php if ($helper->blockExists($this, 'bottom-1')) { ?>
			<div id="container_bottom1_modules" class="module_block <?php if($this->params->get('bottom1_parallax') == "1") {echo 'jarallax';} ?>" style="background-position: 50% 0">
				<?php if($particle_block == "bottom1") {echo $particles;} ?>
				<div class="wrapper960">
					<?php $helper->renderBlock($this, 'bottom-1'); ?>
				</div>
			</div>
			<?php }?>
			<?php if ($helper->blockExists($this, 'bottom-2')) { ?>
			<div id="container_bottom2_modules" class="module_block <?php if($this->params->get('bottom2_parallax') == "1") {echo 'jarallax';} ?>" style="background-position: 50% 0">
				<?php if($particle_block == "bottom2") {echo $particles;} ?>
				<div class="wrapper960">
					<?php $helper->renderBlock($this, 'bottom-2'); ?>
				</div>
			</div>
			<?php }?>
			<?php if ($helper->blockExists($this, 'bottom-3')) { ?>
			<div id="container_bottom3_modules" class="module_block <?php if($this->params->get('bottom3_parallax') == "1") {echo 'jarallax';} ?>" style="background-position: 50% 0">
				<?php if($particle_block == "bottom3") {echo $particles;} ?>
				<div class="wrapper960">
					<?php $helper->renderBlock($this, 'bottom-3'); ?>
				</div>
			</div>
			<?php }?>
			<?php require("inc/layouts/base.php"); ?>
		</main>
		
		<footer id="container_footer">
			<?php if($this->params->get('footermenuPosition') == '1') : ?>
				<?php if ($this->countModules( 'footermenu' )) : ?> 
					<div class="footermenu">
						<jdoc:include type="modules" name="footermenu" />
						<div class="clear"></div>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<div class="footermenu">
					 <?php echo $footermenu; ?>
					 <div class="clear"></div>
				</div>
			<?php endif; ?>
			<div class="copyright">
				<p><?php echo $this->params->get('copyright'); ?></p>
			</div>
		</footer>
		
	<?php if($mobile_menu_type == "slideout") { ?></div><?php } ?>
<?php echo $this->params->get('body_custom_code'); ?>

<?php if (!$this->params->get('top1_bg') || !$this->params->get('top2_bg') || !$this->params->get('top3_bg') || !$this->params->get('bottom1_bg') || !$this->params->get('bottom2_bg') || !$this->params->get('bottom3_bg')) { ?> 
<?php $wa->useScript('template.jarallax'); ?>
<script>
	jarallax(document.querySelectorAll('.jarallax'), {
		speed: 0.5,
		disableParallax: /iPad|iPhone|iPod|Android/,
		disableVideo: /iPad|iPhone|iPod|Android/
	});
</script>
<?php } ?>

<jdoc:include type="modules" name="debug" />
</body> 
</html>