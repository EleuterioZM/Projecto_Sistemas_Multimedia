<?php
/**
* @package		Komento
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

use Foundry\Libraries\VideoParser;

class KomentoParser
{
	protected $emojiPath = [
		'path' => '',
		'originalPath' => ''
	];

	protected $emoticons = [];
	protected $emojiMap = [];

	public function __construct()
	{
		$this->config = KT::config();
	}

	/**
	 * Remove the BBcodes which has no value with empty string
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function removeEmptyBBcodes($comment)
	{
		$bbcodes = [
			'/\[b\]\[\/b\]/ms',
			'/\[i\]\[\/i\]/ms',
			'/\[u\]\[\/u\]/ms',
			'/\[img\]\[\/img\]/ms',
			'/\[quote\]\[\/quote\]/ms',
			'/\[spoiler\]\[\/spoiler\]/ms',
			'/\[code type=\"(.*?)\"\]\[\/code\]/ms'
		];

		$comment = preg_replace($bbcodes, '', $comment);

		return $comment;
	}

	public function parseComment($text)
	{
		// Remove the empty bbcodes first
		$text = $this->removeEmptyBBcodes($text);

		// word censoring
		$text = $this->parseCensor($text);

		// parseBBcode to HTML
		if ($this->config->get('enable_bbcode')) {
			$text = $this->parseBBCode($text);
		}

		return $text;
	}

	public function parseBBCode($text)
	{
		$maxdimension = ' style="max-width:100%"';

		if (!$this->config->get('enable_media_max_width') && ($this->config->get('max_image_width') || $this->config->get('max_image_height'))) {

			$maxdimension = ' style="';

			if ($this->config->get('max_image_width')) {
				$maxdimension .= 'max-width:' . $this->config->get('max_image_width') . 'px;';
			}

			if ($this->config->get('max_image_height')) {
				$maxdimension .= 'max-height:' . $this->config->get('max_image_height') . 'px;';
			}

			$maxdimension .= '"';
		}

		// Converts all html entities properly
		$text = htmlspecialchars($text , ENT_NOQUOTES);
		$text = trim($text);

		// Replace [code] blocks
		if ($this->config->get('bbcode_code')) {
			$text = $this->replaceCodes($text);
		}

		if ($this->config->get('bbcode_giphy')) {
			$text = $this->replaceGiphy($text);
		}

		if ($this->config->get('bbcode_quote')) {
			$text = $this->replaceQuote($text);
		}

		$text = preg_replace_callback('/\[code(type="(.*?)")?\](.*?)\[\/code\]/ms', ['KomentoParser', 'escape'], $text);

		// avoid smileys in pre tag gets replaced
		$text = $this->encodePre($text);

		// BBCode to find...
		$in = [
			'/\[b\](.*?)\[\/b\]/ms',
			'/\[i\](.*?)\[\/i\]/ms',
			'/\[u\](.*?)\[\/u\]/ms',
			'/\[img\]((http|https):\/\/([a-z0-9\%._\s\*_\/+-?]+)\.(jpg|JPG|jpeg|JPEG|png|PNG|gif|GIF).*?)\[\/img]/ims',
			'/\[s\](.*?)\[\/s\]/ims'
		];

		// And replace them by...
		$out = [
			'<b>\1</b>',
			'<em>\1</em>',
			'<u>\1</u>',
			'<img src="\1" alt="\1"' . $maxdimension . ' />',
			'<del>\1</del>'
		];

		// strip out bbcode data first
		$tmp = preg_replace($in, '', $text);

		// strip out bbcode url data
		$urlin = '/\[url\="?(.*?)"?\](.*?)\[\/url\]/ms';
		$tmp = preg_replace($urlin, '', $tmp);

		// strip out video links too
		$tmp = VideoParser::strip($tmp);

		// replace video links
		if ($this->config->get('bbcode_video')) {
			$text = VideoParser::replace($text, $this->config->get('bbcode_video_width'), $this->config->get('bbcode_video_height'), (bool) $this->config->get('enable_media_max_width'));
		}

		// replace bbcode with html
		$text = preg_replace($in, $out, $text);

		// Replace url bbcode with html
		$text = $this->replaceBBUrl($text);

		// manual fix for unwrapped li issue
		$text = $this->replaceBBList($text);

		// Check if the content still contain [img] tag
		// if yes, dont allow to replace to hyperlink for prevent XSS attack
		// Always let system to process bbcode replacement first before process this replace URL part
		if ((strpos($tmp, '[img]') === false || strpos($tmp, '[/img]') === false) && $this->config->get('auto_hyperlink')) {
			$text = $this->replaceURL($tmp, $text);
		}

		// change new line to br (without affecting pre)
		$text = nl2br($text);

		// done parsing emoticons and bbcode, decode pre text back
		$text = $this->decodePre($text);

		// paragraphs
		$text = str_replace("\r", "", $text);
		// $text = "<p>".preg_replace("/(\n){2,}/", "</p><p>", $text)."</p>";
		$text = preg_replace_callback('/<ul>(.*?)<\/ul>/ms', ['KomentoParser','removeBr'], $text);

		// fix [list] within [*] causing dom errors
		$text = preg_replace('/<li>(.*?)<ul>(.*?)<\/ul>(.*?)<\/li>/ms', "\\1<ul>\\2</ul>\\3", $text);
		$text = preg_replace('/<p><ul>(.*?)<\/ul><\/p>/ms', "<ul>\\1</ul>", $text);

		// Replace [spoiler] block
		if ($this->config->get('bbcode_spoiler')) {
			$text = $this->replaceSpoiler($text);
		}

		// Replace mentions
		if ($this->config->get('enable_mention')) {
			$text = $this->replaceMention($text);
		}

		return $text;
	}

	/**
	 * Replace mentions
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function replaceMention($text)
	{
		$users = KT::string()->detectNames($text);

		if (!$users) {
			return $text;
		}

		foreach ($users as $user) {
			$link = $user->getPermalink();
			$name = htmlentities($user->getName());

			$search = ['@' . $name . '­'];
			$replace = '<a href="' . $link . '">' . $name . '</a>';

			if ($this->config->get('layout_avatar_integration') === 'easysocial' && $this->config->get('easysocial_profile_popbox')) {
				$replace = '<a href="' . $link . '" data-popbox="module://easysocial/profile/popbox" data-popbox-position="top-left" data-user-id="' . $user->id . '" class="mentions-user">' . $name . '</a>';
			}

			$text = str_ireplace($search, $replace, $text);
		}

		return $text;
	}

	/**
	 * Callback to process smileys
	 *
	 * @since	3.1.4
	 * @access	public
	 */
	protected function emoticonCallback($matches) {
		$smiley = trim($matches[0]);
		$baseUrl = rtrim(JURI::root(), '/');

		if (count($matches) === 1 && isset($this->emojiMap[$smiley])) {
			$image = '<img class="emoji" src="' . $baseUrl . $this->emojiPath['path'] . $this->emojiMap[$smiley] . '.png" alt="" width="20" height="20">';

			return $image;
		}

		if (count($matches) === 1 || !isset($this->emojiMap[$smiley])) {
			return $matches[0];
		}

		$l = isset($matches[1]) ? $matches[1] : '';
		$r = isset($matches[2]) ? $matches[2] : '';
		
		$smileyPath = $baseUrl . $this->emojiPath['path'] . $this->emojiMap[$smiley] . '.png';

		// Check if the emoji is exists if override folder exists.
		if ($this->emojiPath['path'] != $this->emojiPath['originalPath']) {

			$overrideFile = JPATH_ROOT . $smileyPath;

			// Override file is not exists
			if (!JFile::exists($overrideFile)) {
				$smileyPath = $baseUrl . $this->emojiPath['originalPath'] . $this->emojiMap[$smiley] . '.png';
			}
		}

		$image = '<img class="emoji" src="' . $smileyPath . '" alt="" width="20" height="20">';

		return $l . $image . $r;
	}


	public function parseCensor($text)
	{

		if (!$this->config->get('filter_word')) {
			return $text;
		}

		// We need to determine whether we should use nl2br or not during the process #633
		// By default BBcode content will not use nl2br
		$useBrTag = false;
		$decoda = KT::decoda($text);
		$decoda->initHook('CensorHook');
		$decoda->setEscaping(false);

		$decoda->setNl2br($useBrTag);

		$result = $decoda->parse();

		return $result;
	}

	public function encodePre($text)
	{
		$pattern = '/<pre.*?>(.*?)<\/pre>/s';
		preg_match_all($pattern , $text , $matches);

		if (isset($matches[0]) && is_array($matches[0])) {
			foreach ($matches[1] as $match) {
				$text = str_ireplace($match , base64_encode($match), $text);
			}
		}

		return $text;
	}

	public function decodePre($text)
	{
		$pattern = '/<pre.*?>(.*?)<\/pre>/s';
		preg_match_all($pattern , $text , $matches);

		if (isset($matches[0]) && is_array($matches[0])) {
			foreach ($matches[1] as $match) {
				$text = str_ireplace($match , base64_decode($match) , $text);
			}
		}

		return $text;
	}

	/**
	 * Replace the [quote] with Foundry's quote.item helper
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function replaceQuote($text)
	{
		$fd = KT::themes()->fd;
		$search = [
			'/\[quote]([^\[\/quote\]].*?)\[\/quote\]/ims',
			'/\[quote](.*?)\[\/quote\]/ims',
			'/\[quote name="(.*?)"\](.*?)\[\/quote\]/ims'
		];

		$quote = trim(preg_replace('/\s\s+/', '', $fd->html('quote.item', '\1')));
		$quoteWithAuthor = trim(preg_replace('/\s\s+/', '', $fd->html('quote.item', '\2', ['author' => '\1'])));

		$replace = [
			$quote,
			$quote,
			$quoteWithAuthor
		];

		$text = preg_replace($search, $replace, $text);

		// Parse again if there is still recursive quote tags
		if (preg_match('/\[quote](.*?)\[\/quote\]/ims', $text) || preg_match('/\[quote name="(.*?)"\](.*?)\[\/quote\]/ims', $text)) {
			$text = $this->replaceQuote($text);
		}

		return $text;
	}

	/**
	 * Replace the [giphy] with its embed
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function replaceGiphy($text)
	{
		$giphy = KT::giphy();

		if (!KT::giphy()->isEnabled()) {
			return $text;
		}

		preg_match_all('/\[giphy\](.*?)\[\/giphy\]/ims', $text, $matches);

		if (empty($matches) || !isset($matches[0]) || empty($matches[0])) {
			return $text;
		}

		$codes = $matches[0];
		$urls = $matches[1];
		$i = 0;

		foreach ($urls as $url) {
			if (!$giphy->isValidUrl($url)) {
				continue;
			}

			$item = $giphy->getItem($url);

			$text = FCJString::str_ireplace($codes[$i], $item, $text);
			$i++;
		}

		return $text;
	}

	/**
	 * Replace code blocks with prism.js compatible codes
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function replaceCodes($text, $replace = null)
	{
		// [code type=&quot*&quot]*[/code]
		$codesPattern = '/\[code( type=&quot;(.*?)&quot;)?\](.*?)\[\/code\]/ms';
		$text = preg_replace_callback($codesPattern, ['KomentoParser', 'processCodeBlocks'], $text);

		// Replace [code type="*"]*[/code]
		$codesPattern = '/\[code( type="(.*?)")?\](.*?)\[\/code\]/ms';
		$text = preg_replace_callback($codesPattern, ['KomentoParser', 'processCodeBlocks'], $text);

		return $text;
	}


	/**
	 * Replace [code] blocks with prism.js compatibility
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function processCodeBlocks($blocks)
	{
		$code = $blocks[3];

		// Remove break tags
		$code = str_ireplace("<br />", "", $code);
		$code = str_replace("[", "&#91;", $code);
		$code = str_replace("]", "&#93;", $code);

		// Determine the language type
		$language = isset($blocks[2]) && !empty($blocks[2]) ? $blocks[2] : 'markup';

		// Fix legacy code blocks
		if ($language == 'xml' || $language == 'html') {
			$language = 'markup';
		}

		// Because the text / contents are already escaped, we need to revert back to the original html codes only
		// for the codes.
		$code = html_entity_decode($code);

		// Fix html codes not displaying correctly
		$code = htmlspecialchars($code, ENT_NOQUOTES);

		return '<pre class="line-numbers"><code class="language-' . $language . '">'.$code.'</code></pre>';
	}
	
	public function removeBr($s)
	{
		// return str_replace("<br />", "", $s[0]);
		return str_replace("hello", "*****", $s[0]);
	}

	public function escape($s)
	{
		$code = $s[3];

		$code = str_replace("[", "&#91;", $code);
		$code = str_replace("]", "&#93;", $code);

		$brush  = isset($s[2]) && !empty($s[2]) ? $s[2] : 'xml';
		$code   = html_entity_decode($code);

		$code = FH::escape($code);

		if ($brush != '') {
			$result = '<pre><code class="language-' . htmlspecialchars($brush) . '">' . $code . '</code></pre>';

		} else {
			$result = '<pre><code>' . $code . '</code></pre>';
		}

		return $result;
	}

	public function replaceBBList($content)
	{
		// BBCode to find... e.g.
		// [list]
		// [*]hello world
		// [*]marihome
		// [/list]
		$bbcodeListItemsSearch = '#\[list.*?\](.*?)\[\/list\]#ims';

		// BBCode to find... e.g.
		// [*]hello world
		$bbcodeLISearch = [
			 '/\[\*\]\s?(.*?)\n/ims',
			 '/\[\*\]\s?(.*?)/ims'
		];

		// And replace them by...
		$bbcodeLIReplace = [
			 '<li>\1</li>',
			 '<li>\1</li>'
		];

		// And replace them by...
		$bbcodeLIReplaceString = [
			 '\1',
			 '\1'
		];

		// BBCode to find...
		$bbcodeListPattern = [
			 '/\[list\=(.*?)\]/ims',
			 '/\[list\]/ims',
			 '/\[\/list\]/ims'
		];

		// And replace them by...
		$bbcodeULReplaceString = ['\2', '\1'];

		preg_match_all($bbcodeListItemsSearch, $content, $matches);

		if (!$matches || !$matches[0]) {
			return $content;
		}

		$lists = [];

		$oldListIndicators = [
			'a' => 'lower-alpha',
			'A' => 'upper-alpha',
			'i' => 'lower-roman',
			'I' => 'upper-roman'
		];

		// Fix any unclosed list tags
		foreach ($matches[0] as &$contents) {
			$original = $contents;
			$listStylePattern = ['\[list\=(.*?)(\stype\=(.*?))?\]', '\[list\]'];
			$listStylePattern = implode('|', $listStylePattern);

			preg_match('/' . $listStylePattern . '/ims', $contents, $listStyleMatches);

			// The match of lists within this block should always be the first and last. Anything within the "list" should be considered as unclosed.
			$contents = preg_replace($bbcodeListPattern, '', $contents);

			// this match of list have to follow back what original list code pass in
			$contents = $listStyleMatches[0] . $contents . '[/list]';

			$hasStyleType = isset($listStyleMatches[3]);

			$item = new stdClass();
			$item->original = $original;
			$item->contents = $contents;
			$item->indicator = isset($listStyleMatches[1]) ? $listStyleMatches[1] : null;
			$item->style = $hasStyleType ? $listStyleMatches[3] : 'decimal';

			$isOldIndicator = $item->indicator && in_array($item->indicator, array_keys($oldListIndicators));

			// Backward compatibility
			if ($isOldIndicator) {
				$item->style = $oldListIndicators[$item->indicator];
			}

			$styleTypeRegex = $hasStyleType ? '\stype\=' . $item->style : '';

			// Regex to replace
			$item->regex = $item->indicator ? '/\[list\=' . $item->indicator . $styleTypeRegex . '\](.*?)\[\/list\]/ims' : '/\[list\](.*?)\[\/list\]/ims';

			// The html output
			$item->html = $item->indicator ? '<ol start=' . $item->indicator . ' style="list-style-type: ' . $item->style . '">\1</ol>' : '<ul>\1</ul>';

			$lists[] = $item;
		}

		foreach ($lists as $list) {
			// Check if this list contains any list items "[*]"
			$containsListItems = FCJString::strpos($list->contents, '[*]') !== false;

			if ($containsListItems) {
				$text = preg_replace($list->regex, $list->html, $list->contents);

				$text = preg_replace($bbcodeLISearch, $bbcodeLIReplace, $text);
			}

			if (!$containsListItems) {
				$text = preg_replace($bbcodeULSearch , $bbcodeULReplaceString, $list->contents);
			}

			// Update the content
			$content = str_replace($list->original, $text, $content);
		}

		return $content;
	}

	/**
	 * Replaces the bbcode url tag
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function replaceBBUrl($text)
	{
		$nofollow = $this->config->get('links_nofollow') ? ' rel="nofollow"' : '';

		preg_match_all('/\[url\=(.*?)\](.*?)\[\/url\]/ims', $text, $matches);

		if (!empty($matches)) {

			$sources = array_shift($matches);

			$urls = $matches[0];
			$txts = $matches[1];

			for ($i = 0; $i < count($sources); $i++) {
				$source = $sources[$i];

				if (!empty($source)) {
					$url = $urls[$i];
					
					// prevent user add javascript in the url element
					$segments = explode(' ', $url);
					$url = $segments[0];

					// Ensure that the url doesn't contain " or ' or &quot;
					$url = str_ireplace(['"', "'", '&quot;'], '', $url);

					$txt = $txts[$i];

					if (stripos($url, 'http://') !== 0 && stripos($url, 'https://') !== 0 && stripos($url, 'ftp://') !== 0) {
						$url = 'http://' . $url;
					}

					$replace = '<a target="_blank" href="' . $url . '"' . $nofollow . '>' . $txt . '</a>';

					$text = str_ireplace($source, $replace, $text);
				}
			}
		}

		return $text;
	}

	public function replaceURL($tmp, $text)
	{
		$pattern = '@(?i)\b((?:https?://|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))@';

		preg_match_all($pattern, $tmp, $matches);

		$targetBlank = ' target="_blank"';
		$noFollow = $this->config->get('links_nofollow') ? ' rel="nofollow"' : '';

		// Do not proceed if there are no links to process
		if (!isset($matches[0]) || !is_array($matches[0]) || empty($matches[0])) {
			return $text;
		}

		$tmplinks = $matches[0];

		$linksWithProtocols = [];
		$linksWithoutProtocols = [];

		// We need to separate the link with and without protocols to avoid conflict when there are similar url present in the content.
		if ($tmplinks) {
			foreach($tmplinks as $link) {
				if (stristr( $link , 'http://' ) === false && stristr( $link , 'https://' ) === false && stristr( $link , 'ftp://' ) === false ) {
					$linksWithoutProtocols[] = $link;
				} else if (stristr( $link , 'http://' ) !== false || stristr( $link , 'https://' ) !== false || stristr( $link , 'ftp://' ) === false ) {
					$linksWithProtocols[] = $link;
				}
			}
		}

		// the idea is the first convert the url to [EDWURLx] and [EDWOURLx] where x is the index. This is to prevent same url get overwritten with wrong value.
		$linkArrays = [];

		// global indexing.
		$idx = 1;

		// lets process the one with protocol
		if ($linksWithProtocols) {
			$linksWithProtocols = array_unique($linksWithProtocols);

			foreach($linksWithProtocols as $link) {

				$mypattern = '[EDWURL' . $idx . ']';

				$text = str_ireplace($link, $mypattern, $text);

				$obj = new stdClass();
				$obj->index = $idx;
				$obj->link = $link;
				$obj->newlink = $link;
				$obj->customcode = $mypattern;

				$linkArrays[] = $obj;

				$idx++;
			}
		}

		// Now we process the one without protocol
		if ($linksWithoutProtocols) {
			$linksWithoutProtocols = array_unique($linksWithoutProtocols);

			foreach($linksWithoutProtocols as $link) {
				$mypattern = '[EDWOURL' . $idx . ']';
				$text = str_ireplace($link, $mypattern, $text);

				$obj = new stdClass();
				$obj->index = $idx;
				$obj->link = $link;
				$obj->newlink = 'http://'. $link;
				$obj->customcode = $mypattern;

				$linkArrays[] = $obj;

				$idx++;
			}
		}

		// Let's replace back the link now with the proper format based on the index given.
		foreach ($linkArrays as $link) {
			$text = str_ireplace($link->customcode, $link->newlink, $text);

			$patternReplace = '@(?<![.*">])\b(?:(?:https?))[-A-Z0-9+&#/%=~_|$()?!:;,.]*[A-Z0-9+&#/%=~_|$]@i';

			// Use preg_replace to only replace if the URL doesn't has <a> tag
			$text = preg_replace($patternReplace, '<a href="\0" ' . $targetBlank . $noFollow . '>\0</a>', $text);
		}

		// Not really sure why this is needed as it will cause some of the content to not rendered correctly.
		// We will comment this out for now. References : #1878
		// $text = FCJString::str_ireplace('&quot;', '"', $text);

		return $text;
	}

	/**
	 * Parse spoiler block
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function replaceSpoiler($block)
	{
		// Clean block to remove line break.
		$text = preg_replace('/(\r\n|\n|\r)/ms', "", $block);

		$codesPattern = '/\[spoiler\](.*)\[\/spoiler\]/U';
		preg_match_all($codesPattern, $text, $matches);

		foreach ($matches[1] as $key => $match) {
			$search = $matches[0][$key];
			$replace = '<span class="kt-spoiler-block">' . $match . '</span>';

			if (preg_match('/\<div/i', $match)) {
				$replace = '<div class="kt-spoiler-block">' . $match . '</div>';
			}

			$text = str_replace($search, $replace, $text);
		}

		return $text;
	}
}
