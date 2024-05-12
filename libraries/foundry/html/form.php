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
namespace Foundry\Html;

defined('_JEXEC') or die('Unauthorized Access');

use Foundry\Html\Base;
use Foundry\Libraries\StyleSheets;
use Foundry\Libraries\Scripts;
use Joomla\CMS\HTML\HTMLHelper;

class Form extends Base
{
	/**
	 * Generates generic hidden input with actions for form submission
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function action($task = '', $controller = '', $view ='', $layout = '')
	{
		$token = \JFactory::getSession()->getFormToken();

		$theme = $this->getTemplate();
		$theme->set('controller', $controller);
		$theme->set('view', $view);
		$theme->set('token', $token);
		$theme->set('task', $task);
		$theme->set('layout', $layout);
		$output	= $theme->output('html/form/action');

		return $output;
	}

	/**
	 * Renders an article browser form
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function article($name, $value, $id = null, $attributes = [], $options = [])
	{
		if (is_null($id)) {
			$id = $name;
		}

		$articleTitle = '';

		if ($value) {
			$article = \JTable::getInstance('Content');
			$article->load((int) $value);

			$articleTitle = $article->title;
		}

		$attributes = implode(' ', $attributes);

		$theme = $this->getTemplate();
		$theme->set('articleTitle', $articleTitle);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		return $theme->output('html/form/article');
	}

	/**
	 * Renders the color picker input
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function colorpicker($name, $value = '', $revert = '')
	{
		Scripts::load('shared');

		\FH::renderColorPicker();

		$theme = $this->getTemplate();
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('revert', $revert);

		$output = $theme->output('html/form/colorpicker');

		return $output;
	}

	/**
	 *  Renders a simple checkbox form
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function checkbox($name, $checked = false, $value = 1, $id = null, $label = '', $options = [])
	{
		$inline = \FH::normalize($options, 'inline', false);
		$attributes = \FH::normalize($options, 'attributes', '');

		$theme = $this->getTemplate();
		$theme->set('attributes', $attributes);
		$theme->set('inline', $inline);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('checked', $checked);
		$theme->set('label', $label);
		$output	= $theme->output('html/form/checkbox');

		return $output;
	}

	/**
	 * Renders a datetimepicker input
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function datetimepicker($name, $value = '', $options = [])
	{
		StyleSheets::load('flatpickr');
		Scripts::load('flatpickr');

		// Figure out the language tag to be used for the picker
		static $languageTag = false;

		if (!$languageTag) {
			$languageTag = strtolower(\JFactory::getLanguage()->getTag());

			if ($languageTag !== 'zh-tw') {
				$parts = explode('-', $languageTag);
				$languageTag = $parts[0];

				if ($languageTag === 'en') {
					$languageTag = 'default';
				}
			}
		}

		if ($languageTag !== 'default') {
			$path = \FH::getMediaPath('scripts') . '/vendor/flatpickr/' . $languageTag;
			$localeScript = Scripts::appendExtension($path);

			Scripts::add($localeScript, 'others');
		}

		$id = \FH::normalize($options, 'id', $name);
		$attributes = \FH::normalize($options, 'attributes', '');
		$class = \FH::normalize($options, 'class', '');
		$appearance = \FH::normalize($options, 'appearance', 'light');
		$mode = \FH::normalize($options, 'mode', 'single');
		$enableTime = \FH::normalize($options, 'enableTime', true);

		// return empty if it return the following default value
		if ($value === '0000-00-00 00:00:00') {
			$value = '';
		}

		// Generate a unique id so that it will only be implemented once
		$uid = uniqid();

		$theme = $this->getTemplate();
		$theme->set('appearance', $appearance);
		$theme->set('languageTag', $languageTag);
		$theme->set('uid', $uid);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('name', $name);
		$theme->set('id', $id);
		$theme->set('class', $class);
		$theme->set('mode', $mode);
		$theme->set('enableTime', $enableTime);

		return $theme->output('html/form/datetimepicker');
	}

	/**
	 * Renders a dropdown input
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function dropdown($name, $selected = '', $values = [], $options = [], $useValue = false)
	{
		$id = \FH::normalize($options, 'id', '');
		$attributes = \FH::normalize($options, 'attributes', \FH::normalize($options, 'attr'));
		$class = \FH::normalize($options, 'class', '');
		$multiple = \FH::normalize($options, 'multiple', false);
		$baseClass = \FH::normalize($options, 'baseClass', '');

		$theme = $this->getTemplate();
		$theme->set('multiple', $multiple);
		$theme->set('baseClass', $baseClass);
		$theme->set('values', $values);
		$theme->set('attributes', $attributes);
		$theme->set('name', $name);
		$theme->set('id', $id);
		$theme->set('class', $class);
		$theme->set('selected', $selected);
		$theme->set('useValue', $useValue);

		return $theme->output('html/form/dropdown');
	}

	/**
	 * Renders an e-mail text input
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function email($name, $value = '', $id = null, $options = [])
	{
		$class = \FH::normalize($options, 'class', '');
		$placeholder = \FH::normalize($options, 'placeholder', '');
		$attributes = \FH::normalize($options, 'attributes', \FH::normalize($options, 'attr', ''));

		$theme = $this->getTemplate();
		$theme->set('attributes', $attributes);
		$theme->set('name', $name);
		$theme->set('id', $id);
		$theme->set('value', $value);
		$theme->set('class', $class);
		$theme->set('placeholder', $placeholder);

		return $theme->output('html/form/email');
	}

	/**
	 * Renders a dropdown list of editors for the site
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function editors($element, $selected = null, $options = [])
	{
		$editors = \FH::getEditors();
		$language = \JFactory::getLanguage();

		$default = \FH::normalize($options, 'default', 'tinymce');
		$additionalEditors = \FH::normalize($options, 'additional', []);

		// There are some cases where the editor selected is not exists in the database
		$selectedExists = false;

		foreach ($editors as $editor) {
			$language->load($editor->text . '.sys', JPATH_ADMINISTRATOR, null, false, false);
			$editor->text = \JText::_($editor->text);

			if ($selected === $editor->value) {
				$selectedExists = true;
			}
		}

		// If editor not exist, we automatically select composer as default editor.
		if (!$selectedExists) {
			$selected = $default;
		}

		$availableEditors = array_merge([], $additionalEditors);

		foreach ($editors as $editor) {
			$availableEditors[$editor->value] = $editor->text;
		}

		$theme = $this->getTemplate();
		$theme->set('availableEditors', $availableEditors);
		$theme->set('element', $element);
		$theme->set('selected', $selected);
		$output = $theme->output('html/form/editors');

		return $output;
	}

	/**
	 * Renders a floating label with input form
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function floatingLabel($label, $name, $type = 'text', $value = '', $uniqueID = false, $options = [])
	{
		$class = \FH::normalize($options, 'class', '');
		$trailIcon = \FH::normalize($options, 'trailIcon', null);
		$leadIcon = \FH::normalize($options, 'leadIcon', null);
		$autocomplete = \FH::normalize($options, 'autocomplete', false);
		$attributes = \FH::normalize($options, 'attributes', '');
		$fieldAttributes = \FH::normalize($options, 'fieldAttributes', false);
		$readOnly = \FH::normalize($options, 'readOnly', false);
		$html = \FH::normalize($options, 'html', false);
		$error = \FH::normalize($options, 'error', null);
		$errorAttributes = \FH::normalize($options, 'errorAttributes', '');

		// Backward compatibility
		if (in_array($type, ['text', 'password'])) {
			$html = false;
		}

		$class .= $leadIcon ? ' has-leading-icon' : '';
		$class .= $trailIcon ? ' has-trailing-icon' : '';

		$label = \JText::_($label);

		$id = 'fd-' . str_ireplace(['.'], '', $name);

		// DOM found elements with non-unique id.
		// https://goo.gl/9p2vKq
		if ($uniqueID) {
			$id = $uniqueID;
		}

		$theme = $this->getTemplate();
		$theme->set('type', $type);
		$theme->set('value', $value);
		$theme->set('label', $label);
		$theme->set('name', $name);
		$theme->set('id', $id);
		$theme->set('class', $class);
		$theme->set('leadIcon', $leadIcon);
		$theme->set('trailIcon', $trailIcon);
		$theme->set('autocomplete', $autocomplete);
		$theme->set('attributes', $attributes);
		$theme->set('fieldAttributes', $fieldAttributes);
		$theme->set('readOnly', $readOnly);
		$theme->set('html', $html);
		$theme->set('error', $error);
		$theme->set('errorAttributes', $errorAttributes);

		$output = $theme->output('html/form/floating.label');

		return $output;
	}
	/**
	 * Renders a hidden input on the site
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function hidden($name = '', $value = '', $id = '', $attributes = [])
	{
		if (is_array($attributes)) {
			$attributes = implode(' ', $attributes);
		}

		$theme = $this->getTemplate();
		$theme->set('attributes', $attributes);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$output = $theme->output('html/form/hidden');

		return $output;
	}

	/**
	 * Renders the label for generic forms
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function label($text, $id, $helpTitle = '', $helpContent = '', $tooltip = true, $options = [])
	{
		//$key = strtoupper($text);
		$translatedText = \JText::_($text);

		if (!$helpTitle) {
			$helpTitle = $translatedText;
		}

		if (!$helpContent) {
			$helpContent = \JText::_($text . '_DESC');
		}

		$columns = \FH::normalize($options, 'columns', 5);
		$classes = \FH::normalize($options, 'class', '');

		// Generate a short unique id for each label
		$uniqueId = substr(md5($translatedText), 0, 16);

		$theme = $this->getTemplate();
		$theme->set('columns', $columns);
		$theme->set('id', $id);
		$theme->set('uniqueId', $uniqueId);
		$theme->set('text', $translatedText);
		$theme->set('helpTitle', $helpTitle);
		$theme->set('helpContent', $helpContent);
		$theme->set('tooltip', $tooltip);
		$theme->set('classes', $classes);

		$output = $theme->output('html/form/label');

		return $output;
	}

	/**
	 * Renders the languages dropdown
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function languages($name, $selected = '', $attributes = '', $options = [])
	{
		$languages = [
			'' => 'JOPTION_SELECT_LANGUAGE'
		];

		$joomlaLanguages = \JHtml::_('contentlanguage.existing', true, true);

		foreach ($joomlaLanguages as $language) {
			$languages[$language->value] = $language->text;
		}

		$baseClass = \FH::normalize($options, 'baseClass', '');

		$theme = $this->getTemplate();
		$theme->set('baseClass', $baseClass);
		$theme->set('name', $name);
		$theme->set('selected', $selected);
		$theme->set('languages', $languages);
		$theme->set('attributes', $attributes);

		$output = $theme->output('html/form/languages');

		return $output;
	}

	/**
	 * Renders a dropdown of menus
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function menus($name, $value = '', $id = null, $options = [])
	{
		require_once(JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php');

		$items = \MenusHelper::getMenuLinks();

		$menus = [];

		foreach ($items as $menu) {
			$menus[$menu->menutype] = $menu->title;
		}

		$theme = $this->getTemplate();
		$theme->set('options', $options);
		$theme->set('menus', $menus);
		$theme->set('name', $name);
		$theme->set('value', $value);

		$output = $theme->output('html/form/menus');

		return $output;
	}

	/**
	 * Renders a select list (multiple)
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function multilist($name, $selected = '', $values = [], $options = [], $useValue = false)
	{
		$id = \FH::normalize($options, 'id', '');
		$attributes = \FH::normalize($options, 'attributes', \FH::normalize($options, 'attr'));
		$class = \FH::normalize($options, 'class', '');
		$baseClass = \FH::normalize($options, 'baseClass', '');
		$selected = is_array($selected) ? $selected : explode(',',$selected);

		$theme = $this->getTemplate();
		$theme->set('baseClass', $baseClass);
		$theme->set('values', $values);
		$theme->set('attributes', $attributes);
		$theme->set('name', $name);
		$theme->set('id', $id);
		$theme->set('class', $class);
		$theme->set('selected', $selected);
		$theme->set('useValue', $useValue);

		return $theme->output('html/form/multilist');
	}

	/**
	 * Renders a password input
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function password($name, $value = '', $id = null, $options = [])
	{
		$class = \FH::normalize($options, 'class', '');
		$placeholder = \FH::normalize($options, 'placeholder', '');
		$attributes = \FH::normalize($options, 'attributes', '');
		$autocomplete = \FH::normalize($options, 'autocomplete', false);

		// Caller wants to disable autocomplete
		if ($autocomplete === false) {
			$autocomplete = 'new-password';
		}

		$theme = $this->getTemplate();
		$theme->set('autocomplete', $autocomplete);
		$theme->set('attributes', $attributes);
		$theme->set('name', $name);
		$theme->set('id', $id);
		$theme->set('value', $value);
		$theme->set('class', $class);
		$theme->set('placeholder', $placeholder);

		return $theme->output('html/form/password');
	}

	/**
	 * Renders an ordering input
	 *
	 * @since   1.1.0
	 * @access  public
	 */
	public function ordering($name = 'ordering', $value = '')
	{
		return $this->fd->html('form.hidden', $name, $value, '', 'data-fd-table-ordering');
	}

	/**
	 * Renders an ordering direction hidden input
	 *
	 * @since   1.1.0
	 * @access  public
	 */
	public function orderingDirection($name = 'direction', $value = '')
	{
		return $this->fd->html('form.hidden', $name, $value, '', 'data-fd-table-direction');
	}

	/**
	 * Generates a hidden input for encoded return urls
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function returnUrl($name = '', $url = null)
	{
		if (!$name) {
			$name = 'currentUrl';
		}

		if (is_null($url)) {
			$app = \JFactory::getApplication();
			$url = $app->input->get($name, '', 'default');
		}

		return $this->fd->html('form.hidden', $name, $url);
	}

	/**
	 *  Renders a simple radio form
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function radio($name, $checked = false, $value = 1, $id = null, $label = '', $options = [])
	{
		// Backward compatibility
		return $this->fd->html('radio.standard', $name, $checked, $value, $id, $label, $options);
	}

	/**
	 * Renders a robots dropdown selection
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function robots($name, $selected = null, $options = [])
	{
		$baseClass = \FH::normalize($options, 'baseClass', '');

		$theme = $this->getTemplate();
		$theme->set('baseClass', $baseClass);
		$theme->set('name', $name);
		$theme->set('selected', $selected);

		$output = $theme->output('html/form/robots');
		return $output;
	}

	/**
	 * Renders a select2 input
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function select2($name, $selected = '', $values = [], $options = [], $useValue = false)
	{
		Scripts::load('select2');
		StyleSheets::load('select2');

		$attributes = \FH::normalize($options, 'attributes', '');
		$theme = \FH::normalize($options, 'theme', 'backend');
		$width = \FH::normalize($options, 'width', '');
		$appearance = \FH::normalize($options, 'appearance', $this->fd->getAppearance());
		$multiple = \FH::normalize($options, 'multiple', false);

		$extraAttributes = [
			'data-fd-select2' => $this->fd->getName(),
			'data-theme' => $theme,
			'data-appearance' => $appearance
		];

		if ($width) {
			$extraAttributes['data-width'] = $width;
		}

		if ($multiple) {
			$extraAttributes['data-fd-select2-multiple'] = '';
		}
		
		$language = \JFactory::getLanguage();

		if ($language->isRTL()) {
			$extraAttributes['data-dir'] = 'rtl';
		}

		foreach ($extraAttributes as $attributeKey => $attributeValue) {
			$attributes .= ' ' . $attributeKey . '="' . $attributeValue . '"';
		}

		$options['attributes'] = $attributes;
		
		return $this->fd->html('form.dropdown', $name, $selected, $values, $options, $useValue);
	}

	/**
	 * Renders a simple text input
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function text($name, $value = '', $id = null, $options = [])
	{
		$class = \FH::normalize($options, 'class', '');
		$customClass = \FH::normalize($options, 'customClass', '');
		$baseClass = \FH::normalize($options, 'baseClass', '');
		$placeholder = \FH::normalize($options, 'placeholder', '');
		$attributes = \FH::normalize($options, 'attributes', \FH::normalize($options, 'attr', ''));
		$help = \FH::normalize($options, 'help', false);
		$disabled = \FH::normalize($options, 'disabled', false);
		$readOnly = \FH::normalize($options, 'readOnly', false);

		$size = \FH::normalize($options, 'size', '');
		$prefix = \FH::normalize($options, 'prefix', '');
		$postfix = \FH::normalize($options, 'postfix', '');

		// Override class if custom class is available.
		if ($customClass) {
			$class = $customClass;
		}

		$theme = $this->getTemplate();
		$theme->set('size', $size);
		$theme->set('postfix', $postfix);
		$theme->set('prefix', $prefix);
		$theme->set('help', $help);
		$theme->set('attributes', $attributes);
		$theme->set('name', $name);
		$theme->set('id', $id);
		$theme->set('value', $value);
		$theme->set('class', $class);
		$theme->set('placeholder', $placeholder);
		$theme->set('disabled', $disabled);
		$theme->set('readOnly', $readOnly);
		$theme->set('baseClass', $baseClass);

		return $theme->output('html/form/text');
	}

	/**
	 * Renders a textarea input
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function textarea($name, $value = '', $id = null, $options = [])
	{
		$class = \FH::normalize($options, 'class', '');
		$placeholder = \FH::normalize($options, 'placeholder', '');
		$attributes = \FH::normalize($options, 'attributes', \FH::normalize($options, 'attr', ''));
		$rows = \FH::normalize($options, 'rows', 3);
		$baseClass = \FH::normalize($options, 'baseClass', '');

		$theme = $this->getTemplate();
		$theme->set('baseClass', $baseClass);
		$theme->set('attributes', $attributes);
		$theme->set('rows', $rows);
		$theme->set('name', $name);
		$theme->set('id', $id);
		$theme->set('value', $value);
		$theme->set('class', $class);
		$theme->set('placeholder', $placeholder);

		return $theme->output('html/form/textarea');
	}

	/**
	 * Renders a grouped text input with copy behavior
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function textCopy($name, $value = '', $id = null, $options = [])
	{
		Scripts::load('shared');

		$attributes = \FH::normalize($options, 'attributes', \FH::normalize($options, 'attr', ''));
		$class = \FH::normalize($options, 'class', '');
		$copyTooltips = \FH::normalize($options, 'tooltipsCopy', \JText::_('FD_COPY'));
		$copiedTooltips = \FH::normalize($options, 'tooltipsCopied', \JText::_('FD_COPIED'));

		$tooltips = (object) [
			'copy' => \JText::_($copyTooltips),
			'copied' => \JText::_($copiedTooltips)
		];

		$theme = $this->getTemplate();
		$theme->set('tooltips', $tooltips);
		$theme->set('attributes', $attributes);
		$theme->set('name', $name);
		$theme->set('id', $id);
		$theme->set('value', $value);
		$theme->set('class', $class);

		return $theme->output('html/form/text.copy');
	}

	/**
	 * Generates the toggler switch
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function toggler($name, $checked = false, $id = '', $attributes = '', $options = [])
	{
		Scripts::load('shared');

		$disabled = false;

		if (is_array($attributes)) {
			$attributes = implode(' ', $attributes);
		}

		// Target dependencies option allows this option to display / hide dependency items
		// based on the current toggle value
		$dependency = \FH::normalize($options, 'dependency', '');
		$dependencyValue = \FH::normalize($options, 'dependencyValue', 1);

		// Ensure it does not have any double quotes
		if ($dependency) {
			$dependency = str_ireplace('"', '\'', $dependency);
		}

		// Determines if the input has been disabled
		$disabled = \FH::normalize($options, 'disabled', false);
		$disabledDesc = \FH::normalize($options, 'disabledDesc', '');
		$disabledTitle = \FH::normalize($options, 'disabledTitle', '');

		if (!$id) {
			$id = $name;
		}

		$theme = $this->getTemplate();
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('checked', $checked);
		$theme->set('disabled', $disabled);
		$theme->set('disabledTitle', $disabledTitle);
		$theme->set('disabledDesc', $disabledDesc);
		$theme->set('attributes', $attributes);
		$theme->set('dependency', $dependency);
		$theme->set('dependencyValue', $dependencyValue);

		$output = $theme->output('html/form/toggler');

		return $output;
	}

	/**
	 * Renders a hidden input with Joomla's token
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function token()
	{
		$token = \JFactory::getSession()->getFormToken();

		$theme = $this->getTemplate();
		$theme->set('token', $token);
		$output = $theme->output('html/form/token');

		return $output;
	}

	/**
	 * Renders user browser form
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function user($name, $value, $id = null, $options = [])
	{
		Scripts::load('shared');

		$userName = '';
		$attributes = \FH::normalize($options, 'attributes', '');
		$browseTitle = \FH::normalize($options, 'browseTitle', \JText::_('FD_BROWSE'));
		$columns = \FH::normalize($options, 'columns', 10);

		// Id cannot be null since it is used to link the hidden input
		if (!$id) {
			$id = $name;
		}

		if ($value) {
			$user = \JFactory::getUser((int) $value);
			$userName = $user->name;
		}

		$theme = $this->getTemplate();
		$theme->set('columns', $columns);
		$theme->set('browseTitle', $browseTitle);
		$theme->set('id', $id);
		$theme->set('userName', $userName);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		return $theme->output('html/form/user');
	}

	/**
	 * Renders the user groups tree checkbox
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function userGroupsTree($name, $selected = '', $checkSuperAdmin = false)
	{
		if (is_string($selected)) {
			$selected = explode(',', $selected);
		}

		$groups = \FH::getUserGroupsTree();
		
		$theme = $this->getTemplate();
		$theme->set('name', $name);
		$theme->set('checkSuperAdmin', $checkSuperAdmin);
		$theme->set('selected', $selected);
		$theme->set('groups', $groups);

		return $theme->output('html/form/user.groups.tree');
	}

	/**
	 * Renders a dropdown list of languages available to the user
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function userLanguages($name, $selected)
	{
		$availableLanguages = \FH::getLanguages();

		$languages = [
			'' => 'FD_USE_DEFAULT'
		];

		foreach ($availableLanguages as $language) {
			$languages[$language->value] = $language->text;
		}

		$theme = $this->getTemplate();
		$theme->set('name', $name);
		$theme->set('selected', $selected);
		$theme->set('languages', $languages);

		return $theme->output('html/form/user.languages');
	}

	/**
	 * Renders a dropdown list of user timezone available to the user
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function userTimezone($name, $selected)
	{
		$joomlaTimezones = \FH::getJoomlaTimezones();

		$timezones = [
			'UTC' => 'FD_USE_DEFAULT'
		];

		foreach ($joomlaTimezones as $group => $countries) {
			$timezones[$group] = [];

			foreach ($countries as $country) {
				$timezones[$group][$country] = $country;
			}
		}

		// UTC timezone is only meant for server.
		if ($selected == null || $selected === '') {
			$selected = 'UTC';
		}

		$theme = $this->getTemplate();
		$theme->set('name', $name);
		$theme->set('selected', $selected);
		$theme->set('timezones', $timezones);

		return $theme->output('html/form/user.timezone');
	}

	/**
	 * Renders the emoji picker markup
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function emoji($options = [])
	{
		Scripts::load('perfect-scrollbar');
		Scripts::load('popper');
		Scripts::load('tippy');
		Scripts::load('emoji');

		$appearance = \FH::normalize($options, 'appearance', 'light');
		$accent = \FH::normalize($options, 'theme', 'foundry');

		$theme = $this->getTemplate();
		$theme->set('appearance', $appearance);
		$theme->set('accent', $accent);

		$output = $theme->output('html/form/emoji');

		return $output;
	}

	/**
	 * Renders a date range form
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function dateRange($selected = '', $name = 'dateRange', $placeholder = '', $options = [])
	{
		$options = array_merge($options, ['submitonclick' => false]);

		return $this->fd->html('filter.dateRange', $selected, $name, $placeholder, $options);
	}
}
