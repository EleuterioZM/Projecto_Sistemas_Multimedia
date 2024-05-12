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

class Email extends Base
{
	/**
	 * Generates an attachment in the e-mail content
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function attachment($link, $title)
	{
		$theme = $this->getTemplate();
		$theme->set('link', $link);
		$theme->set('title', $title);
		$html = $theme->output('html/email/attachment');
		
		return $html;
	}

	/**
	 * Generates a blog post preview
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function blog($title, $content, $date = '', $authorName = '', $authorLink = '', $authorAvatar = '')
	{
		$author = false;

		if ($authorName) {
			$author = (object) [
				'name' => $authorName,
				'link' => $authorLink,
				'avatar' => $authorAvatar
			];
		}

		$theme = $this->getTemplate();
		$theme->set('author', $author);
		$theme->set('date', $date);
		$theme->set('title', $title);
		$theme->set('content', $content);

		$html = $theme->output('html/email/blog');

		return $html;
	}

	/**
	 * Generates a blog post preview
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function comment($comment, $date = '', $authorName = '', $authorLink = 'javascript:void(0)')
	{
		$author = false;

		if ($authorName) {
			$author = (object) [
				'name' => $authorName,
				'link' => $authorLink
			];
		}

		$theme = $this->getTemplate();
		$theme->set('author', $author);
		$theme->set('date', $date);
		$theme->set('comment', $comment);

		$html = $theme->output('html/email/comment');

		return $html;
	}

	/**
	 * Generates a button in the e-mail template
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function button($text, $link, $style = 'default')
	{
		$allowedStyles = [
			'primary',
			'danger',
			'default'
		];

		$buttonColors = [
			'primary' => [
				'background' => '#4e72e2',
				'text' => '#fff',
				'border' => false
			],
			'danger' => [
				'background' => '#d9534f',
				'text' => '#fff',
				'border' => false
			],
			'default' => [
				'background' => '#fff',
				'text' => '#4e72e2',
				'border' => '#E1E4ED'
			]
		];

		$buttonColor = $buttonColors['default'];

		if (in_array($style, $allowedStyles)) {
			$buttonColor = (object) $buttonColors[$style];
		}

		$theme = $this->getTemplate();
		$theme->set('text', $text);
		$theme->set('link', $link);
		$theme->set('buttonColor', $buttonColor);
		$html = $theme->output('html/email/button');

		return $html;
	}

	/**
	 * Generates a standard content with wrapper for e-mails
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function content($content, $style = 'clear', $options = [])
	{
		$content = \JText::_($content);
		$previewContent = \FH::normalize($options, 'previewContent', 'Lorem Ipsum is simply dummy text of the printing');
		$spacer = \FH::normalize($options, 'spacer', true);

		$theme = $this->getTemplate();
		$theme->set('content', $content);
		$theme->set('spacer', $spacer);

		$namespace = 'content.' . $style;

		$html = $theme->output('html/email/' . $namespace);

		return $html;
	}

	/**
	 * Generates the divider section of an e-mail
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function divider()
	{
		static $html = null;

		if (is_null($html)) {
			$theme = $this->getTemplate();
			$html = $theme->output('html/email/divider');
		}

		return $html;
	}

	/**
	 * Generates the heading section of an e-mail
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function heading($title, $subtitle = '')
	{
		$title = \JText::_($title);
		$subtitle = \JText::_($subtitle);

		$theme = $this->getTemplate();
		$theme->set('title', $title);
		$theme->set('subtitle', $subtitle);

		$html = $theme->output('html/email/heading');

		return $html;
	}

	/**
	 * Generates the logo section of an e-mail
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function logo($logo)
	{
		static $html = null;

		if (is_null($html)) {
			$theme = $this->getTemplate();
			$theme->set('logo', $logo);
			$html = $theme->output('html/email/logo');
		}

		return $html;
	}

	/**
	 * Generates a section heading in an e-mail
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function sectionHeading($title, $subtitle = '')
	{
		$theme = $this->getTemplate();
		$theme->set('title', $title);
		$theme->set('subtitle', $subtitle);
		$html = $theme->output('html/email/section.heading');

		return $html;
	}

	/**
	 * Generates the logo section of an e-mail
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function spacer()
	{
		static $html = null;

		if (is_null($html)) {
			$theme = $this->getTemplate();
			$html = $theme->output('html/email/spacer');
		}

		return $html;
	}

	/**
	 * Generates the date string section used in digest email
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function digestDate($datestr = '')
	{
		if (!$datestr) {
			$datestr = \FH::date()->format('l, d F Y');
		}

		$theme = $this->getTemplate();
		$theme->set('datestring', $datestr);
		$html = $theme->output('html/email/digest.date');

		return $html;
	}

	/**
	 * Generates the digest item used in digest email
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function digestItem($title, $content = '', $options = [])
	{
		$title = \JText::_($title);
		$content = \JText::_($content);
		$icon = \FH::normalize($options, 'icon', false);
		$divider = \FH::normalize($options, 'divider', false);

		$theme = $this->getTemplate();
		$theme->set('title', $title);
		$theme->set('content', $content);
		$theme->set('icon', $icon);
		$theme->set('divider', $divider);

		$html = $theme->output('html/email/digest.item');

		return $html;
	}

	/**
	 * Generates the unsubscribe link in email
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function unsubscribe($unsubcribeLink, $linkLabel = null, $text = null)
	{
		if (!$linkLabel) {
			$linkLabel = 'FD_EMAILS_UNSUBSCRIBE_LINK_LABEL';
		}

		// if calller pass in an empty string, this mean caller do now want to display the text.
		// so we will use is_null to test against the string.
		if (is_null($text)) {
			$text = 'FD_EMAILS_UNSUBSCRIBE_TEXT';
		}

		$text = \JText::_($text);
		$linkLabel = \JText::_($linkLabel);

		$theme = $this->getTemplate();
		$theme->set('unsubcribeLink', $unsubcribeLink);
		$theme->set('linkLabel', $linkLabel);
		$theme->set('text', $text);

		$html = $theme->output('html/email/unsubscribe');

		return $html;
	}
}