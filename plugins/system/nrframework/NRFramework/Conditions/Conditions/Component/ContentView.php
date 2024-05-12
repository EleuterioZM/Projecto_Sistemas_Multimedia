<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Component;

defined('_JEXEC') or die;

class ContentView extends ContentBase
{
    /**
	 *  Pass check for Joomla! Articles
	 *
	 *  @return  bool
	 *  @return  bool
	 */
	public function pass()
	{
        // Make sure we are in the right context
        if (empty($this->selection) || !$this->passContext())
        {
            return false;
        }

        // In the Joomla Content component, the 'view' query parameter equals to 'category' in both Category List and Category Blog views.
        // In order to distinguish them we are using the 'layout' parameter as well.
        if ($this->request->view == 'category' && $this->request->layout)
        {
            $this->request->view .= '_' . $this->request->layout;
        }

        return $this->passByOperator($this->request->view, $this->selection, 'includes');
    }
}