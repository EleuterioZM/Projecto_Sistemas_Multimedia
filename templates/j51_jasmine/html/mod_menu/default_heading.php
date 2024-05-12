<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_menu
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

$title      = $item->anchor_title ? ' title="' . $item->anchor_title . '"' : '';
$anchor_css = $item->anchor_css ?: '';
$linktype   = $item->title;

if ($item->menu_icon)
{
	// The link is an icon
	if ($itemParams->get('menu_text', 1))
	{
		// If the link text is to be displayed, the icon is added with aria-hidden
		$linktype = '<span class="pe-2 ' . $item->menu_icon . '" aria-hidden="true"></span>' . $item->title;
	}
	else
	{
		// If the icon itself is the link, it needs a visually hidden text
		$linktype = '<span class="pe-2 ' . $item->menu_icon . '" aria-hidden="true"></span><span class="visually-hidden">' . $item->title . '</span>';
	}
}
elseif ($item->menu_image)
{
	// The link is an image, maybe with its own class
	$image_attributes = [];

	if ($item->menu_image_css)
	{
		$image_attributes['class'] = $item->menu_image_css;
	}

	$linktype = HTMLHelper::_('image', $item->menu_image, $item->title, $image_attributes);

	if ($itemParams->get('menu_text', 1))
	{
		$linktype .= '<span class="image-title">' . $item->title . '</span>';
	}
}
if (strpos($item->title, 'menumod') === 0) {
?><jdoc:include type="modules" name="<?php echo $item->title; ?>"  style="mod_standard"/>
<?php }
else { ?>
<a class="mod-menu__heading nav-header <?php echo $anchor_css; ?>"<?php echo $title; ?>><?php echo $linktype; ?>
<?php if ($item->parent) {
	echo '<span class="parent-indicator" aria-hidden="true"></span>';
} ?>
</a>
<?php } ?>
