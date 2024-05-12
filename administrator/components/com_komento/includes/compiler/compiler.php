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

class KomentoCompiler
{
	static $instance = null;
	public $version;
	public $cli = false;

	// These script files should be rendered externally and not compiled together
	// Because they are either too large or only used in very minimal locations.
	public $excludeFiles = [];

	/**
	 * Allows caller to compile a script file on the site, given the section
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function compile($section = 'admin', $minify = true, $jquery = true)
	{
		// Get the file name that should be used after compiling the scripts
		$fileName = KT::scripts()->getFileName($section, $jquery);
		
		$files = $this->getFiles($section, $jquery);

		$contents = '';

		$contents .= $this->compileBootloader();

		// 1. Core file contents needs to be placed at the top
		$contents .= $this->compileCoreFiles($files->core);
		
		// 2. Libraries should be appended next
		$contents .= $this->compileLibraries($files->libraries);

		// 3. Compile the normal scripts
		$contents .= $this->compileScripts($files->scripts);

		$result = new stdClass();
		$result->section = $section;
		$result->minify = $minify;

		// Store the uncompressed version
		$standardPath = KOMENTO_SCRIPTS . '/' . $fileName . '.js';
		$this->write($standardPath, $contents);

		$result->standard = $standardPath;
		$result->minified = false;

		// Compress the script and minify it
		if ($minify) {
			// 1. Minify the main library
			$contents = FH::minifyScript($contents);
			
			// Store the minified version
			$minifiedPath = KOMENTO_SCRIPTS . '/' . $fileName . '.min.js';
			$this->write($minifiedPath, $contents);

			// 2. Since excluded files are running on their own, we would need to minify them so that it
			// runs on the compressed version rather than the uncompressed version
			$excludedFiles = $this->getExcludedFiles();
			
			foreach ($excludedFiles as $excludedFile) {
				$targetPath = str_ireplace('.js', '.min.js', $excludedFile);
				$excludedContents = file_get_contents($excludedFile);

				$excludedContents = FH::minifyScript($excludedContents);

				$this->write($targetPath, $excludedContents);
			}

			$result->minified = $minifiedPath;
		}

		if (defined('KOMENTO_CLI')) {
			return $result;
		}

		return $result;
	}

	/**
	 * Retrieves a list of excluded script files from the compiler
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getExcludedFiles()
	{
		$path = KOMENTO_SCRIPTS . '/vendors';

		if (!$this->excludeFiles) {
			return array();
		}
		
		$pattern = implode('|^', $this->excludeFiles);

		$files = JFolder::files($path, $pattern, true, true);

		return $files;
	}

	/**
	 * Compiles core files
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function compileCoreFiles($files)
	{
		$contents = '';

		foreach ($files as $file) {
			$contents .= file_get_contents($file);
		}

		return $contents;
	}

	/**
	 * Retrieves contents from the bootloader file
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function compileBootloader()
	{
		$file = JPATH_ROOT . '/media/com_komento/scripts/bootloader.js';

		$contents = file_get_contents($file);

		return $contents;
	}

	/**
	 * Compiles all libraries
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function compileLibraries($files)
	{
		$modules = array();

		// Get the prefix so that we can get the proper namespace
		$prefix = KOMENTO_SCRIPTS . '/vendors';

		foreach ($files as $file) {
			$fileName = ltrim(str_ireplace($prefix, '', $file), '/');
			$modules[] = str_ireplace('.js', '', $fileName);
		}		

		$modules = json_encode($modules);

ob_start();
?>
KTVendors.plugin("static", function($) {
	$.module(<?php echo $modules;?>);

	// Now we need to retrieve the contents of each files
	<?php foreach ($files as $file) { ?>
		<?php echo $this->getContents($file); ?>
	<?php } ?>
});
<?php
$contents = ob_get_contents();
ob_end_clean();

		return $contents;
	}

	/**
	 * Compiles script files
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function compileScripts($files)
	{
		$modules = array();

		foreach ($files as $file) {
			$namespace = str_ireplace(KOMENTO_SCRIPTS, 'komento', $file);

			$modules[] = str_ireplace('.js', '', $namespace);
		}

		$modules = json_encode($modules);
ob_start();
?>
// Prepare the script definitions
KTVendors.installer('Komento', 'definitions', function($) {
	$.module(<?php echo $modules;?>);
});

// Prepare the contents of all the scripts
KTVendors.installer('Komento', 'scripts', function($) {
	<?php foreach ($files as $file) { ?>
		<?php echo $this->getContents($file); ?>
	<?php } ?>
});
<?php
$contents = ob_get_contents();
ob_end_clean();
	
		return $contents;
	}


	/**
	 * Only creates this instance once
	 *
	 * @since	2.0
	 * @access	public
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Retrieves the contents of a particular file
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getContents($file)
	{
		$contents = file_get_contents($file);

		return $contents;
	}

	/**
	 * Retrieves a list of files for specific sections
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getFiles($section, $jquery = true)
	{
		$files = new stdClass();

		// Get a list of core files
		$coreFiles = KT::scripts()->getDependencies(true, $jquery);
		$files->core = $coreFiles;

		// Get a list of libraries
		$files->libraries = $this->getLibraryFiles();

		// Get a list of shared scripts that is used across sections
		$scriptFiles = array();
		$scriptFiles = array_merge($scriptFiles, $this->getSharedFiles());

		// Get script files from the particular section
		$scriptFiles = array_merge($scriptFiles, $this->getScriptFiles($section));

		$files->scripts = $scriptFiles;

		return $files;
	}

	/**
	 * Retrieves a list of library files used on the site
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getLibraryFiles()
	{
		// Retrieve core dependencies
		$excludes = array('moment', 'jquery.js');

		// Add exclusion files
		foreach ($this->excludeFiles as $exclusion) {
			$excludes[] = $exclusion;

			// Excluded files may also contain a .min.js
			$excludes[] = str_ireplace('.js', '.min.js', $exclusion);
		}

		// Exclude dependencies
		$dependencies = KT::scripts()->getDependencies();
		$excludes = array_merge($excludes, $dependencies);

		$path = KOMENTO_SCRIPTS . '/vendors';
		$files = JFolder::files($path, '.js$', true, true, $excludes);

		// $this->debug($files);

		return $files;
	}

	/**
	 * Retrieves list of shared files that is used across all sections
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getSharedFiles()
	{
		$files = array();

		// Retrieve core dependencies
		$dependencies = KT::scripts()->getDependencies();

		$folderExclusion = array('.git', '.svn', 'CVS', '.DS_Store', '__MACOSX', 'admin', 'site', 'unused', 'vendors');
		$folders = JFolder::folders(KOMENTO_SCRIPTS, '.', false, true, $folderExclusion);

		foreach ($folders as $folder) {
			$files = array_merge($files, JFolder::files($folder, '.js$', true, true, $this->excludeFiles));
		}

		// $this->debug($files);

		return $files;
	}

	/**
	 * Retrieves list of scripts that is only used in the particular section
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getScriptFiles($section)
	{
		$path = KOMENTO_SCRIPTS . '/' . $section;
		$files = JFolder::files($path, '.js$', true, true, $this->excludeFiles);

		return $files;
	}

	/**
	 * Saves the contents into a file
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function write($path, $contents)
	{
		if (JFile::exists($path)) {
			JFile::delete($path);
		}

		return JFile::write($path, $contents);
	}

	/**
	 * For debugging purposes only. @dump does not display everything
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function debug($items)
	{
		echo '<pre>';
		print_r($items);
		echo '</pre>';
		exit;
	}
}
