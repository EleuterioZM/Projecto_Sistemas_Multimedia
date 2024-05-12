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
 * BlockFilter
 *
 * Provides tags for block styled elements.
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/php/decoda
 */

class BlockFilter extends DecodaFilter {

	/**
	 * Supported tags.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_tags = array(
		'align' => array(
			'tag' => 'div',
			'type' => self::TYPE_BLOCK,
			'allowed' => self::TYPE_BOTH,
			'attributes' => array(
				'default' => array('/left|center|right|justify/i', 'align-{default}')
			),
			'map' => array(
				'default' => 'class'
			)
		),
		'left' => array(
			'tag' => 'div',
			'type' => self::TYPE_BLOCK,
			'allowed' => self::TYPE_BOTH,
			'html' => array(
				'class' => 'align-left'
			)
		),
		'right' => array(
			'tag' => 'div',
			'type' => self::TYPE_BLOCK,
			'allowed' => self::TYPE_BOTH,
			'html' => array(
				'class' => 'align-right'
			)
		),
		'center' => array(
			'tag' => 'div',
			'type' => self::TYPE_BLOCK,
			'allowed' => self::TYPE_BOTH,
			'html' => array(
				'class' => 'align-center'
			)
		),
		'justify' => array(
			'tag' => 'div',
			'type' => self::TYPE_BLOCK,
			'allowed' => self::TYPE_BOTH,
			'html' => array(
				'class' => 'align-justify'
			)
		),
		'float' => array(
			'tag' => 'div',
			'type' => self::TYPE_BLOCK,
			'allowed' => self::TYPE_BOTH,
			'attributes' => array(
				'default' => array('/left|right|none/i', 'float-{default}')
			),
			'map' => array(
				'default' => 'class'
			)
		),
		'hide' => array(
			'tag' => 'span',
			'type' => self::TYPE_BLOCK,
			'allowed' => self::TYPE_BOTH,
			'html' => array(
				'style' => 'display: none'
			)
		),
		'alert' => array(
			'tag' => 'div',
			'type' => self::TYPE_BLOCK,
			'allowed' => self::TYPE_BOTH,
			'html' => array(
				'class' => 'decoda-alert'
			)
		),
		'note' => array(
			'tag' => 'div',
			'type' => self::TYPE_BLOCK,
			'allowed' => self::TYPE_BOTH,
			'html' => array(
				'class' => 'decoda-note'
			)
		),
		'div' => array(
			'tag' => 'div',
			'type' => self::TYPE_BLOCK,
			'allowed' => self::TYPE_BOTH,
			'attributes' => array(
				'id' => '/[-_a-z0-9]+/i',
				'class' => '/[-_a-z0-9\s]+/i'
			)
		),
		'spoiler' => array(
			'template' => 'spoiler',
			'type' => self::TYPE_BLOCK,
			'allowed' => self::TYPE_BOTH
		)
	);

}
