<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework;

defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 *  Fonts Class
 */
class Fonts
{
	/**
	 *  Classic Fonts
	 *
	 *  @var  array
	 */
	private static $classic = array(
		'Arial',
		'Arial Black',
		'Georgia',
		'Tahoma',
		'Franklin Gothic Medium',
		'Calibri',
		'Cambria',
		'Century Gothic',
		'Consolas',
		'Corbel',
		'Courier New',
		'Times New Roman',
		'Impact',
		'Lucida Console',
		'Palatino Linotype',
		'Trebuchet MS',
		'Verdana'
	);

	/**
	 *  Google Fonts List
	 *
	 *  @var  array
	 */
	private static $google = array(
		'Roboto',
		'Staatliches',
		'Thasadith',
		'Open Sans',
		'Sarabun',
		'Slabo 27px',
		'Lato',
		'Oswald',
		'Charm',
		'Roboto Condensed',
		'Source Sans Pro',
		'Montserrat',
		'Raleway',
		'PT Sans',
		'Poppins',
		'Roboto Slab',
		'Lora',
		'Droid Sans',
		'Merriweather',
		'Ubuntu',
		'Droid Serif',
		'Arimo',
		'Noto Sans',
		'PT Sans Narro'
	);

	/**
	 *  Returns all font groups alphabetically sorted
	 *
	 *  @return  array
	 */
	public static function getFontGroups()
	{
		return array(
			'Google Fonts' => self::getFontGroup('google'),
			'Classic' 	   => self::getFontGroup('classic')
		);
	}

	/**
	 *  Returns a font group alphabetically sorted
	 *
	 *  @param   string  $name  The Font Group
	 *
	 *  @return  array         
	 */
	public static function getFontGroup($name)
	{
		$fonts = self::$$name;
		sort($fonts);
		return $fonts;
	}

	/**
	 *  Loads Google font to the document
	 *
	 *  @param   mixed  $name  The Google font name
	 *
	 *  @return  void
	 */
	public static function loadFont($names)
	{
		if (!$names)
		{
			return;
		}

		if (!is_array($names))
		{
			$names = array($names);
		}

		foreach ($names as $key => $value)
		{
			// If font is a Google Font then load it into the document
	        if (in_array($value, self::$google))
	        {
	            \JFactory::getDocument()->addStylesheet('//fonts.googleapis.com/css?family=' . urlencode($value));
	        }
		}
	}
}