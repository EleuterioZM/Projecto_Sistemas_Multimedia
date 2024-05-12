<?php

/**
 *  @author          Tassos Marinos <info@tassos.gr>
 *  @link            http://www.tassos.gr
 *  @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 *  @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Geo;

defined('_JEXEC') or die;

class Region extends GeoBase
{
    /**
     *  Returns the assignment's value
     * 
     *  @return string Region codes
     */
	public function value()
	{
		return $this->getRegions();
    }
    
    /**
     *  Get list of all ISO 3611 Country Region Codes
     *
     *  @return array
     */
    private function getRegions()
    {
        $regionCodes = [];
		$record = $this->geo->getRecord();

		if ($record === false || is_null($record))
		{
			return $regionCodes;
		}

        // Skip if no regions found
        if (!$regions = $record->subdivisions)
        {
            return $regionCodes;
        }
        
        foreach ($regions as $key => $region)
        {
            // Get the Region's full name
            $regionCodes[] = $region->names['en'];

            // Get the Region's code by preppending the country isocode to the region code
            $regionCodes[] = $record->country->isoCode . '-' . $region->isoCode;
        }

        return $regionCodes;
    }
}