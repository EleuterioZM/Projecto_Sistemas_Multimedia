<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('template.masonry');
$wa->useScript('template.imagesloaded');

$document = JFactory::getDocument();

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

$num_columns  	= $this->params->get('num_columns', '3');

$document->addScriptDeclaration('
	document.addEventListener("DOMContentLoaded", function() {
		var elem = document.querySelector(".blog-masonry-items");
		imagesLoaded( elem, function() {
			var msnry = new Masonry( elem, {
				itemSelector: ".item",
			});
		});
	});
');
$document->addStyleDeclaration('
[style*="--line-clamp:"] {
    display: -webkit-box;
    -webkit-line-clamp: var(--line-clamp);
    -webkit-box-orient: vertical;  
    overflow: hidden;
}
');
?>
<div class="blog <?php echo $this->pageclass_sfx; ?> blog-masonry" itemscope itemtype="https://schema.org/Blog">
	<div class="blog-header">
		<?php if ($this->params->get('show_page_heading')) : ?>
			<div class="page-header">
				<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
			</div>
		<?php endif; ?>

		<?php if ($this->params->get('show_category_title', 1) or $this->params->get('page_subheading')) : ?>
			<h2> <?php echo $this->escape($this->params->get('page_subheading')); ?>
				<?php if ($this->params->get('show_category_title')) : ?>
					<span class="subheading-category"><?php echo $this->category->title; ?></span>
				<?php endif; ?>
			</h2>
		<?php endif; ?>

		<?php if ($this->params->get('show_cat_tags', 1) && !empty($this->category->tags->itemTags)) : ?>
			<?php $this->category->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
			<?php echo $this->category->tagLayout->render($this->category->tags->itemTags); ?>
		<?php endif; ?>

		<?php if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
			<div class="category-desc">
				<?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
					<img src="<?php echo $this->category->getParams()->get('image'); ?>" alt="<?php echo htmlspecialchars($this->category->getParams()->get('image_alt'), ENT_COMPAT, 'UTF-8'); ?>"/>
				<?php endif; ?>
				<?php if ($this->params->get('show_description') && $this->category->description) : ?>
					<?php echo JHtml::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="blog-masonry-leading">
		<?php if (!empty($this->lead_items)) : ?>
			<?php foreach ($this->lead_items as &$item) : ?>
				<?php $test = true;?>
				<div class="item item-leading cols-1 <?php echo $item->state == 0 ? ' system-unpublished' : null; ?> "
					itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
					<?php
					$this->item = & $item;
					echo $this->loadTemplate('item');
					?>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<div class="blog-masonry-items">

		<?php
		$introcount = (count($this->intro_items));
		$counter = 0;

		$this->intro_items = $this->get('items');
		?>

		<?php if (!empty($this->intro_items)) : ?>
			<?php foreach ($this->intro_items as $key => &$item) : ?>
				<?php $test = false;?>
				<div class="item cols-<?php echo (int) $num_columns; ?> <?php echo $item->state == 0 ? ' system-unpublished' : null; ?>"
					itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
					<?php
					$this->item = & $item;
					echo $this->loadTemplate('item');
					?>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<?php if (empty($this->lead_items) && empty($this->link_items) && empty($this->intro_items)) : ?>
		<?php if ($this->params->get('show_no_articles', 1)) : ?>
			<p><?php echo JText::_('COM_CONTENT_NO_ARTICLES'); ?></p>
		<?php endif; ?>
	<?php endif; ?>

	<?php if (!empty($this->link_items)) : ?>
		<div class="items-more">
			<?php echo $this->loadTemplate('links'); ?>
		</div>
	<?php endif; ?>

	<?php if (!empty($this->children[$this->category->id]) && $this->maxLevel != 0) : ?>
		<div class="cat-children">
			<?php if ($this->params->get('show_category_heading_title_text', 1) == 1) : ?>
				<h3> <?php echo JText::_('JGLOBAL_SUBCATEGORIES'); ?> </h3>
			<?php endif; ?>
			<?php echo $this->loadTemplate('children'); ?> </div>
	<?php endif; ?>
	
	<?php // Add pagination links ?>
	<?php if (!empty($this->items)) : ?>
		<?php if (($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
			<div class="com-content-category__navigation w-100">
				<?php if ($this->params->def('show_pagination_results', 1)) : ?>
					<p class="com-content-category__counter counter float-end pt-3 pe-2">
						<?php echo $this->pagination->getPagesCounter(); ?>
					</p>
				<?php endif; ?>
				<div class="com-content-category__pagination">
					<?php echo $this->pagination->getPagesLinks(); ?>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</div>
