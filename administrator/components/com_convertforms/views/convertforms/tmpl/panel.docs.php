<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

$docs = array(
	"Getting Started with Convert Forms" 	=> "getting-started-with-convert-forms",
	"How to use the Form Designer" 			=> "how-to-use-the-form-designer",
	"How to display a form on the frontend" => "how-to-display-a-form-on-the-frontend",
	"Sync Submissions with ActiveCampaign" 	=> "sync-leads-with-activecampaign",
	"Sync Submissions with GetResponse" 	=> "sync-leads-with-getresponse",
	"Sync Submissions with MailChimp" 		=> "sync-leads-with-mailchimp",
	"How to use Convert Forms as a popup"	=> "how-to-use-convert-forms-as-a-popup",
);

$docHome = "http://www.tassos.gr/joomla-extensions/convert-forms/docs/";

?>

<ul>
	<?php foreach ($docs as $title => $url) { ?>
		<li><a target="_blank" href="<?php echo $docHome; ?><?php echo $url?>"><?php echo $title ?></a></li>
	<?php } ?>
</ul>