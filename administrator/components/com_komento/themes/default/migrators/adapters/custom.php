<?php
/**
* @package		Komento
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="panel">
	<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_MIGRATORS_LAYOUT_MAIN', false); ?>

	<div class="panel-body" data-migrator-custom-data>
		<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
			<?php echo $this->fd->html('form.label', 'COM_KOMENTO_MIGRATORS_CUSTOM_TABLE', 'table'); ?>
			
			<div class="flex-grow">
				<?php echo $this->fd->html('form.dropdown', 'table', '', $tableOptions, ['attributes' => 'data-custom-table']); ?>
			</div>
		</div>

		<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
			<?php echo $this->fd->html('form.label', 'COM_KOMENTO_MIGRATORS_CUSTOM_COMPONENT', 'component'); ?>

			<div class="flex-grow">
				<?php echo $this->fd->html('form.dropdown', 'component', '', $componentOptions, ['attributes' => 'data-custom-component-filter']); ?>
			</div>
		</div>

		<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
			<?php echo $this->fd->html('form.label', 'COM_KOMENTO_MIGRATORS_CUSTOM_POST_ID', 'migrate-column-contentid'); ?>
			
			<div class="flex-grow">
				<select id="migrate-column-contentid" class="o-form-control" data-table-columns data-required="true"></select>
			</div>
		</div>

		<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
			<?php echo $this->fd->html('form.label', 'COM_KOMENTO_MIGRATORS_CUSTOM_COMMENT', 'migrate-column-comment'); ?>

			<div class="flex-grow">
				<select id="migrate-column-comment" class="o-form-control" data-table-columns></select>
			</div>
		</div>

		<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
			<?php echo $this->fd->html('form.label', 'COM_KOMENTO_MIGRATORS_CUSTOM_DATE', 'migrate-column-date'); ?>

			<div class="flex-grow">
				<select id="migrate-column-date" class="o-form-control" data-table-columns></select>
			</div>
		</div>

		<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
			<?php echo $this->fd->html('form.label', 'COM_KOMENTO_MIGRATORS_CUSTOM_AUTHOR_ID', 'migrate-column-authorid'); ?>
			
			<div class="flex-grow">
				<select id="migrate-column-authorid" class="o-form-control" data-table-columns></select>
			</div>
		</div>

		<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
			<?php echo $this->fd->html('form.label', 'COM_KOMENTO_MIGRATORS_CUSTOM_NAME', 'migrate-column-name'); ?>
			<div class="flex-grow">
				<select id="migrate-column-name" class="o-form-control" data-table-columns></select>
			</div>
		</div>

		<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
			<?php echo $this->fd->html('form.label', 'COM_KOMENTO_MIGRATORS_CUSTOM_EMAIL', 'migrate-column-email'); ?>

			<div class="flex-grow">
				<select id="migrate-column-email" class="o-form-control" data-table-columns></select>
			</div>
		</div>

		<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
			<?php echo $this->fd->html('form.label', 'COM_KOMENTO_MIGRATORS_CUSTOM_WEBSITE', 'migrate-column-homepage'); ?>

			<div class="flex-grow">
				<select id="migrate-column-" class="o-form-control" data-table-columns></select>
			</div>
		</div>

		<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
			<?php echo $this->fd->html('form.label', 'COM_KOMENTO_MIGRATORS_CUSTOM_PUBLISH_STATE', 'migrate-column-published'); ?>

			<div class="flex-grow">
				<select id="migrate-column-published" class="o-form-control" data-table-columns></select>
			</div>
		</div>

		<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
			<?php echo $this->fd->html('form.label', 'COM_KOMENTO_MIGRATORS_CUSTOM_IP', 'migrate-column-ip'); ?>

			<div class="flex-grow">
				<select id="migrate-column-ip" class="o-form-control" data-table-columns></select>
			</div>
		</div>

		<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
			<?php echo $this->fd->html('form.label', 'COM_KOMENTO_MIGRATORS_CUSTOM_MIGRATE_CYCLE', 'migrate-cycle'); ?>

			<div class="flex-grow">
				<?php echo $this->fd->html('form.text', 'cycle', '10', 'migrate-cycle', [
					'attributes' => 'data-migrate-cycle'
				]); ?>
			</div>
		</div>

		<?php echo $this->output('admin/migrators/adapters/fields/button'); ?>
	</div>
</div>