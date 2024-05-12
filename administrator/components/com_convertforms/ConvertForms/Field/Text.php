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

use Joomla\String\StringHelper;

defined('_JEXEC') or die('Restricted access');

class Text extends \ConvertForms\Field
{
    
    /**
	 *  Validate form submitted value
	 *
	 *  @param   mixed  $value           The field's value to validate (Passed by reference)
	 *
	 *  @return  mixed                   True on success, throws an exception on error
	 */
	public function validate(&$value)
	{
		$isEmpty = $this->isEmpty($value);
		$isRequired = $this->field->get('required');

		// If the field is empty and its not required, skip validation
		if ($isEmpty && !$isRequired)
		{
			return;
		}

		if ($isEmpty && $isRequired)
		{
			$this->throwError(\JText::_('COM_CONVERTFORMS_FIELD_REQUIRED'), $field_options);
		}

		// Validate Min / Max characters
		$min_chars = $this->field->get('minchars', 0);
		$max_chars = $this->field->get('maxchars', 0);
		$value_length = StringHelper::strlen($value);

		if ($min_chars > 0 && $value_length < $min_chars)
		{
			$this->throwError(\JText::sprintf('COM_CONVERTFORMS_FIELD_VALIDATION_MIN_CHARS', $min_chars, $value_length), $field_options);
		}

		if ($max_chars > 0 && $value_length > $max_chars)
		{
			$this->throwError(\JText::sprintf('COM_CONVERTFORMS_FIELD_VALIDATION_MAX_CHARS', $max_chars, $value_length), $field_options);
		}

		// Validate Min / Max words
		$min_words = $this->field->get('minwords', 0);
		$max_words = $this->field->get('maxwords', 0);

		// Find words count
		$words_temp = preg_replace('/\s+/', ' ', trim($value));
		$words_temp = array_filter(explode(' ', $words_temp));
		$words_count = count($words_temp);

		if ($min_words > 0 && $words_count < $min_words)
		{
			$this->throwError(\JText::sprintf('COM_CONVERTFORMS_FIELD_VALIDATION_MIN_WORDS', $min_words, $words_count), $field_options);
		}

		if ($max_words > 0 && $words_count > $max_words)
		{
			$this->throwError(\JText::sprintf('COM_CONVERTFORMS_FIELD_VALIDATION_MAX_WORDS', $max_words, $words_count), $field_options);
		}

		// Let's do some filtering.
		$value = $this->filterInput($value);
	}
}

?>