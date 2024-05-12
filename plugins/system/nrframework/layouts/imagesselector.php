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

defined('_JEXEC') or die('Restricted access');
extract($displayData);

if (empty($images))
{
	return;
}

$value = !empty($value) ? $value : $images[0];
$heightAtt = !empty($height) ? ' style="height:' . $height . ';"' : '';

?>
<div class="nr-images-selector <?php echo $class; ?>" style="max-width: <?php echo $width;?>;">
	<?php
	if ($required)
	{
		?><input type="hidden" required class="required" id="<?php echo $id; ?>"/><?php
	}
	
	foreach ($images as $key => $img)
	{
		$id = "nr-images-selector-" . md5(uniqid() . $img);
		
		$item_value = $key_type === 'filename' ? pathinfo($img, PATHINFO_FILENAME) : $img;

		$isChecked = $value == $item_value ? ' checked="checked"' : '';
		?>
		<div class="nr-images-selector-item image"<?php echo $heightAtt; ?>>
			<input type="radio" id="<?php echo $id; ?>" value="<?php echo $item_value; ?>" name="<?php echo $name; ?>"<?php echo $isChecked; ?> />
			<label for="<?php echo $id; ?>"><img src="<?php echo JURI::root() . $img; ?>" alt="<?php echo $img; ?>" /></label>
		</div>
		<?php
	}
	?>
</div>