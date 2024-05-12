<?php
/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterBase;
class ConvertFormsRouter extends RouterBase
{
	/**
	 * Build the route for the com_convertforms component
	 *
	 * @param   array &$query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   2.8.1
	 */
	public function build(&$query)
	{
		$segments = [];

		if (!isset($query['view']))
		{
			return $segments;
		}

		$view = $query['view'];
		unset($query['view']);

		if ($view == 'submission')
		{
			$segments[] = $view;

			if (isset($query['id']))
			{
				$segments[] = $query['id'];
				unset($query['id']);
			}

			if (isset($query['print']))
			{
				$segments[] = 'print';
				unset($query['print']);
			}

			if (isset($query['tmpl']))
			{
				unset($query['tmpl']);
			}
		}

		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array &$segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   2.5.1
	 */
	public function parse(&$segments)
	{
		$vars = [];

		// View is always the first element of the array
		$view = $segments[0];

		if ($view == 'submission')
		{
			$vars['view'] = $view;
			$vars['id'] = $segments[1];

			if (isset($segments[2]) && $segments[2] == 'print')
			{
				$vars['tmpl'] = 'component';
				$vars['print'] = 1;
			}

			// Reset segments to make J4 Router happy.
			$segments = [];
		}

		return $vars;
	}
}

/**
 * Convert Forms router functions
 *
 * These functions are proxies for the new router interface
 * for old SEF extensions.
 *
 * @param   array &$query  An array of URL arguments
 *
 * @return  array  The URL arguments to use to assemble the subsequent URL.
 */

function ConvertFormsBuildRoute(&$query)
{
	$router = new ConvertFormsRouter();

	return $router->build($query);
}

function ConvertFormsParseRoute($segments)
{
	$router = new ConvertFormsRouter();

	return $router->parse($segments);
}