<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 * @credits			https://github.com/codeigniter4/CodeIgniter4/blob/develop/app/Config/Mimes.php
*/

namespace NRFramework;

// No direct access
defined('_JEXEC') or die;

class Mimes
{
	/**
	 * Map of extensions to mime types.
	 *
	 * @var array
	 */
	public static $mimes = [
		'hqx'   => [
			'application/mac-binhex40',
			'application/mac-binhex',
			'application/x-binhex40',
			'application/x-mac-binhex40',
		],
		'cpt'   => 'application/mac-compactpro',
		'csv'   => [
			'text/csv',
			'text/x-comma-separated-values',
			'text/comma-separated-values',
			'application/vnd.ms-excel',
			'application/x-csv',
			'text/x-csv',
			'application/csv',
			'application/excel',
			'application/vnd.msexcel',
			'text/plain',
		],
		'bin'   => [
			'application/macbinary',
			'application/mac-binary',
			'application/octet-stream',
			'application/x-binary',
			'application/x-macbinary',
		],
		'dms'   => 'application/octet-stream',
		'lha'   => 'application/octet-stream',
		'lzh'   => 'application/octet-stream',
		'exe'   => [
			'application/octet-stream',
			'application/x-msdownload',
		],
		'class' => 'application/octet-stream',
		'psd'   => [
			'application/x-photoshop',
			'image/vnd.adobe.photoshop',
		],
		'so'    => 'application/octet-stream',
		'sea'   => 'application/octet-stream',
		'dll'   => 'application/octet-stream',
		'oda'   => 'application/oda',
		'pdf'   => [
			'application/pdf',
			'application/force-download',
			'application/x-download',
		],
		'ai'    => [
			'application/pdf',
			'application/postscript',
		],
		'eps'   => 'application/postscript',
		'ps'    => 'application/postscript',
		'smi'   => 'application/smil',
		'smil'  => 'application/smil',
		'mif'   => 'application/vnd.mif',
		'xls'   => [
			'application/vnd.ms-excel',
			'application/msexcel',
			'application/x-msexcel',
			'application/x-ms-excel',
			'application/x-excel',
			'application/x-dos_ms_excel',
			'application/xls',
			'application/x-xls',
			'application/excel',
			'application/download',
			'application/vnd.ms-office',
			'application/msword',
		],
		'ppt'   => [
			'application/vnd.ms-powerpoint',
			'application/powerpoint',
			'application/vnd.ms-office',
			'application/msword',
		],
		'pptx'  => [
			'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'application/x-zip',
			'application/zip',
		],
		'wbxml' => 'application/wbxml',
		'wmlc'  => 'application/wmlc',
		'dcr'   => 'application/x-director',
		'dir'   => 'application/x-director',
		'dxr'   => 'application/x-director',
		'dvi'   => 'application/x-dvi',
		'gtar'  => 'application/x-gtar',
		'gz'    => 'application/x-gzip',
		'gzip'  => 'application/x-gzip',
		'php'   => [
			'application/x-php',
			'application/x-httpd-php',
			'application/php',
			'text/php',
			'text/x-php',
			'application/x-httpd-php-source',
		],
		'php4'  => 'application/x-httpd-php',
		'php3'  => 'application/x-httpd-php',
		'phtml' => 'application/x-httpd-php',
		'phps'  => 'application/x-httpd-php-source',
		'js'    => [
			'application/x-javascript',
			'text/plain',
		],
		'swf'   => 'application/x-shockwave-flash',
		'sit'   => 'application/x-stuffit',
		'tar'   => 'application/x-tar',
		'tgz'   => [
			'application/x-tar',
			'application/x-gzip-compressed',
		],
		'z'     => 'application/x-compress',
		'xhtml' => 'application/xhtml+xml',
		'xht'   => 'application/xhtml+xml',
		'zip'   => [
			'application/x-zip',
			'application/zip',
			'application/x-zip-compressed',
			'application/s-compressed',
			'multipart/x-zip',
		],
		'rar'   => [
			'application/vnd.rar',
			'application/x-rar',
			'application/rar',
			'application/x-rar-compressed',
		],
		'mid'   => 'audio/midi',
		'midi'  => 'audio/midi',
		'mpga'  => 'audio/mpeg',
		'mp2'   => 'audio/mpeg',
		'mp3'   => [
			'audio/mpeg',
			'audio/mpg',
			'audio/mpeg3',
			'audio/mp3',
		],
		'aif'   => [
			'audio/x-aiff',
			'audio/aiff',
		],
		'aiff'  => [
			'audio/x-aiff',
			'audio/aiff',
		],
		'aifc'  => 'audio/x-aiff',
		'ram'   => 'audio/x-pn-realaudio',
		'rm'    => 'audio/x-pn-realaudio',
		'rpm'   => 'audio/x-pn-realaudio-plugin',
		'ra'    => 'audio/x-realaudio',
		'rv'    => 'video/vnd.rn-realvideo',
		'wav'   => [
			'audio/x-wav',
			'audio/wave',
			'audio/wav',
		],
		'bmp'   => [
			'image/bmp',
			'image/x-bmp',
			'image/x-bitmap',
			'image/x-xbitmap',
			'image/x-win-bitmap',
			'image/x-windows-bmp',
			'image/ms-bmp',
			'image/x-ms-bmp',
			'application/bmp',
			'application/x-bmp',
			'application/x-win-bitmap',
		],
		'gif'   => 'image/gif',
		'jpg'   => [
			'image/jpeg',
			'image/pjpeg',
		],
		'jpeg'  => [
			'image/jpeg',
			'image/pjpeg',
		],
		'jpe'   => [
			'image/jpeg',
			'image/pjpeg',
		],
		'jp2'   => [
			'image/jp2',
			'video/mj2',
			'image/jpx',
			'image/jpm',
		],
		'j2k'   => [
			'image/jp2',
			'video/mj2',
			'image/jpx',
			'image/jpm',
		],
		'jpf'   => [
			'image/jp2',
			'video/mj2',
			'image/jpx',
			'image/jpm',
		],
		'jpg2'  => [
			'image/jp2',
			'video/mj2',
			'image/jpx',
			'image/jpm',
		],
		'jpx'   => [
			'image/jp2',
			'video/mj2',
			'image/jpx',
			'image/jpm',
		],
		'jpm'   => [
			'image/jp2',
			'video/mj2',
			'image/jpx',
			'image/jpm',
		],
		'mj2'   => [
			'image/jp2',
			'video/mj2',
			'image/jpx',
			'image/jpm',
		],
		'mjp2'  => [
			'image/jp2',
			'video/mj2',
			'image/jpx',
			'image/jpm',
		],
		'png'   => [
			'image/png',
			'image/x-png',
		],
		'tif'   => 'image/tiff',
		'tiff'  => 'image/tiff',
		'css'   => [
			'text/css',
			'text/plain',
		],
		'html'  => [
			'text/html',
			'text/plain',
		],
		'htm'   => [
			'text/html',
			'text/plain',
		],
		'shtml' => [
			'text/html',
			'text/plain',
		],
		'txt'   => 'text/plain',
		'text'  => 'text/plain',
		'log'   => [
			'text/plain',
			'text/x-log',
		],
		'rtx'   => 'text/richtext',
		'rtf'   => 'text/rtf',
		'xml'   => [
			'application/xml',
			'text/xml',
			'text/plain',
		],
		'xsl'   => [
			'application/xml',
			'text/xsl',
			'text/xml',
		],
		'mpeg'  => 'video/mpeg',
		'mpg'   => 'video/mpeg',
		'mpe'   => 'video/mpeg',
		'qt'    => 'video/quicktime',
		'mov'   => 'video/quicktime',
		'avi'   => [
			'video/x-msvideo',
			'video/msvideo',
			'video/avi',
			'application/x-troff-msvideo',
		],
		'movie' => 'video/x-sgi-movie',
		'doc'   => [
			'application/msword',
			'application/vnd.ms-office',
		],
		'docx'  => [
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'application/zip',
			'application/msword',
			'application/x-zip',
		],
		'dot'   => [
			'application/msword',
			'application/vnd.ms-office',
		],
		'dotx'  => [
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'application/zip',
			'application/msword',
		],
		'xlsx'  => [
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'application/zip',
			'application/vnd.ms-excel',
			'application/msword',
			'application/x-zip',
		],
		'xlsb'  => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
		'xlsm'  => 'application/vnd.ms-excel.sheet.macroEnabled.12',
		'word'  => [
			'application/msword',
			'application/octet-stream',
		],
		'xl'    => 'application/excel',
		'eml'   => 'message/rfc822',
		'json'  => [
			'application/json',
			'text/json',
		],
		'pem'   => [
			'application/x-x509-user-cert',
			'application/x-pem-file',
			'application/octet-stream',
		],
		'p10'   => [
			'application/x-pkcs10',
			'application/pkcs10',
		],
		'p12'   => 'application/x-pkcs12',
		'p7a'   => 'application/x-pkcs7-signature',
		'p7c'   => [
			'application/pkcs7-mime',
			'application/x-pkcs7-mime',
		],
		'p7m'   => [
			'application/pkcs7-mime',
			'application/x-pkcs7-mime',
		],
		'p7r'   => 'application/x-pkcs7-certreqresp',
		'p7s'   => 'application/pkcs7-signature',
		'crt'   => [
			'application/x-x509-ca-cert',
			'application/x-x509-user-cert',
			'application/pkix-cert',
		],
		'crl'   => [
			'application/pkix-crl',
			'application/pkcs-crl',
		],
		'der'   => 'application/x-x509-ca-cert',
		'kdb'   => 'application/octet-stream',
		'pgp'   => 'application/pgp',
		'gpg'   => 'application/gpg-keys',
		'sst'   => 'application/octet-stream',
		'csr'   => 'application/octet-stream',
		'rsa'   => 'application/x-pkcs7',
		'cer'   => [
			'application/pkix-cert',
			'application/x-x509-ca-cert',
		],
		'3g2'   => 'video/3gpp2',
		'3gp'   => [
			'video/3gp',
			'video/3gpp',
		],
		'mp4'   => 'video/mp4',
		'm4a'   => 'audio/x-m4a',
		'f4v'   => [
			'video/mp4',
			'video/x-f4v',
		],
		'flv'   => 'video/x-flv',
		'webm'  => 'video/webm',
		'aac'   => 'audio/x-acc',
		'm4u'   => 'application/vnd.mpegurl',
		'm3u'   => 'text/plain',
		'xspf'  => 'application/xspf+xml',
		'vlc'   => 'application/videolan',
		'wmv'   => [
			'video/x-ms-wmv',
			'video/x-ms-asf',
		],
		'au'    => 'audio/x-au',
		'ac3'   => 'audio/ac3',
		'flac'  => 'audio/x-flac',
		'ogg'   => [
			'audio/ogg',
			'video/ogg',
			'application/ogg',
		],
		'kmz'   => [
			'application/vnd.google-earth.kmz',
			'application/zip',
			'application/x-zip',
		],
		'kml'   => [
			'application/vnd.google-earth.kml+xml',
			'application/xml',
			'text/xml',
		],
		'ics'   => 'text/calendar',
		'ical'  => 'text/calendar',
		'zsh'   => 'text/x-scriptzsh',
		'7zip'  => [
			'application/x-compressed',
			'application/x-zip-compressed',
			'application/zip',
			'multipart/x-zip',
		],
		'cdr'   => [
			'application/cdr',
			'application/coreldraw',
			'application/x-cdr',
			'application/x-coreldraw',
			'image/cdr',
			'image/x-cdr',
			'zz-application/zz-winassoc-cdr',
		],
		'wma'   => [
			'audio/x-ms-wma',
			'video/x-ms-asf',
		],
		'jar'   => [
			'application/java-archive',
			'application/x-java-application',
			'application/x-jar',
			'application/x-compressed',
		],
		'svg'   => [
			'image/svg+xml',
			'image/svg',
			'application/xml',
			'text/xml',
		],
		'vcf'   => 'text/x-vcard',
		'srt'   => [
			'text/srt',
			'text/plain',
		],
		'vtt'   => [
			'text/vtt',
			'text/plain',
		],
		'ico'   => [
			'image/x-icon',
			'image/x-ico',
			'image/vnd.microsoft.icon',
		],
		'stl'   => [
			'application/sla',
			'application/vnd.ms-pki.stl',
			'application/x-navistyle',
		],
	];

	/**
	 * Attempts to determine the best mime type for the given file extension.
	 *
	 * @param string $extension
	 *
	 * @return string|null The mime type found, or none if unable to determine.
	 */
	public static function getTypesFromExtension($extension)
	{
		$extension = trim(strtolower($extension), '. ');

		if (!array_key_exists($extension, static::$mimes))
		{
			return null;
		}

		return (array) static::$mimes[$extension];
	}

	/**
	 * Attempts to determine the best file extension for a given mime type.
	 *
	 * @param string      $type
	 * @param string|null $proposedExtension - default extension (in case there is more than one with the same mime type)
	 *
	 * @return string|null The extension determined, or null if unable to match.
	 */
	public static function guessExtensionFromType($type, $proposedExtension = null)
	{
		$type = trim(strtolower($type), '. ');

		$proposedExtension = trim(strtolower($proposedExtension));

		if ($proposedExtension !== '')
		{
			if (array_key_exists($proposedExtension, static::$mimes) && in_array($type, is_string(static::$mimes[$proposedExtension]) ? [static::$mimes[$proposedExtension]] : static::$mimes[$proposedExtension], true))
			{
				// The detected mime type matches with the proposed extension.
				return $proposedExtension;
			}

			// An extension was proposed, but the media type does not match the mime type list.
			return null;
		}

		// Reverse check the mime type list if no extension was proposed.
		// This search is order sensitive!
		foreach (static::$mimes as $ext => $types)
		{
			if ((is_string($types) && $types === $type) || (is_array($types) && in_array($type, $types, true)))
			{
				return $ext;
			}
		}

		return null;
	}

	/**
	 * Test whether the given mime type is in the allowed file types.
	 *
	 * @param	mixed	$allowed_types	Can be a list of comma separated types or an array of types. Types can be either an extension (.jpg) or a mime type (application/zip)
	 * @param	string	$mime			The mime type to check
	 *
	 * @return	mixed	Null on failure, true on success
	 */
	public static function check($allowed_types, $detected_mime)
	{
		if (!$allowed_types || !$detected_mime)
		{
			return false;
		}

		$allowed_types = self::toSafeArray($allowed_types);

		foreach ($allowed_types as $allowed_type)
		{
			// Check whether we have a mime type or a file extension. A Mime type is supposed to have a forward slash character.
			// If we have a file extension (.jpg, .zip), convert it to a Mime type.
			$allowed_mime_types = strpos($allowed_type, '/') === false ? self::getTypesFromExtension($allowed_type) : $allowed_type;

			if (self::typeIsInTypes($detected_mime, $allowed_mime_types))
			{
				return true;
			}
		}
	}

	/**
	 * Test whether the given detected mime type is in allowed mime types
	 *
	 * @param	string	$detected_type		The mime type to check Eg: application/zip
	 * @param	array	$allowed_types		A list of allowed mime types Eg: ['application/zip', 'images/jpg']
	 * 
	 * @return	bool	True on success
	 */
	public static function typeIsInTypes($detected_type, $allowed_types)
	{
		if (!$detected_type || !$allowed_types)
		{
			return;
		}

		$allowed_types = self::toSafeArray($allowed_types);
		$detected_type = strtolower($detected_type);

		foreach ($allowed_types as $allowed_type)
		{		
			// Special case: Allow to use wildcard in mime types like: image/* - This requires to convert the asterisk character to regex pattern.
			$allowed_type = str_replace('*', '.*', $allowed_type);

			if (preg_match('#' . $allowed_type . '#', $detected_type))
			{
				return true;
			}
		}
	}

	/**
	 * Detect the filename's Mime type 
	 *
	 * @param   string   $file     The path to the file to be checked
	 *
	 * @return  mixed    the mime type detected false on error
	 */
	public static function detectFileType($file)
	{
		// If we can't detect anything mime is false
		$mime = false;

		try
		{
			if (function_exists('mime_content_type'))
			{
				$mime = mime_content_type($file);
			}
			elseif (function_exists('finfo_open'))
			{
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$mime  = finfo_file($finfo, $file);
				finfo_close($finfo);
			}
		}
		catch (\Exception $e)
		{
		}

		return $mime;
	}

	private static function toSafeArray($subject)
	{
		if (!is_array($subject))
		{
			$subject = explode(',', $subject);
		}

		$subject = array_map('trim', $subject);
		$subject = array_map('strtolower', $subject);
        $subject = array_unique($subject);
        $subject = array_filter($subject);

		return $subject;
	}
}