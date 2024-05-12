<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use ConvertForms\Helper;

jimport('joomla.filesystem.file');

$downloadKey = NRFramework\Functions::getDownloadKey();

// Display extension notices
\NRFramework\Notices\Notices::getInstance([
	'ext_element' => 'com_convertforms',
	'ext_xml' => 'com_convertforms'
])->show();

$canAccessOptions     = Helper::authorise('core.admin');
$canAccessForms       = Helper::authorise('convertforms.forms.manage');
$canAccessSubmissions = Helper::authorise('convertforms.submissions.manage');
$canAccessCampaigns   = Helper::authorise('convertforms.campaigns.manage');
$canAccessAddons      = Helper::authorise('convertforms.addons.manage');
?>
<div class="<?php echo defined('nrJ4') ? 'row' : 'row-fluid' ?> dashboard">
	<span class="span8 col-md-8">
		<div class="<?php echo defined('nrJ4') ? 'row' : 'row-fluid' ?>">
			<div class="col">
				<ul class="nr-icons">
					<?php if ($canAccessForms) { ?>
					<li>
						<a href="javascript: newForm()">
							<span class="icon-pencil-2"></span>
							<span><?php echo JText::_("COM_CONVERTFORMS_NEW_FORM") ?></span>
						</a>
					</li>
					<li>
						<a href="<?php echo JURI::base() ?>index.php?option=com_convertforms&view=forms">
							<span class="icon-list-2"></span>
							<span><?php echo JText::_("COM_CONVERTFORMS_FORMS") ?></span>
						</a>
					</li>
					<?php } ?>
					<?php if ($canAccessCampaigns) { ?>
					<li>
						<a href="<?php echo JURI::base() ?>index.php?option=com_convertforms&view=campaigns">
							<span class="cf-icon-megaphone"></span>
							<span><?php echo JText::_("COM_CONVERTFORMS_CAMPAIGNS") ?></span>
						</a>
					</li>
					<?php } ?>
					<?php if ($canAccessSubmissions) { ?>
					<li>
						<a href="<?php echo JURI::base() ?>index.php?option=com_convertforms&view=conversions">
							<span class="icon-users"></span>
							<span><?php echo JText::_("COM_CONVERTFORMS_SUBMISSIONS") ?></span>
						</a>
					</li>
					<?php } ?>
					<?php if ($canAccessAddons) { ?>
					<li>
						<a href="<?php echo JURI::base() ?>index.php?option=com_convertforms&view=addons">
							<span class="icon-puzzle"></span>
							<span><?php echo JText::_("COM_CONVERTFORMS_ADDONS") ?></span>
						</a>
					</li>
					<?php } ?>
					<?php if ($canAccessForms) { ?>
					<li>
						<a href="<?php echo JURI::base() ?>index.php?option=com_convertforms&view=forms&layout=import">
							<span class="icon-box-remove"></span>
							<span><?php echo JText::_("NR_IMPORT") ?></span>
						</a>
					</li>
					<?php } ?>
					<?php if ($canAccessOptions) { ?>
					<li>
						<a href="<?php echo JURI::base() ?>index.php?option=com_config&view=component&component=com_convertforms&path=&return=<?php echo MD5(JURI::base()."index.php?option=com_convertforms") ?>">
							<span class="icon-options"></span>
							<span><?php echo JText::_("JOPTIONS") ?></span>
						</a>
					</li>
					<?php } ?>
					<li>
						<a href="https://www.tassos.gr/joomla-extensions/convert-forms/docs" target="_blank">
							<span class="icon-info"></span>
							<span><?php echo JText::_("NR_KNOWLEDGEBASE")?></span>
						</a>
					</li>
				</ul>
			</div>
		</div>
		<?php if ($canAccessSubmissions) { ?>
		<div class="<?php echo defined('nrJ4') ? 'row mt-3' : 'row-fluid' ?>" style="margin-top:10px;">
			<div class="span6 col">
				<div class="nr-well-white">
				<h3><?php echo JText::_("COM_CONVERTFORMS_SUBMISSIONS") ?></h3>
				<?php include "panel.stats.php"; ?>
				</div>
			</div>
			<div class="span6 col">
				<div class="nr-well-white">
					<h3><?php echo JText::_('COM_CONVERTFORMS_LATEST_SUBMISSIONS') ?></h3>
					<?php include "latest.leads.php"; ?>
				</div>
			</div>
		</div>
		<?php } ?>
	</span>
	<span class="span4 col-md-4">
		<?php echo JHtml::_('bootstrap.startAccordion', "info", array('active' => 'slide0')); ?>

		<!-- Information Slide -->
		<?php 
			echo JHtml::_('bootstrap.addSlide', "info", JText::_("NR_INFORMATION"), 'slide0'); 
			include "panel.info.php";	
			echo JHtml::_('bootstrap.endSlide');
		?>

		<!-- Documentation Slide -->
		<?php 
			echo JHtml::_('bootstrap.addSlide', "info", JText::_("NR_KNOWLEDGEBASE"), 'slide1'); 
			include "panel.docs.php";
			echo JHtml::_('bootstrap.endSlide');
		?>

		<!-- Translations Slide -->
		<?php 
			echo JHtml::_('bootstrap.addSlide', "info", JText::_("NR_HELP_WITH_TRANSLATIONS"), 'slide2'); 
			include "panel.translations.php";
			echo JHtml::_('bootstrap.endSlide');
		?>

		<?php echo JHtml::_('bootstrap.endAccordion'); ?>
	</span>
</div>
<?php include_once(JPATH_COMPONENT_ADMINISTRATOR . '/layouts/footer.php'); ?>

<script>
	function newForm() {
        jQuery("#cfSelectTemplate").modal("show");
    }
</script>
