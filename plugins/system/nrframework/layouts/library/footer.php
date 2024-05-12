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

defined('_JEXEC') or die('Restricted access');

extract($displayData);
?>
<div class="tf-library-footer">
	<a href="<?php echo $create_new_template_link; ?>" target="_blank">
		<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
			<circle cx="8" cy="8" r="6" stroke="currentColor" />
			<line x1="8" y1="5" x2="8" y2="11" stroke="currentColor" stroke-linecap="round" />
			<line x1="11" y1="8" x2="5" y2="8" stroke="currentColor" stroke-linecap="round" />
		</svg>
        <?php echo \JText::_('NR_START_FROM_SCRATCH'); ?>
	</a>
    <a href="https://www.tassos.gr/contact?topic=Custom Development&plugin=<?php $project_name; ?>" target="_blank">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M5.6 11H6.1C6.1 10.7239 5.87614 10.5 5.6 10.5V11ZM5.6 14H5.1C5.1 14.1905 5.20823 14.3644 5.37912 14.4486C5.55002 14.5327 5.75387 14.5125 5.90486 14.3963L5.6 14ZM3 2.5H13V1.5H3V2.5ZM13.5 3V10H14.5V3H13.5ZM2.5 10V3H1.5V10H2.5ZM5.6 10.5H3V11.5H5.6V10.5ZM6.1 14V11H5.1V14H6.1ZM13 10.5H9.84012V11.5H13V10.5ZM8.92556 10.8111L5.29514 13.6037L5.90486 14.3963L9.53527 11.6037L8.92556 10.8111ZM1.5 10C1.5 10.8284 2.17157 11.5 3 11.5V10.5C2.72386 10.5 2.5 10.2761 2.5 10H1.5ZM13.5 10C13.5 10.2761 13.2761 10.5 13 10.5V11.5C13.8284 11.5 14.5 10.8284 14.5 10H13.5ZM9.84012 10.5C9.50931 10.5 9.18777 10.6094 8.92556 10.8111L9.53527 11.6037C9.62267 11.5365 9.72985 11.5 9.84012 11.5V10.5ZM13 2.5C13.2761 2.5 13.5 2.72386 13.5 3H14.5C14.5 2.17157 13.8284 1.5 13 1.5V2.5ZM3 1.5C2.17157 1.5 1.5 2.17157 1.5 3H2.5C2.5 2.72386 2.72386 2.5 3 2.5V1.5Z" fill="currentColor"/>
        </svg>
        <?php echo \JText::_('NR_REQUEST_TEMPLATE'); ?>
    </a>
</div>