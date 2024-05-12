<?php
/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

JHtml::script('com_convertforms/editorbutton.js', ['relative' => true, 'version' => 'auto']);

JFactory::getDocument()->addStyleDeclaration( '
	.cfEditorButton form, .eboxEditorButton .controls > * {
		margin:0;
	}
	.cfHeader {
	    border-bottom: 1px dotted #ccc;
	    margin-bottom: 15px;
	    padding-bottom: 5px;
	}
	.cfHeader p {
	    color:#666;
	    font-size: 11px;
	}
	.cfHeader h3 {
	    font-size: 16px;
	    margin-bottom: 5px;
	    margin-top: 0;
	}
	.cfEditorButton .control-group {
	    margin-bottom: 15px;
	}
	.cfEditorButton {
	    padding: 5px;
	}
');

?>
<div class="cfEditorButton">
	<form>
		<?php echo $this->form->renderFieldset("main") ?>
		<button onclick="insertConvertFormShortcode('<?php echo $this->eName; ?>', <?php echo defined('nrJ4') ? 'true' : 'false' ?>);" class="btn btn-success span12">
			<?php echo JText::_('PLG_EDITORS-XTD_CONVERTFORMS_INSERTBUTTON'); ?>
		</button>
	</form>
</div>
