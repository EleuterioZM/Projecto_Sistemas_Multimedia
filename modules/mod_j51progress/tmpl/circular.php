<?php
/**
* J51_Progress
* Created by	: Joomla51
* Email			: info@joomla51.com
* URL			: www.joomla51.com
* License GPLv2.0 - http://www.gnu.org/licenses/gpl-2.0.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;

$baseurl 			    = JURI::base();
$document               = JFactory::getDocument();

JHtml::_('behavior.core');
HTMLHelper::_('stylesheet', 'mod_j51progress/style.css', array('relative' => 'true', 'version' => 'auto'));
HTMLHelper::_('script', 'mod_j51progress/script.js', array('relative' => 'true', 'version' => 'auto'));

$js_assets = array(
	'noframework.waypoints.min.js', 
	'countUp.min.js'
);

if ($j51_enable_animation) {
	foreach ($js_assets as $asset) {
		if (file_exists('media/j51_assets/js/'.$asset.'')) { 
		    HTMLHelper::_('script', 'j51_assets/'.$asset.'', array('relative' => 'true', 'version' => 'auto'));
		} else { 
		    HTMLHelper::_('script', 'mod_j51progress/'.$asset.'', array('relative' => 'true', 'version' => 'auto'));
		}
	}
}

// Styling from module parameters
$document->addStyleDeclaration('
.j51_progress'.$j51_moduleid.' .j51_progress_item {
	flex: 0 0 '.$j51_columns.';
	max-width: '.$j51_columns.';
	padding: '.($j51_margin_y / 2).'px '.($j51_margin_x / 2).'px;
}
.j51_progress'.$j51_moduleid.' {
	margin: -'.($j51_margin_y / 2).'px -'.($j51_margin_x / 2).'px;
}
.j51_progress'.$j51_moduleid.' .j51_progress_circle_highlight {
	animation-duration: '.$j51_animation_length.'ms;
}
@media only screen and (min-width: 960px) and (max-width: 1280px) {
	.j51_progress'.$j51_moduleid.' .j51_progress_item {flex: 0 0 '.$j51_columns_tabl.';max-width: '.$j51_columns_tabl.';}
}
@media only screen and (min-width: 768px) and (max-width: 959px) {
	.j51_progress'.$j51_moduleid.' .j51_progress_item {flex: 0 0 '.$j51_columns_tabp.';max-width: '.$j51_columns_tabp.';}
}
@media only screen and ( max-width: 767px ) {
	.j51_progress'.$j51_moduleid.' .j51_progress_item {flex: 0 0 '.$j51_columns_mobl.';max-width: '.$j51_columns_mobl.';}
}
@media only screen and (max-width: 440px) {
	.j51_progress'.$j51_moduleid.' .j51_progress_item {flex: 0 0 '.$j51_columns_mobp.';max-width: '.$j51_columns_mobp.';}
}
');
if ($module->showtitle) {
	$document->addStyleDeclaration('
		.j51_progress'.$j51_moduleid.' {
			margin: 0 -'.($j51_margin_x / 2).'px;
		}
	');
} else {
	$document->addStyleDeclaration('
		.j51_progress'.$j51_moduleid.' {
			margin: -'.($j51_margin_y / 2).'px -'.($j51_margin_x / 2).'px;
		}
	');
}
if (!empty($j51_title_color)) {
	$document->addStyleDeclaration('
		.j51_progress'.$j51_moduleid.' .j51_progress_title {
			color: '.$j51_title_color.';
			fill: '.$j51_title_color.';
		}
	');
}
if (!empty($j51_value_color)) {
	$document->addStyleDeclaration('
		.j51_progress'.$j51_moduleid.' .j51_progress_value {
			color: '.$j51_value_color.';
			fill: '.$j51_value_color.';
		}
	');
}

if (!empty($document->getScriptOptions('j51_module_progress'))) {
    $dataArray = array_merge($document->getScriptOptions('j51_module_progress'), $dataArray);
}

$document->addScriptOptions('j51_module_progress', $dataArray);

$id = 1;
$delay = 0;

?>

<div class="j51_progress j51_progress<?php echo $j51_moduleid; ?>" >
<?php foreach ($j51_items as $item) : ?>
	<?php 
		$numberid = $j51_moduleid.'i'.$id;

		if ($item->j51_override_style) {
			$radius = 18 - ($item->j51_progress_height / 2);
			$stroke = $item->j51_progress_height / 4;
			$stroke_color = $item->j51_progress_color;
			$stroke_bg = $item->j51_progress_bg_color;
		} else {
			$radius = 18 - ($j51_progress_height / 2);
			$stroke = $j51_progress_height / 4; 
			$stroke_color = $j51_progress_color;
			$stroke_bg = $j51_progress_bg_color;
		}
		$radius = "15.9"
	?>
	<div id="number<?php echo $numberid ?>" class="j51_progress_item <?php echo $j51_layout; ?> <?php if ($j51_enable_animation) { ?>animate<?php } ?>">
		<div class="j51_progress_circle_wrapper">
			<?php if($item->j51_progress_title  != "") : ?>
			<<?php echo $j51_title_tag; ?> class="j51_progress_title">
				<?php echo htmlspecialchars($item->j51_progress_title, ENT_COMPAT, 'UTF-8'); ?>
			</<?php echo $j51_title_tag; ?>>
			<?php endif; ?>
			<div>
				<svg viewbox="0 0 34 34" xmlns="http://www.w3.org/2000/svg">
			      <circle 
					class="j51_progress_circle" 
					stroke-width="<?php echo $stroke; ?>" 
					fill="none" 
					cx="50%" 
					cy="50%" 
					r="<?php echo $radius; ?>"
					style="stroke: <?php echo $stroke_bg; ?>;"
			      />
			      <circle 
					class="j51_progress_circle_highlight" 
					stroke-width="<?php echo $stroke; ?>" 
					stroke-dasharray="<?php echo htmlspecialchars($item->j51_progress, ENT_COMPAT, 'UTF-8'); ?>,100" 
					fill="none" 
					cx="50%" 
					cy="50%" 
					r="<?php echo $radius; ?>" 
					transition-delay="<?php echo $delay; ?>"
					style="stroke: <?php echo $stroke_color; ?>;"
			      />
			    </svg>
			    <div class="j51_progress_value" transition-delay="<?php echo $delay; ?>">
			        <span id="counter<?php echo $numberid ?>" class="circle-chart__percent">
			        	<?php echo htmlspecialchars($item->j51_progress, ENT_COMPAT, 'UTF-8'); ?>
			        </span>
			        <span>%</span>
			    </div>
			</div>
		</div>
	</div>
	<?php 
		$id++;
		$delay = $delay + $j51_interval_length; 
		endforeach;
	?>
</div>
