<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// no direct access
defined('_JEXEC') or die;

class NRFrameworkImage {

    /**
     * @var
     */
    private $image;

    /**
     * @var array
     */
    private $variables;

    /**
     * @param $image
     */
    function __construct($image)
    {
        $this->image = str_replace(JURI::root(true), '', $image);

        if (substr($this->image, 0, 1) == "/")
        {
            $this->image = substr($this->image, 1);
        }

        // Default values
        $this->variables = array (
            'height' => '100',
            'width' => '100',
            'ratio' => '1',
            'crop' => true,
            'quality' => 100,
            'cache' => true,
            'filename' => 'img_'
        );
    }

    /**
     * @param $width
     * @param $height
     */
    public function setSize($width, $height) {
        $this->variables['width'] = (int) $width;
        $this->variables['height'] = (int) $height;
        $this->variables['ratio'] = ((int) $width / (int) $height);
    }

    /**
     * @param $crop
     */
    public function setCrop($crop) {
        $this->variables['crop'] = (bool) $crop;
    }


    /**
     * @param $quality
     */
    public function setQuality($quality) {
        $this->variables['quality'] = (int) $quality;
    }

    /**
     * @param $cache
     */
    public function setCache($cache) {
        $this->variables['cache'] = (bool) $cache;
    }

    /**
     * Get some basic information from the source image
     * @return array
     */
    private function imageInfo() {

        $image = getimagesize($this->image);

        $info = array();

        $info['width'] = $image[0];
        $info['height'] = $image[1];
        $info['ratio'] = $image[0]/$image[1];
        $info['mime'] = $image['mime'];

        return $info;

    }

    /**
     * @return resource
     * Loads the image
     */
    private function openImage() {

        switch ($this->imageInfo()['mime']) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg ($this->image);
                break;

            case 'image/png':
                $image = imagecreatefrompng ($this->image);
                imagealphablending( $image, true );
                imagesavealpha( $image, true );
                break;

            case 'image/gif':
                $image = imagecreatefromgif ($this->image);
                break;

            default:
                throw new RuntimeException('Unknown file type');
        }

        return $image;
    }

    /**
     * @param $image
     * @return resource
     * Does the actual image resize
     */
    private function resizeImage($image) {

        if (!is_resource($image)) {
            throw new RuntimeException('Wrong path or this is not an image');
        }

        $newImage = imagecreatetruecolor($this->variables['width'], $this->variables['height']);

        if (($this->variables['crop'] == true) and ($this->imageInfo()['ratio'] != $this->variables['ratio'])) {

            $src_x = $src_y = 0;
            $src_w = $this->imageInfo()['width'];
            $src_h = $this->imageInfo()['height'];

            $cmp_x = $src_w / $this->variables['width'];
            $cmp_y = $src_h / $this->variables['height'];

            // calculate x or y coordinate and width or height of source
            if ($cmp_x > $cmp_y) {

                $src_w = round ($src_w / $cmp_x * $cmp_y);
                $src_x = round (($src_w - ($src_w / $cmp_x * $cmp_y)) / 2);

            } else if ($cmp_y > $cmp_x) {

                $src_h = round ($src_h / $cmp_y * $cmp_x);
                $src_y = round (($src_h - ($src_h / $cmp_y * $cmp_x)) / 2);

            }

            imagecopyresampled($newImage,
                $image,
                0, 0,
                $src_x,
                $src_y,
                $this->variables['width'],
                $this->variables['height'],
                $src_w,
                $src_h);

            return $newImage;


        }

        else {

            imagecopyresampled($newImage,
                $image,
                0, 0, 0, 0,
                $this->variables['width'],
                $this->variables['height'],
                $this->imageInfo()['width'],
                $this->imageInfo()['height']);

        }

        return $newImage;

    }

    /**
     * @return string
     * Generate the filename for the image, based on original name, width, height and quality
     */
    private function createFilename() {
       return $this->variables['filename'].md5($this->image.$this->variables['width'].$this->variables['height'].$this->variables['quality']).'.jpg';
    }

    /**
     * @return bool
     * Check if an image exists in the cache
     */
    private function checkCache() {
        return file_exists(JPATH_SITE.'/cache/images/'.$this->createFilename());
    }

    /**
     * Checks if the cache folder exists, and if not, it creates it     *
     */
    private function cacheFolder() {

        if (!JFolder::exists(JPATH_SITE.'/cache/images'))
        {
            try {
                JFolder::create(JPATH_SITE.'/cache/images'); 
            }
            catch (Exception $e) 
            {
                echo 'Caught exception: ', $e->getMessage(), "\n";
            }
        }
    }

    /**
     * @param $image
     * Saves the image
     * @throws ErrorException
     */
    private function saveImage($image) {
        $this->cacheFolder();
        imageinterlace($image, true);
        $saved = imagejpeg($image, JPATH_SITE . '/cache/images/' . $this->createFilename(), $this->variables['quality']);
         if ($saved == false) {
             throw new ErrorException('Cannot save file, please check directory and permissions');
         }
         imagedestroy($image);

    }

    /**
     * @param $image
     * Processes the image, unless it is already in the cache
     * @throws ErrorException
     * @returns string
     */
    private function processImage($image) {

        if (($this->variables['cache'] == true) and $this->checkCache()) {
            return false;
        }

        else {
            try {
                $newImage = $this->openImage($image);
                $newImage = $this->resizeImage($newImage);
                $this->saveImage($newImage);
            }
            catch (Exception $e) {
                echo 'Caught exception: ', $e->getMessage(), "\n";
            }            
            
        }
    }

    /**
     * Method to process the image and get the new image's URL
     */
    public function get() {

        $this->processImage($this->image);
        return JURI::root(true).'/cache/images/'.$this->createFilename();

    }
}