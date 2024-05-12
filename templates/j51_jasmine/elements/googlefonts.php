<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Form Field class for the Joomla Platform.
 * Supports a generic list of options.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldGoogleFonts extends JFormField

{
	
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'GoogleFonts';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	

	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';
		
		// Initialize some field attributes.
		$attr .= 'class="form-select"';
		
		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true')
		{
			$attr .= ' disabled="disabled"';
		}
		
		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';

		// Get the field options.
		$options = (array) $this->getOptions();

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true')
		{
			$html[] = HTMLHelper::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
		}
		// Create a regular list.
		else
		{
			$html[] = HTMLHelper::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		}

		return implode($html);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
// 		$lines = file(JPATH_ROOT.DS.'php'.DS.'googlefonts.php');
		// Initialize variables.
		$googleFonts = array();

		foreach ($this->element->children() as $googlefonts)
		{
    $googleFonts['Arial, sans-serif'] = '--------Web Safe Fonts--------';
    $googleFonts['Arial, Helvetica, sans-serif'] = 'Arial';
		$googleFonts['Courier, monospace'] = 'Courier';
		$googleFonts['Garamond, serif'] = 'Garamond';
		$googleFonts['Georgia, serif'] = 'Georgia';
		$googleFonts['Impact, Charcoal, sans-serif'] = 'Impact';
		$googleFonts['Lucida Console, Monaco, monospace'] = 'Lucida Console';
		$googleFonts['Lucida Sans Unicode, Lucida Grande, sans-serif'] = 'Lucida Sans Unicode';
		$googleFonts['MS Sans Serif, Geneva, sans-serif'] = 'MS Sans Serif';
		$googleFonts['MS Serif, New York, sans-serif'] = 'MS Serif';
		$googleFonts['Palatino Linotype, Book Antiqua, Palatino, serif'] = 'Palatino Linotype';
		$googleFonts['Tahoma, Geneva, sans-serif'] = 'Tahoma';
		$googleFonts['Times New Roman, Times, serif'] = 'Times New Roman';
		$googleFonts['Trebuchet MS, Helvetica, sans-serif'] = 'Trebuchet MS';
		$googleFonts['Verdana, Geneva, sans-serif'] = 'Verdana';
		$googleFonts['Arial'] = '----------Google Fonts----------';
		$googleFonts['ABeeZee'] = 'ABeeZee';
		$googleFonts['Actor'] = 'Actor';
		$googleFonts['Abril+Fatface'] = 'Abril Fatface';
		$googleFonts['Allan'] = 'Allan';
		$googleFonts['Allerta'] = 'Allerta';
		$googleFonts['Allerta+Stencil'] = 'Allerta Stencil';
		$googleFonts['Anonymous+Pro'] = 'Anonymous Pro';
		$googleFonts['Amiri'] = 'Amiri';
		$googleFonts['Anton'] = 'Anton';
		$googleFonts['Arimo'] = 'Arimo';
		$googleFonts['Archivo+Black'] = 'Archivo Black';
		$googleFonts['Arvo'] = 'Arvo';
		$googleFonts['Assistant'] = 'Assistant';
		$googleFonts['Astloch'] = 'Astloch';
		$googleFonts['Barlow+Semi+Condensed'] = 'Barlow Semi Condensed';
		$googleFonts['Bentham'] = 'Bentham';
		$googleFonts['Bevan'] = 'Bevan';
		$googleFonts['Buda'] = 'Buda';
		$googleFonts['Cabin'] = 'Cabin';
		$googleFonts['Calligraffitti'] = 'Calligraffitti';	
		$googleFonts['Cantarell'] = 'Cantarell';
		$googleFonts['Cardo'] = 'Cardo';	
		$googleFonts['Carme'] = 'Carme';	
		$googleFonts['Catamaran'] = 'Catamaran';	
		$googleFonts['Cherry+Cream+Soda'] = 'Cherry Cream Soda';
		$googleFonts['Chewy'] = 'Chewy';
		$googleFonts['Coda'] = 'Coda';	
		$googleFonts['Coming+Soon'] = 'Coming Soon';
		$googleFonts['Comfortaa'] = 'Comfortaa';
		$googleFonts['Copse'] = 'Copse';
		$googleFonts['Corben'] = 'Corben';
		$googleFonts['Cormorant'] = 'Cormorant';
		$googleFonts['Cookie'] = 'Cookie';
		$googleFonts['Cousine'] = 'Cousine';
		$googleFonts['Covered+By+Your+Grace'] = 'Covered By Your Grace';
		$googleFonts['Crafty+Girls'] = 'Crafty Girls';
		$googleFonts['Crimson+Text'] = 'Crimson Text';
		$googleFonts['Crushed'] = 'Crushed';
		$googleFonts['Cuprum'] = 'Cuprum';	
		$googleFonts['Cutive'] = 'Cutive';
		$googleFonts['Dosis'] = 'Dosis';
		$googleFonts['Dancing+Script'] = 'Dancing Script';
		$googleFonts['Droid+Sans'] = 'Droid Sans';
		$googleFonts['Droid+Sans Mono'] = 'Droid Sans Mono';
		$googleFonts['Droid+Serif'] = 'Droid Serif';
		$googleFonts['Economica'] = 'Economica';
		$googleFonts['Erica+One'] = 'Erica One';
		$googleFonts['Expletus+Sans'] = 'Expletus Sans';
		$googleFonts['Fontdiner+Swanky'] = 'Fontdiner Swanky';
		$googleFonts['Fira+Sans'] = 'Fira Sans';
		$googleFonts['Geo'] = 'Geo';
		$googleFonts['Goudy+Bookletter 1911'] = 'Goudy Bookletter 1911';	
		$googleFonts['Grand+Hotel'] = 'Grand Hotel';
		$googleFonts['Great+Vibes'] = 'Great Vibes';
		$googleFonts['Gruppo'] = 'Gruppo';	
		$googleFonts['Hammersmith+One'] = 'Hammersmith One';		
		$googleFonts['Hind'] = 'Hind';
		$googleFonts['Homemade+Apple'] = 'Homemade Apple';
		$googleFonts['Helvetica'] = 'Helvetica';
		$googleFonts['IM+Fell'] = 'IM Fell';
		$googleFonts['Inconsolata'] = 'Inconsolata';
		$googleFonts['Irish+Grover'] = 'Irish Grover';
		$googleFonts['Jomhuria'] = 'Jomhuria';
		$googleFonts['Josefin+Slab'] = 'Josefin Slab';
		$googleFonts['Josefin+Sans'] = 'Josefin Sans';
		$googleFonts['Josefin+Sans+Std Light'] = 'Josefin Sans Std Light';
		$googleFonts['Junge'] = 'Junge';	
		$googleFonts['Just+Another+Hand'] = 'Just Another Hand';
		$googleFonts['Just+Me+Again+Down+Here'] = 'Just Me Again Down Here';	
		$googleFonts['Kenia'] = 'Kenia';
		$googleFonts['Kranky'] = 'Kranky';
		$googleFonts['Kreon'] = 'Kreon';
		$googleFonts['Kristi'] = 'Kristi';
		$googleFonts['Lato'] = 'Lato';
		$googleFonts['Lekton'] = 'Lekton';
		$googleFonts['Lobster'] = 'Lobster';
		$googleFonts['Lora'] = 'Lora';
		$googleFonts['Luckiest+Guy'] = 'Luckiest Guy';
		$googleFonts['Mako'] = 'Mako';
		$googleFonts['Meddon'] = 'Meddon';
		$googleFonts['Merriweather'] = 'Merriweather';
		$googleFonts['Metrophobic'] = 'Metrophobic'; 
		$googleFonts['Michroma'] = 'Michroma';
		$googleFonts['Molengo'] = 'Molengo';
		$googleFonts['Montserrat'] = 'Montserrat';
		$googleFonts['Montserrat+Alternates'] = 'Montserrat Alternates';
		$googleFonts['Mountains+of+Christmas'] = 'Mountains of Christmas';
		$googleFonts['Muli'] = 'Muli';
		$googleFonts['Noto+Sans'] = 'Noto Sans';
		$googleFonts['Neucha'] = 'Neucha';
		$googleFonts['Neuton'] = 'Neuton';
		$googleFonts['Nobile'] = 'Nobile';
		$googleFonts['Nobile'] = 'Nobile';
		$googleFonts['Nunito'] = 'Nunito';
		$googleFonts['OFL+Sorts+Mill+Goudy+TT'] = 'OFL Sorts Mill Goudy TT';
		$googleFonts['Old+Standard+TT'] = 'Old Standard TT';
		$googleFonts['Open+Sans'] = 'Open Sans';
		$googleFonts['Oranienbaum'] = 'Oranienbaum';
		$googleFonts['Orbitron'] = 'Orbitron';
		$googleFonts['Oswald'] = 'Oswald';
		$googleFonts['Pacifico'] = 'Pacifico';
		$googleFonts['Passion+One'] = 'Passion One';
		$googleFonts['Pathway+Gothic+One'] = 'Pathway Gothic One';
		$googleFonts['Permanent+Marker'] = 'Permanent Marker';
		$googleFonts['Playfair+Display'] = 'Playfair Display';
		$googleFonts['Philosopher'] = 'Philosopher';
		$googleFonts['Poppins'] = 'Poppins';
		$googleFonts['PT+Sans'] = 'PT Sans';
		$googleFonts['PT+Serif'] = 'PT Serif';
		$googleFonts['Poiret+One'] = 'Poiret One';
		$googleFonts['Puritan'] = 'Puritan';
		$googleFonts['Questrial'] = 'Questrial';
		$googleFonts['Radley'] = 'Radley';	
		$googleFonts['Raleway'] = 'Raleway';
		$googleFonts['Reenie+Beanie'] = 'Reenie Beanie';
		$googleFonts['Roboto'] = 'Roboto';
		$googleFonts['Roboto+Condensed'] = 'Roboto Condensed';
		$googleFonts['Rock+Salt'] = 'Rock Salt';
		$googleFonts['Rubik'] = 'Rubik';
		$googleFonts['Sans-Serif'] = 'Sans-Serif';
		$googleFonts['Schoolbell'] = 'Schoolbell';
		$googleFonts['Share'] = 'Share';
		$googleFonts['Slackey'] = 'Slackey';
		$googleFonts['Sniglet'] = 'Sniglet';
		$googleFonts['Source+Sans+Pro'] = 'Source Sans Pro';
		$googleFonts['Sunshiney'] = 'Sunshiney';
		$googleFonts['Syncopate'] = 'Syncopate';
		$googleFonts['Tangerine'] = 'Tangerine';
		$googleFonts['Tinos'] = 'Tinos';
		$googleFonts['Titillium+Web'] = 'Titillium Web';
		$googleFonts['Trirong'] = 'Trirong';
		$googleFonts['Ubuntu'] = 'Ubuntu';
		$googleFonts['UnifrakturCook'] = 'UnifrakturCook';
		$googleFonts['UnifrakturMaguntia'] = 'UnifrakturMaguntia';
		$googleFonts['Unkempt'] = 'Unkempt';
		$googleFonts['Vibur'] = 'Vibur';	
		$googleFonts['Vollkorn'] = 'Vollkorn';
		$googleFonts['VT323'] = 'VT323';
		$googleFonts['Walter+Turncoat'] = 'Walter Turncoat';	
		$googleFonts['Yanone+Kaffeesatz'] = 'Yanone Kaffeesatz';
		
		}

		reset($googlefonts);

		return $googleFonts;
	}
}
