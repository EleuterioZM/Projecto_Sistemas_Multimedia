<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class VideoParserFacebook extends VideoParserBase
{
	private function getCode($url)
	{
		// Check if the url should be processed here.
		if (stristr($url, 'facebook.com') === false) {
			return false;
		}

		// only process this if the URL is youtube.com
		parse_str(parse_url($url, PHP_URL_QUERY), $data);

		if (!$data) {
			return false;
		}

		return $data;
	}

	/**
	 * Renders the HTML for Facebook embedded videos
	 *
	 * @since	1.1.4
	 * @access	public
	 */
	public function getHtml($url, $amp = false)
	{
		$fullWidth = $this->isFullWidth();

		ob_start();
?>
<script async defer src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script>
<div class="fb-video" data-href="<?php echo $url;?>" <?php if (!$fullWidth) { ?> data-width="<?php echo $this->getWidth();?>" <?php } ?> data-show-captions="false" style="<?php echo $fullWidth ? 'width: 100%;' : '';?>height: auto !important;"></div>
<?php
		$contents = ob_get_contents();
		ob_end_clean();


		return $contents;
	}
}
