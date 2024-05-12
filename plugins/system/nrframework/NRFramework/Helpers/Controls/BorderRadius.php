<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Helpers\Controls;

defined('_JEXEC') or die;

class BorderRadius extends Spacing
{
    /**
     * Border Radius Spacing Control Positions.
     * 
     * @var  array
     */
    protected static $spacing_positions = ['top_left', 'top_right', 'bottom_right', 'bottom_left'];
}