<?php

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

$module  = $displayData['module'];
$params  = $displayData['params'];
$attribs = $displayData['attribs'];

if ($module->content === null || $module->content === '')
{
	return;
}

$moduleTag              = $params->get('module_tag', 'div');
$moduleAttribs          = [];
$moduleAttribs['class'] = $module->position . ' card ' . htmlspecialchars($params->get('moduleclass_sfx'), ENT_QUOTES, 'UTF-8');
$headerTag              = htmlspecialchars($params->get('header_tag', 'h3'), ENT_QUOTES, 'UTF-8');
$headerClass            = htmlspecialchars($params->get('header_class', ''), ENT_QUOTES, 'UTF-8');
$headerAttribs          = [];
$headerAttribs['class'] = $headerClass;

?>
<<?php echo $moduleTag; ?> class="module <?php echo $params->get('moduleclass_sfx'); ?>">
	<div class="module_surround">
		<?php if ($module->showtitle) : ?>
			<?php
				$title = explode(' ', $module->title);
				$title_part1 = array_shift($title);
				$title_part2 = join(' ', $title);
			?>
		<div class="module_header">
			<<?php echo $headerTag; ?> class="mod_standard <?php echo $headerClass; ?>"><span><?php echo $title_part1.' '; ?><?php echo $title_part2; ?></span></<?php echo $headerTag; ?>>
		</div>
		<?php endif; ?>
		<div class="module_content">
		<?php echo $module->content; ?>
		</div> 
	</div>
</<?php echo $moduleTag; ?>>