<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$app = Factory::getApplication();
$wa  = $this->getWebAssetManager();
$document = Factory::getDocument();
$this->language = $document->language;
$this->direction = $document->direction;

$document->addStyleSheet('templates/' . $this->template . '/css/base/template.css');
$document->addStyleSheet('templates/' . $this->template . '/css/nexus.min.css');
$document->addStyleSheet('templates/' . $this->template . '/css/responsive.min.css');

// Add JavaScript Frameworks
\JHtml::_('behavior.core');
\JHtml::_('bootstrap.framework');

$wa->useStyle('fontawesome');
$wa->addInlineStyle(":root {
	--hue: 214;
	--template-bg-light: #f0f4fb;
	--template-text-dark: #495057;
	--template-text-light: #ffffff;
	--template-link-color: #2a69b8;
	--template-special-color: #001B4C;
}
body {
	font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,'Noto Sans',sans-serif;
}
");
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="metas" />
  <jdoc:include type="styles" />
  <jdoc:include type="scripts" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/print.css" />
</head>
<body>
	<jdoc:include type="message" />
	<jdoc:include type="component" />
</body>
</html>
