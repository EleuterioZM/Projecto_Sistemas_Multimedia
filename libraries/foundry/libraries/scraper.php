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
namespace Foundry\Libraries;

defined('_JEXEC') or die('Unauthorized Access');

use Foundry\Libraries\SimpleHtml;
use Foundry\Helpers\StringHelper;

require_once(__DIR__ . '/scraper/oembed/adapter.php');
require_once(__DIR__ . '/scraper/plugins/plugin.php');

class Scraper
{
	private $hooks = [];
	private $oembeds = [];

	private $contents = null;
	private $url = '';

	private $plugins = [
		'title',
		'description',
		'keywords',
		'opengraph',
		'images'
	];

	public function __construct($url)
	{
		$this->url = $this->normalizeUrl($url);
		$this->oembeds = $this->getOembedAdapters();
	}

	/**
	 * Extract the content type from the header
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	private function extractContentType($headers)
	{
		preg_match_all("/Content-Type: (\w+\/\w+)/i", $headers, $matches);

		if (isset($matches[1][0])) {
			return $matches[1][0];
		}

		return false;
	}

	/**
	 * Determines the type of the response from the header
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getContentType($header)
	{
		$mime = $this->extractContentType($header);

		if (in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
			return 'image';
		}

		// Treat any text based as html
		if ($mime === 'text/html' || $mime === 'application/json') {
			return 'html';
		}

		return false;
	}

	/**
	 * Creates a new plugin instance
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getPlugin($plugin, $parser, $contents)
	{
		require_once(__DIR__ . '/scraper/plugins/' . $plugin . '.php');

		$pluginClass = 'ScraperPlugin' . ucfirst($plugin);
		$plugin = new $pluginClass($parser, $contents, $this->url);

		return $plugin;
	}

	/**
	 * Create an instance of the scraper adapter 
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getOembedAdapter($type)
	{
		$file = __DIR__ . '/scraper/oembed/' . $type . '.php';
		$exists = file_exists($file);

		if (!$exists) {
			return false;
		}

		require_once($file);

		$adapterClassName = 'OembedAdapter' . ucfirst($type);
		$adapter = new $adapterClassName($this->url);

		return $adapter;
	}

	/**
	 * Create an instance of the scraper adapter 
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getOembedAdapterForUrl($url)
	{
		foreach ($this->oembeds as $adapter) {
			$handler = $adapter->isHandler($url);

			if ($handler !== false) {
				return $adapter;
			}
		}

		return false;
	}

	/**
	 * Initialize adapters available for the scraper
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getOembedAdapters()
	{
		$files = \JFolder::files(__DIR__ . '/scraper/oembed', '.php', false, false, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'adapter.php'));

		if (!$files) {
			return false;
		}

		$adapters = [];

		foreach ($files as $file) {
			$adapters[] = $this->getOembedAdapter(str_ireplace('.php', '', $file));
		}

		return $adapters;
	}

	/**
	 * Determines if the response header determines if this is a html document
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function isImage($headers)
	{
		$type = $this->getContentType($headers);

		return $type === 'image';
	}

	/**
	 * Determines if the response header determines if this is a html document
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function isHtmlDocument($headers)
	{
		$type = $this->getContentType($headers);

		return $type === 'html';
	}

	/**
	 * Normalizes the output of a url
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function normalizeContent($url, $content)
	{
		$info = parse_url($url);

		// This will ensure that urls that doesn't contain a scheme, will be prefixed with the correct scheme. E.g: //some/image.png
		$content = str_ireplace('src="//', 'src="' . $info['scheme'] . '://' , $content);

		return $content;
	}

	/**
	 * Normalizes the url and ensure that it is a valid url
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function normalizeUrl($url)
	{
		$url = trim($url);

		if (stristr($url, 'http://') === false && stristr($url, 'https://') === false) {
			$url = 'http://' . $url;
		}

		return $url;
	}

	/**
	 * Normalizes the result object to ensure that all our data is standardized
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function normalizeResult(&$result)
	{
		if (!isset($result->images)) {
			$result->images = array();
		}

		// If there is an oembed title, we should use it instead
		if (isset($result->oembed->title)) {
			$result->title = $result->oembed->title;
		}

		// We should rely on the opengraph title if there is
		if (isset($result->opengraph->title)) {
			$result->title = $result->opengraph->title;
		}

		if (isset($result->opengraph->desc)) {
			$result->description = $result->opengraph->desc;
		}

		if (isset($result->oembed->description)) {
			$result->description = $result->oembed->description;
		}

		// Normalize the properties
		$result->title = isset($result->title) ? $result->title : $result->url;

		// If the oembed has a thumbnail, we should always use it as the first image
		if (isset($result->oembed->thumbnail)) {
			array_unshift($result->images, $result->oembed->thumbnail);
		}

		// If the page has opengraph data
		if (isset($result->opengraph->image)) {
			array_unshift($result->images, $result->opengraph->image);
		}

		// If opengraph has video
		if (isset($result->opengraph->video)) {
			$result->video = $result->opengraph->video;
		}

		if (!isset($result->video)) {
			$result->video = false;
		}
	}

	/**
	 * Rescrapes a url
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function rescrape($url)
	{
		$scraper = new Scraper($url);
		return $scraper->scrape();
	}

	/**
	 * Scrapes url and retrieves the content from the particular page
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function scrape()
	{
		// Get the appropriate oembed adapter for this url
		$oembed = $this->getOembedAdapterForUrl($this->url);

		if (!$oembed) {
			$oembed = new \OembedAdapter($this->url);
		}

		$result = $oembed->scrape();

		// Oembed adapter implements its own scrape method, so we do not need to proceed
		if ($result !== true) {
			return $result;
		}

		$connector = \FH::connector($this->url);
		$contents = $connector->setUserAgent('Facebot')->execute()->getResult();

		// Get the response headers so that we can determine the response type
		$headers = $connector->getResponseHeaders();

		// For non html result, we should not process anything
		if (!$this->isHtmlDocument($headers)) {
			$result = (object) [
				'url' => $this->url,
				'description' => $this->url,
				'images' => $this->isImage($headers) ? $this->url : []
			];

			// Format the result
			$this->normalizeResult($result);

			return $result;
		}

		// Normalize the contents
		$contents = $this->normalizeContent($this->url, $contents);

		// Make sure the content is utf-8 as SocialSimpleHTML can only support UTF-8
		// $contents = mb_convert_encoding($contents, "UTF-8");
		if (!mb_detect_encoding($contents, 'UTF-8', true)) {
			$charset = 'utf-8';

			preg_match_all("/charset=([^()<>@,;:\"\/[\]?.=\s]*)/i", $header, $matches);

			if ($matches && isset($matches[1]) && $matches[1]) {
				$charset = $matches[1][0];
			}

			if ($charset === 'windows-1251') {
				$contents = mb_convert_encoding($contents, "utf-8", "windows-1251");
			}

			if ($charset !== 'windows-1251') {
				$contents = StringHelper::forceUTF8($contents);
			}
		}

		// Get the parser
		$this->parser = SimpleHtml::str_get_html($contents);

		// If we cannot parse the html, we shouldn't try to do anything
		if (!$this->parser) {
			return false;
		}

		$oembed->setParser($this->parser);
		$oembed->setContents($contents);

		// When scraping an amp page, we need to find the correct url
		$amp = $this->parser->find('html[amp]');

		if ($amp && isset($amp[0])) {
			$canonical = $this->parser->find('link[rel=canonical]');

			if ($canonical && isset($canonical[0])) {
				$url = $canonical[0]->href;

				return $this->rescrape($url);
			}
		}

		// When there are redirections, we also need to handle the correct url
		$httpEquiv = $this->parser->find('meta[http-equiv=refresh]');

		if ($httpEquiv && isset($httpEquiv[0])) {
			$httpEquiv = $httpEquiv[0]->attr['content'];

			// Check if this refresh value has url in it.
			$pattern = '/url=["\'](.*)["\']/i';
			preg_match($pattern, $httpEquiv, $matches);

			if (!empty($matches)) {
				return $this->rescrape($matches[1]);
			}
		}

		$result = (object) [
			'oembed' => $oembed->getOembedData($this->parser->find('link[type=application/json+oembed]'))
		];

		foreach ($this->plugins as $plugin) {
			$plugin = $this->getPlugin($plugin, $this->parser, $contents);

			$plugin->process($result);
		}

		if (method_exists($oembed, 'process')) {
			$oembed->process($result);
		}

		// Ensure that we have a standardized result
		$this->normalizeResult($result);

		return $result;
	}
}
