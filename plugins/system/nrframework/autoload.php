<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

// Registers framework's namespace
JLoader::registerNamespace('NRFramework', __DIR__ . '/NRFramework/', false, false, 'psr4');

// Assignment related class aliases
JLoader::registerAlias('NRFrameworkFunctions',               '\\NRFramework\\Functions');
JLoader::registerAlias('NRAssignment',                       '\\NRFramework\\Conditions\Condition');
JLoader::registerAlias('nrFrameworkAssignmentsHelper',       '\\NRFramework\\Assignments');
JLoader::registerAlias('nrFrameworkAssignmentsAcyMailing',   '\\NRFramework\\Conditions\\AcyMailing');
JLoader::registerAlias('nrFrameworkAssignmentsAkeebaSubs',   '\\NRFramework\\Conditions\\AkeebaSubs');
JLoader::registerAlias('nrFrameworkAssignmentsContent',      '\\NRFramework\\Conditions\\Content');
JLoader::registerAlias('nrFrameworkAssignmentsConvertForms', '\\NRFramework\\Conditions\\ConvertForms');
JLoader::registerAlias('nrFrameworkAssignmentsDateTime',     '\\NRFramework\\Conditions\\DateTime');
JLoader::registerAlias('nrFrameworkAssignmentsDevices',      '\\NRFramework\\Conditions\\Devices');
JLoader::registerAlias('nrFrameworkAssignmentsGeoIP',        '\\NRFramework\\Conditions\\GeoIP');
JLoader::registerAlias('nrFrameworkAssignmentsLanguages',    '\\NRFramework\\Conditions\\Languages');
JLoader::registerAlias('nrFrameworkAssignmentsMenu',         '\\NRFramework\\Conditions\\Menu');
JLoader::registerAlias('nrFrameworkAssignmentsPHP',          '\\NRFramework\\Conditions\\PHP');
JLoader::registerAlias('nrFrameworkAssignmentsURLs',         '\\NRFramework\\Conditions\\URLs');
JLoader::registerAlias('nrFrameworkAssignmentsUsers',        '\\NRFramework\\Conditions\\Users');
JLoader::registerAlias('nrFrameworkAssignmentsOS',           '\\NRFramework\\Conditions\\OS');
JLoader::registerAlias('nrFrameworkAssignmentsBrowsers',     '\\NRFramework\\Conditions\\Browsers');
JLoader::registerAlias('NRCache', 							 '\\NRFramework\\Cache');
JLoader::registerAlias('NRHTML', 							 '\\NRFramework\\HTML');
JLoader::registerAlias('NRUpdateSites', 					 '\\NRFramework\\Updatesites');
JLoader::registerAlias('NRSmartTags', 					     '\\NRFramework\\SmartTags\\SmartTags');
JLoader::registerAlias('NRFramework\\SmartTags',			 '\\NRFramework\\SmartTags\\SmartTags');
JLoader::registerAlias('NREmail', 					         '\\NRFramework\\Email');
JLoader::registerAlias('NRVisitor', 					     '\\NRFramework\\VisitorToken');
JLoader::registerAlias('NRFonts', 					         '\\NRFramework\\Fonts');
JLoader::registerAlias('NR_activecampaign', 				 '\\NRFramework\\Integrations\\ActiveCampaign');
JLoader::registerAlias('NR_campaignmonitor', 				 '\\NRFramework\\Integrations\\CampaignMonitor');
JLoader::registerAlias('NR_convertkit', 				 	 '\\NRFramework\\Integrations\\ConvertKit');
JLoader::registerAlias('NR_drip', 				 			 '\\NRFramework\\Integrations\\Drip');
JLoader::registerAlias('NR_elasticemail', 					 '\\NRFramework\\Integrations\\ElasticEmail');
JLoader::registerAlias('NR_getresponse', 					 '\\NRFramework\\Integrations\\GetResponse');
JLoader::registerAlias('NR_hubspot', 						 '\\NRFramework\\Integrations\\HubSpot');
JLoader::registerAlias('NR_icontact', 						 '\\NRFramework\\Integrations\\IContact');
JLoader::registerAlias('NR_mailchimp', 						 '\\NRFramework\\Integrations\\MailChimp');
JLoader::registerAlias('NR_recaptcha', 						 '\\NRFramework\\Integrations\\ReCaptcha');
JLoader::registerAlias('NR_salesforce', 					 '\\NRFramework\\Integrations\\Salesforce');
JLoader::registerAlias('NR_sendinblue', 					 '\\NRFramework\\Integrations\\SendInBlue');
JLoader::registerAlias('NR_zoho', 							 '\\NRFramework\\Integrations\\Zoho');
JLoader::registerAlias('NR_zohocrm', 						 '\\NRFramework\\Integrations\\ZohoCRM');

// Define a helper constant to indicate whether we are on a Joomla 4 installation
if (version_compare(JVERSION, '4.0', 'ge') && !defined('nrJ4'))
{
	define('nrJ4', true);
}

// The Tassos.gr Site URL
if (!defined('TF_TEMPLATES_SITE_URL'))
{
	define('TF_TEMPLATES_SITE_URL', 'https://templates.tassos.gr/');
}

// Site Tower Endpoint
if (!defined('TF_TEMPLATES_TOWER_ENDPOINT'))
{
	define('TF_TEMPLATES_TOWER_ENDPOINT', TF_TEMPLATES_SITE_URL . '?option=com_ajax&format=raw&plugin=tower&task=api&endpoint=');
}

// URL to retrieve templates
if (!defined('TF_TEMPLATES_GET_URL'))
{
	define('TF_TEMPLATES_GET_URL', TF_TEMPLATES_TOWER_ENDPOINT . 'get_templates&project={{PROJECT}}&download_key={{DOWNLOAD_KEY}}');
}

// URL to retrieve a template
if (!defined('TF_TEMPLATE_GET_URL'))
{
	define('TF_TEMPLATE_GET_URL', TF_TEMPLATES_TOWER_ENDPOINT . 'get_template&project={{PROJECT}}&download_key={{DOWNLOAD_KEY}}&template={{TEMPLATE}}');
}

// URL to check the license
if (!defined('TF_CHECK_LICENSE'))
{
	define('TF_CHECK_LICENSE', 'https://www.tassos.gr/?option=com_ajax&format=raw&plugin=tower&task=api&endpoint=license_check&download_key={{DOWNLOAD_KEY}}');
}