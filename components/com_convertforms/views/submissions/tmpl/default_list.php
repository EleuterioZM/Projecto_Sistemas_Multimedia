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

defined('_JEXEC') or die;
?>

<table>
    <thead>
        <tr>
            <th><?php echo JText::_('COM_CONVERTFORMS_ID') ?></th>
            <th><?php echo JText::_('COM_CONVERTFORMS_CREATED') ?></th>
            <th><?php echo JText::_('JSTATUS') ?></th>
            <th width="70px"></th>
        </tr>
    </thead>
    <?php foreach ($this->submissions as $submission) { ?>
        <tr>
            <td><a href="<?php echo $submission->link ?>"><?php echo $submission->id ?></a></td>
            <td><?php echo $submission->created ?></td>
            <td>
                <?php 
                    $badge = 'bg-' . ($submission->state == '1' ? 'success' : 'danger');

                    if (!defined('nrJ4'))
                    {
                        $badge = 'badge-' . ($submission->state == '1' ? 'success' : 'important');
                    }
                ?>

                <span class="badge <?php echo $badge ?>">
					<?php echo JText::_(($submission->state == '1' ? 'COM_CONVERTFORMS_SUBMISSION_CONFIRMED' : 'COM_CONVERTFORMS_SUBMISSION_UNCONFIRMED')) ?>
				</span>
            </td>
            <td><a class="btn btn-secondary btn-small" href="<?php echo $submission->link ?>">View</a></td>  
        </tr>
    <?php } ?> 
</table>

<?php if ($this->pagination && $pagination = $this->pagination->getPagesLinks()) {  ?>
    <div class="pagination">
        <?php echo $pagination; ?>
        <div class="pagecounter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </div>
    </div>
<?php } ?>
