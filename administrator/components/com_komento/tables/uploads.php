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

class KomentoTableUploads extends KomentoTable
{
	public $id = null;
	public $uid = null;
	public $filename = null;
	public $hashname = null;
	public $path = '';
	public $created = null;
	public $created_by = null;
	public $published = null;
	public $mime = null;
	public $size = null;

	public function __construct(&$db)
	{
		parent::__construct('#__komento_uploads', 'id', $db);
	}

	public function getType()
	{
		$type = explode("/", $this->mime);

		return $type[0];
	}

	public function getSubtype()
	{
		$type = explode("/", $this->mime);

		return $type[1];
	}

	public function upload()
	{
		if (empty($this->hashname)) {
			$this->hashname = $this->hash();
		}

		return $this->store();
	}

	public function download()
	{
		$file = $this->getFilePath();

		if (!JFile::exists($file)) {
			return false;
		}

		$length = filesize($file);

		header('Content-Description: File Transfer');
		header('Content-Type: ' . $this->mime);
		header('Content-Disposition: attachment; filename="' . basename($this->filename) . '";');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . $length);

		ob_clean();
		flush();
		readfile($file);
		exit;
	}

	public function rollback()
	{
		$this->delete();
	}

	private function hash()
	{
		return md5($this->filename . JFactory::getDate()->toSql());
	}

	// Overwrite parent delete function
	public function delete($pk = null)
	{
		$state = parent::delete($pk);

		if (!$state) {
			return false;
		}

		$file = $this->getFilePath();

		jimport('joomla.filesystem.file');

		return JFile::delete($file);
	}

	public function getPath()
	{
		$path = KOMENTO_UPLOADS_ROOT . '/';
		$relativePath = trim(str_ireplace(['/', '\\'], '/', $this->path), '/');

		if (!empty($relativePath)) {
			$path .= $relativePath;
		}

		if (!file_exists($path)) {
			jimport('joomla.filesystem.folder');
			JFolder::create($path);
		}

		return $path;
	}

	public function getFilePath()
	{
		$file = $this->getPath() . '/' . $this->hashname;

		return $file;
	}

	public function getLink()
	{
		$link = rtrim(JURI::root(), '/') . '/index.php?option=com_komento&controller=file&task=download&id=' . $this->id;

		return $link;
	}

	public function getExtension()
	{
		$tmp = explode('.', $this->filename);

		if (count($tmp) <= 1) {
			return false;
		}

		$extension = array_pop($tmp);

		return $extension;
	}

	public function getIconType()
	{
		$extension = $this->getExtension();
		$zipExtensions = array("zip","rar","gz","gzip");

		$class = 'doc';

		if (in_array($extension, $zipExtensions)) {
			$class = 'zip';
		}

		return $class;
	}

	/**
	 * Determines if this is an image
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function isImage()
	{
		$type = $this->getType();

		return $type == 'image';
	}

	public function isCommentAttachment($commentid)
	{
		return ($this->uid == $commentid);
	}

	/**
	 * Get the proper filesize
	 *
	 * @since   3.1
	 * @access  public
	 */
	public function getSize($format = 'kb')
	{
		$size = $this->size;

		switch ($format) {
			case 'kb':
			default:
				$size = round($this->size / 1024);
				break;
		}

		return $size;
	}
}
