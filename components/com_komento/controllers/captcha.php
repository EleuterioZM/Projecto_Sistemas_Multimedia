<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/base.php');

class KomentoControllerCaptcha extends KomentoControllerBase
{
	/**
	 * Generates the captcha image
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function generate()
	{
		$id = $this->input->get('id', 0, 'int');

		// Clear expired captcha records
		KT::captcha()->clear();

		$table = KT::table('Captcha');
		$table->load($id);

		if (!$id || !$table->id) {
			throw FH::exception('Invalid captcha id', 500);
		}

		// Generate a random integer and take only 5 chars max.
		$hash = substr(md5(rand(0, 9999)), 0, 5);
		$table->response = $hash;
		$table->store();

		// Captcha width and height
		$width = 100;
		$height = 20;

		$image = ImageCreate($width, $height);
		$white = ImageColorAllocate($image, 255, 255, 255);
		$black = ImageColorAllocate($image, 0, 0, 0);
		$gray = ImageColorAllocate($image, 204, 204, 204);

		ImageFill( $image , 0 , 0 , $white );
		ImageString( $image , 5 , 30 , 3 , $hash , $black );
		ImageRectangle( $image , 0 , 0 , $width - 1 , $height - 1 , $gray );
		imageline( $image , 0 , $height / 2 , $width , $height / 2 , $gray );
		imageline( $image , $width / 2 , 0 , $width / 2 , $height , $gray );

		// Clear the output buffer to prevent other contents messing up with the image
		if (ob_get_length() !== false) {
			while (@ ob_end_clean());
			if (function_exists('ob_clean')) {
				@ob_clean();
			}
		}

		header('Content-type: image/jpeg');
		ImageJpeg($image);
		ImageDestroy($image);

		exit;
	}
}
