<?php

/**
 *  @author          Tassos Marinos <info@tassos.gr>
 *  @link            http://www.tassos.gr
 *  @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 *  @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Geo;

defined('_JEXEC') or die;

use NRFramework\Functions;

class Continent extends GeoBase
{
    /**
     *  Continent check
     * 
     *  @return bool
     */
    public function prepareSelection()
    {
        $selection = Functions::makeArray($this->getSelection());

        // Try to convert continent names to codes
        return array_map(function($c) {
            if (strlen($c) > 2)
            {
                $c = \NRFramework\Continents::getCode($c);
            }
            return $c;
        }, $selection);
    }

    /**
     *  Return the Continent's code and full name
     * 
     *  @return string Country code
     */
	public function value()
	{
        return [
            $this->geo->getContinentName('en'),
            $this->geo->getContinentCode()
        ];
	}
}