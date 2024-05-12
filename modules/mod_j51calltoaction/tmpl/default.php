<?php
/**
* J51_CallToAction
* Version		: 1.0
* Created by	: Joomla51
* Email			: info@joomla51.com
* URL			: www.joomla51.com
* License GPLv2.0 - http://www.gnu.org/licenses/gpl-2.0.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Load CSS/JS
$doc->addStyleSheet (JURI::base() . 'modules/mod_j51calltoaction/css/style.css' );

$doc->addStyleDeclaration('
	@media only screen and (max-width: '.$j51_col_breakpoint.'px) {
		.j51-calltoaction'.$j51_moduleid.' {
			flex-direction: column;
			align-items: center;
			text-align: center;
		}
		.j51-calltoaction'.$j51_moduleid.' .j51-text {
			margin: 0;
		}
	}
');

if(!empty($j51_bg_color)) {
	$doc->addStyleDeclaration('
		.j51-calltoaction'.$j51_moduleid.' {
			background-color: '.$j51_bg_color.';
			padding: 3em 3em;
		}
	');
}

if(!empty($j51_bg_image)) {
	$doc->addStyleDeclaration('
		.j51-calltoaction'.$j51_moduleid.' {
			background-image: url("'.JURI::base().'/'.$j51_bg_image.'");
			padding: 20px 35px;
		}
	');
}

?>

<div class="j51-calltoaction<?php echo $j51_moduleid; ?> j51-calltoaction j51-calltoaction-<?php echo $j51_layout; ?> j51-calltoaction-<?php echo $j51_align; ?>" style="margin: <?php echo $j51_margin_y; ?>px <?php echo $j51_margin_x; ?>px;">
	<div class="j51-text">
		<?php echo $j51_text; ?>
	</div>
	<?php if(!empty($j51_items)) { ?>
		<div class="j51-buttons">
			<?php foreach ($j51_items as $item) { ?>
			<a href="<?php if ($item->j51_linktype === 'url') {echo $item->j51_url;} else {echo JRoute::_("index.php?Itemid={$item->j51_menuitem}");}?>" class="<?php echo $item->j51_class; ?>"><?php echo $item->j51_buttontext; ?></a>
			<?php }	?>
		</div>
	<?php }	?>
</div>
