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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

/**
 * TextFilter
 *
 * Provides tags for text and font styling.
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/php/decoda
 */

class TextFilter extends DecodaFilter {

	/**
	 * Supported tags.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_tags = array(
		'font' => array(
			'tag' => 'span',
			'type' => self::TYPE_INLINE,
			'allowed' => self::TYPE_INLINE,
			'attributes' => array(
				'default' => array('(.*?)', 'font-family: {default}')
			),
			'map' => array(
				'default' => 'style'
			)
		),
		'size' => array(
			'tag' => 'span',
			'type' => self::TYPE_INLINE,
			'allowed' => self::TYPE_INLINE,
			'attributes' => array(
				'default' => array('/[1-2]{1}[0-9]{1}/', 'font-size: {default}px'),
			),
			'map' => array(
				'default' => 'style'
			)
		),
		'color' => array(
			'tag' => 'span',
			'type' => self::TYPE_INLINE,
			'allowed' => self::TYPE_INLINE,
			'attributes' => array(
				'default' => array('/(?:#[0-9a-f]{3,6}|[a-z]+)/i', 'color: {default}'),
			),
			'map' => array(
				'default' => 'style'
			)
		),
		'h1' => array(
			'tag' => 'h1',
			'type' => self::TYPE_BLOCK,
			'allowed' => self::TYPE_INLINE
		),
		'h2' => array(
			'tag' => 'h2',
			'type' => self::TYPE_BLOCK,
			'allowed' => self::TYPE_INLINE
		),
		'h3' => array(
			'tag' => 'h3',
			'type' => self::TYPE_BLOCK,
			'allowed' => self::TYPE_INLINE
		),
		'h4' => array(
			'tag' => 'h4',
			'type' => self::TYPE_BLOCK,
			'allowed' => self::TYPE_INLINE
		),
		'h5' => array(
			'tag' => 'h5',
			'type' => self::TYPE_BLOCK,
			'allowed' => self::TYPE_INLINE
		),
		'h6' => array(
			'tag' => 'h6',
			'type' => self::TYPE_BLOCK,
			'allowed' => self::TYPE_INLINE
		)
	);

}
