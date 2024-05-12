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

use Foundry\Libraries\SimpleHtml;
use Foundry\Libraries\Scraper;
use Foundry\Helpers\StringHelper;

class OembedAdapter
{
	public $url = null;
	public $parser = null;
	public $contents = '';
	public $oembed = false;

	public function __construct($url)
	{
		$this->url = $url;
	}

	/**
	 * Determines if the adapter is currently the handler for the given url
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function isHandler($url)
	{
		$valid = (bool) $this->isValid($url);

		return $valid; 
	}

	/**
	 * Triggered before the scraper scrapes any contents.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function beforeScrape()
	{
		// To exit scraping, return non true value
		return true;
	}

	/**
	 * Determines if the current link supports oembed tags
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getOembedUrl()
	{
		$link = $this->parser->find('link[type=application/json+oembed]');

		if ($link) {
			return $link;
		}

		return false;
	}

	/**
	 * Retrieves the oembed data of a page
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getOembedData()
	{
		// Try to find any oembed links associated with the page so that we can try to crawl it
		$urls = $this->getOembedUrl();

		if (!$urls) {
			return false;
		}

		$object = false;

		foreach ($urls as $node) {

			if (!isset($node->attr['href'])) {
				continue;
			}

			// Get the oembed url
			$url = $node->attr['href'];

			// Urls should not contain html entities
			$url = html_entity_decode($url);

			// Now we need to crawl the url again
			$connector = \FH::connector($url);
			$contents = $connector->execute()->getResult();

			$object = json_decode($contents);

			if (is_array($object) && isset($object[0])) {
				$object = $object[0];
			}

			if (isset($object->thumbnail_url)) {
				$object->thumbnail = $object->thumbnail_url;
			}

			// For any reasons object is not created at this point, we need to simulate it
			if (!is_object($object)) {
				$object = new stdClass();
			}

			// For wordpress specific sites
			$object->isWordpress = false;

			if ($this->isWordpress($url)) {
				$object->isWordpress = true;
			}
		}

		return $object;
	}

	/**
	 * Determines if the target site is a wordpress site
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	private function isWordpress($url)
	{
		if (stristr($url, 'https://public-api.wordpress.com/oembed') !== false) {
			return true;
		}

		// For some cases, the url for wordpress is different
		// http://site.com/wp-json/oembed/1.0/embed?url=http%3A%2F%2Fsite.com.br%2Farticle1%2F
		$url = rtrim($url,'/');
		$tmp = explode('/', $url);

		if (strtolower($tmp[count($tmp) - 3] == 'oembed') && strtolower($tmp[count($tmp) - 4] == 'wp-json')) {
			return true;
		}

		return false;
	}

	/**
	 * Fix the http url in https site
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function fixOembedUrl($oembed)
	{
		$uri = \JURI::getInstance();

		if ($uri->getScheme() == 'https') {
			$oembed->html = str_ireplace('http://', 'https://', $oembed->html);
			$oembed->thumbnail = str_ireplace('http://', 'https://', $oembed->thumbnail);

			if (isset($oembed->thumbnail_url)) {
				$oembed->thumbnail_url = str_ireplace('http://', 'https://', $oembed->thumbnail_url);
			}
		}

		return $oembed;
	}

	/**
	 * Generates the opengraph data given the link to the video
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function getOpengraphData($contents)
	{
		$og = new stdClass();

		$parser = SocialSimpleHTML::str_get_html($contents);

		$og->type = 'video';

		$meta = @$parser->find('meta[property=og:video]');

		if ($meta && isset($meta[0])) {
			$og->video = $meta[0]->content;
		}

		$meta = $parser->find('meta[property=og:image]');
		$og->image = $meta[0]->content;

		$meta = @$parser->find('meta[property=og:title]');
		$og->title = $meta[0]->content;

		$meta = @$parser->find('meta[property=og:video:width]');

		if ($meta && isset($meta[0])) {
			$og->video_width = $meta[0]->content;
		}

		$meta = @$parser->find('meta[property=og:video:height]');

		if ($meta && isset($meta[0])) {
			$og->video_height = $meta[0]->content;
		}

		$meta = @$parser->find('meta[property=og:video:duration]');

		if ($meta && isset($meta[0])) {
			$og->video_duration = $meta[0]->content;
		}

		return $og;
	}

	/**
	 * By default, we let the scraper run its own course. If there are any oembed adapter that needs to run its own, 
	 * they can override this method and implement on their own
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function scrape()
	{
		// Child can override this method
		return true;
	}

	/**
	 * Allows caller to set the contents in the adapter
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function setContents($contents)
	{
		$this->contents = $contents;
	}

	/**
	 * Allows caller to set the parser in the adapter
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function setParser($parser)
	{
		$this->parser = $parser;
	}

}
