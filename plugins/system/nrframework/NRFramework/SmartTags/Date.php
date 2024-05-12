<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\SmartTags;

defined('_JEXEC') or die('Restricted access');

class Date extends SmartTag
{
    /**
     * Constructor
     *
     * @param object    $factory    The framework factory object
     * @param array     $options    Assignment configuration options
     */
    public function __construct($factory = null, $options = null)
    {
        parent::__construct($factory, $options);

        $this->tz = new \DateTimeZone($this->factory->getApplication()->getCfg('offset', 'GMT'));
        $this->date = $this->factory->getDate()->setTimezone($this->tz);
    }

    /**
     * Returns the current date
     * 
     * @return  string
     */
    public function getDate()
    {
        $format = $this->parsedOptions->get('format', 'Y-m-d H:i:s');

        return $this->date->format($format, true);
    }
}