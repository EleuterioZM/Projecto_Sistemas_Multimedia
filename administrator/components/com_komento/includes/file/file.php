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

KT::import('admin:/includes/file/file');

class KomentoFile
{
	public function upload($fileItem, $fileName = '', $storagePath = '', $published = 1)
	{
		if (empty($fileItem)) {
			return false;
		}

		// store record first
		$table = KT::table('Uploads');

		$now = JFactory::getDate()->toSql();
		$table->created = $now;

		$profile = KT::user();

		$table->created_by = $profile->id;
		$table->published = $published;
		$table->mime = $fileItem['type'];
		$table->size = $fileItem['size'];

		if ($fileName === '') {
			$fileName = $fileItem['name'];
		}

		$table->filename = $fileName;

		if ($storagePath === '') {
			$config = KT::getConfig();
			$storagePath = $config->get('upload_path', '');
		}

		$table->path = $storagePath;

		if (!$table->upload()) {
			return false;
		}

		$source = $fileItem['tmp_name'];
		$destination = $table->getFilePath();

		jimport('joomla.filesystem.file');

		if (!JFile::copy($source , $destination)) {
			$table->rollback();
			return false;
		}

		return $table->id;
	}

	public function attach( $id, $uid )
	{
		$table = KT::getTable( 'uploads' );
		$state = $table->load( $id );

		if( !$state )
		{
			return false;
		}

		$table->uid = $uid;

		return $table->store();
	}

	public function clearAttachments( $uid )
	{
		$model = KT::model('Uploads');
		$attachments = $model->getAttachments($uid);

		foreach ($attachments as $attachment) {
			$attachment->delete();
		}

		return true;
	}
}
