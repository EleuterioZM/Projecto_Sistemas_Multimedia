<?php
namespace J51\Helper;

defined( '_JEXEC' ) or die( 'Restricted index access' );

class BlockHelper {

	/**
	 * Function to check if module exists in block.
	 *
	 * @param   object  $tpl     The template
	 * @param   object  $module  The module
	 *
	 * @return bool
	 */
	public function blockExists($tpl, $module) {
		return
			$tpl->countModules($module.'a') ||
			$tpl->countModules($module.'b') ||
			$tpl->countModules($module.'c') ||
			$tpl->countModules($module.'d') ||
			$tpl->countModules($module.'e') ||
			$tpl->countModules($module.'f');
	}

	/**
	 * Function to output module block.
	 *
	 * @param   object  $tpl          The template
	 * @param   object  $moduleBlock  The module block
	 */
	public function renderBlock($tpl, $moduleBlock) {
		$moduleColumns = range("a","f"); // Creates array a-f
		$moduleBlockClean = str_replace('-', '', $moduleBlock); // Variable equal to block minus '-' eg. top1
		$moduleBlockVar = str_replace('-', '_', $moduleBlock); // eg. block_1
		$document = \JFactory::getDocument();
		// Calculate module column width (auto)
		$counted = 0;
		if ($tpl->countModules($moduleBlock.'a')) $counted++;
		if ($tpl->countModules($moduleBlock.'b')) $counted++;
		if ($tpl->countModules($moduleBlock.'c')) $counted++;
		if ($tpl->countModules($moduleBlock.'d')) $counted++;
		if ($tpl->countModules($moduleBlock.'e')) $counted++;
		if ($tpl->countModules($moduleBlock.'f')) $counted++;
		if ( $counted == 6 ) {
			$moduleWidthAuto = '16.66%';}
		else if ( $counted == 5 ) {
			$moduleWidthAuto = '20%';
		} else if ($counted == 4) {
			$moduleWidthAuto = '25%';
		} else if ($counted == 3) {
			$moduleWidthAuto = '33.3%';
		} else if ($counted == 2) {
			$moduleWidthAuto = '50%';
		} else if ($counted == 1) {
			$moduleWidthAuto = '100%';
		}

		echo '<div id="'.$moduleBlockClean.'_modules" class="block_holder">';

		if ( // If a module exists in block
			$tpl->countModules($moduleBlock.'a') ||
			$tpl->countModules($moduleBlock.'b') ||
			$tpl->countModules($moduleBlock.'c') ||
			$tpl->countModules($moduleBlock.'d') ||
			$tpl->countModules($moduleBlock.'e') ||
			$tpl->countModules($moduleBlock.'f')) {
			echo '<div id="wrapper_'.$moduleBlock.'" class="block_holder_margin">';
			// Start of module loop
			foreach ($moduleColumns as $moduleColumn) {
				$moduleBlockColumn = "$moduleBlock" . "$moduleColumn"; // eg. block-1a
				$moduleBlockColVar = str_replace('-', '_', $moduleBlockColumn); // eg. block_1a
				if($tpl->params->get($moduleBlockClean.'_auto') != '1') {
					if ($tpl->countModules($moduleBlockColumn)) { // If auto width set in param do this
						echo '<div class="'.$moduleBlock.' '.$moduleBlockColumn.'" style="max-width:'.$moduleWidthAuto.';">';
						echo '<jdoc:include type="modules" name="'.$moduleBlockColumn.'"  style="mod_standard"/>';
						echo '</div>';
					}
				} else {
					if ($tpl->countModules($moduleBlockColumn)) { // If manual width set in param do this
						echo '<div class="'.$moduleBlock.' '.$moduleBlockColumn.'" style="flex: 0 0 '.$tpl->params->get($moduleBlockColVar.'_manual').'%; max-width:'.$tpl->params->get($moduleBlockColVar.'_manual').'%;">';
						echo '<jdoc:include type="modules" name="'.$moduleBlockColumn.'"  style="mod_standard"/>';
						echo '</div>';
					}
				}
			}
			echo '<div class="clear"></div>';
			echo '</div>';

			// Background
			$moduleBlockCleanBg = $tpl->params->get($moduleBlockClean.'_bg');
			if ($moduleBlockCleanBg) {
				$document->addStyleDeclaration('#container_'.$moduleBlockClean.'_modules {background-image: url('.$tpl->baseurl.'/'.$tpl->params->get($moduleBlockClean.'_bg').')}');
			}
			$document->addStyleDeclaration('#container_'.$moduleBlockClean.'_modules {background-color: '.$tpl->params->get($moduleBlockClean.'_color').'}');

			// 100% width
			if($tpl->params->get($moduleBlockClean.'_width100') != "0") {
				$document->addStyleDeclaration ('#container_'.$moduleBlockClean.'_modules > .wrapper960 {width:100%;} #'.$moduleBlockClean.'_modules.block_holder {padding: 0;}
    		');
			}

			// Remove Padding
			if($tpl->params->get($moduleBlockClean.'_padding') != "0") {
				$document->addStyleDeclaration ('#'.$moduleBlockClean.'_modules.block_holder, #'.$moduleBlockClean.'_modules .module_surround, #'.$moduleBlockClean.'_modules .module_content {padding: 0;}
    		');
			}

			// Mobile disable
			if($tpl->params->get('res_'.$moduleBlockClean.'_sw') != "1") {
				$document->addStyleDeclaration ('@media only screen and ( max-width: 767px ) {#container_'.$moduleBlockClean.'_modules {display:none;}}
    		');
			}
		}
		echo '</div>';
	}
}
