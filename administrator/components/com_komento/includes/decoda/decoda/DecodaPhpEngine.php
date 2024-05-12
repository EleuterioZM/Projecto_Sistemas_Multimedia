<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2010 - 2020 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

/**
 * DecodaPhpEngine
 *
 * Renders tags by using PHP as template engine.
 *
 * @author      Miles Johnson - http://milesj.me
 * @author      Sean C. Koop - sean.koop@icans-gmbh.com
 * @copyright   Copyright 2006-2012, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/php/decoda
 */

class KtDecodaPhpEngine implements KtDecodaTemplateEngineInterface {

	/**
	 * Current path.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_path;

	/**
	 * Current filter.
	 *
	 * @access protected
	 * @var DecodaFilter
	 */
	protected $_filter;

	/**
	 * Return the current filter.
	 *
	 * @access public
	 * @return DecodaFilter
	 */
	public function getFilter() {
		return $this->_filter;
	}

	/**
	 * Return the template path. If no path has been set, set it.
	 *
	 * @access public
	 * @return string
	 */
	public function getPath() {
		if (empty($this->_path)) {
			$this->setPath(ED_DECODA . '/templates/');
		}

		return $this->_path;
	}

	/**
	 * Renders the tag by using php templates.
	 *
	 * @access public
	 * @param array $tag
	 * @param string $content
	 * @return string
	 * @throws Exception
	 */
	public function render(array $tag, $content) {
		$setup = $this->getFilter()->tag($tag['tag']);
		$path = $this->getPath() . $setup['template'] . '.php';

		if (!file_exists($path)) {
			throw new Exception(sprintf('Template file %s does not exist.', $setup['template']));
		}

		$vars = array();

		foreach ($tag['attributes'] as $key => $value) {
			if (isset($setup['map'][$key])) {
				$key = $setup['map'][$key];
			}

			$vars[$key] = $value;
		}

		extract($vars, EXTR_SKIP);
		ob_start();

		include $path;

		return ob_get_clean();
	}

	/**
	 * Sets the current filter.
	 *
	 * @access public
	 * @param DecodaFilter $filter
	 * @return DecodaTemplateEngineInterface
	 */
	public function setFilter(KtDecodaFilter $filter) {
		$this->_filter = $filter;

		return $this;
	}

	/**
	 * Sets the path to the tag templates.
	 *
	 * @access public
	 * @param string $path
	 * @return DecodaTemplateEngineInterface
	 */
	public function setPath($path) {
		if (substr($path, -1) !== '/') {
			$path .= '/';
		}

		$this->_path = $path;

		return $this;
	}

}
