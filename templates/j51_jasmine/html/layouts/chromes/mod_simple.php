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
	<div class="module_content">
		<?php echo $module->content; ?>
	</div> 
</<?php echo $moduleTag; ?>>