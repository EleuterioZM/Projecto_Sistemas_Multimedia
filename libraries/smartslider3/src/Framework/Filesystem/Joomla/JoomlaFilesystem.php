<?php

namespace Nextend\Framework\Filesystem\Joomla;

use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Nextend\Framework\Filesystem\AbstractPlatformFilesystem;

class JoomlaFilesystem extends AbstractPlatformFilesystem {

    public function init() {
        $this->_basepath = realpath(JPATH_SITE == '' ? DIRECTORY_SEPARATOR : JPATH_SITE . DIRECTORY_SEPARATOR);
        if ($this->_basepath == DIRECTORY_SEPARATOR) {
            $this->_basepath = '';
        }

        $this->measurePermission($this->_basepath . '/media/');
    }

    public function getWebCachePath() {
        return $this->getBasePath() . '/media/nextend';
    }

    public function getNotWebCachePath() {
        return JPATH_CACHE . '/nextend';
    }

    public function getImagesFolder() {
        if (defined('JPATH_NEXTEND_IMAGES')) {
            return $this->_basepath . JPATH_NEXTEND_IMAGES;
        }

        return $this->_basepath . '/images';
    }

    /**
     * Calling File:exists() method
     *
     * @param $file
     *
     * @return bool
     */
    public function fileexists($file) {
        return File::exists($file);
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    public function folders($path) {
        return Folder::folders($path);
    }

    /**
     * @param $path
     *
     * @return bool
     */
    public function is_writable($path) {
        return true;
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    public function createFolder($path) {
        return Folder::create($path, $this->dirPermission);
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    public function deleteFolder($path) {
        return Folder::delete($path);
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    public function existsFolder($path) {
        return Folder::exists($path);
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    public function files($path) {
        return Folder::files($path);
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    public function existsFile($path) {
        return File::exists($path);
    }

    /**
     * @param $path
     * @param $buffer
     *
     * @return mixed
     */
    public function createFile($path, $buffer) {
        return File::write($path, $buffer);
    }

}