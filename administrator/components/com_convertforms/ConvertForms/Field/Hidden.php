<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ConvertForms\Field;

defined('_JEXEC') or die('Restricted access');

class Hidden extends \ConvertForms\Field
{
	/**
	 *  Remove common fields from the form rendering
	 *
	 *  @var  mixed
	 */
	protected $excludeFields = [
		'label',
		'placeholder',
		'description',
		'cssclass',
		'hidelabel',
		'browserautocomplete',
		'size',
		'inputcssclass'
	];

	/**
	 * Indicates the default required behavior on the form
	 *
	 * @var bool
	 */
	protected $required = false; 

	/**
	 *  Set field object
	 *
	 *  @param  mixed  $field  Object or Array Field options
	 */
	public function setField($field)
	{
		parent::setField($field);

		// Always hide the container of a hidden field
		$this->field->cssclass = 'cf-hide';

		return $this;
	}
}

?>