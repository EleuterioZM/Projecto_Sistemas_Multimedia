<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($scriptTag && $script) { ?>
<script type="text/javascript">
<?php } ?>

<?php if ($cdata) { ?>
//<![CDATA[
<?php } ?>

<?php echo $script; ?>

<?php if ($cdata) { ?>
//]]>
<?php } ?>

<?php if ($scriptTag && $script) { ?>
</script>
<?php } ?>
