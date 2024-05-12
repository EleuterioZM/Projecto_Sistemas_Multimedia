<?php
/**
* @package		Komento
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/controller.php');

class KomentoControllerLicenseVerify extends KomentoSetupController
{
	public function execute()
	{
		$key = SI_KEY;
		$result = new stdClass();

		if (!$key) {
			$result->state = 400;

			return $this->output($result);
		}
		
		// Verify the key
		$response = $this->verify($key);

		if ($response === false) {
			$result->state = 400;
			$result->message = JText::_('COM_KOMENTO_SETUP_UNABLE_TO_VERIFY');
			return $this->output($result);
		}

		if ($response->state == 400) {
			return $this->output($response);
		}

		// Get the total number of licenses
		if (count($response->licenses) < 1) {
			$result->state = 400;
			$result->message = JText::_('COM_KOMENTO_SETUP_UNABLE_TO_DETECT_LICENSES');
			return $this->output($result);
		}

		ob_start();
?>
		<select name="license" data-source-license>
			<?php foreach ($response->licenses as $license) { ?>
			<option value="<?php echo $license->reference;?>"><?php echo $license->title;?> - <?php echo $license->reference; ?></option>
			<?php } ?>
		</select>
<?php
		$output = ob_get_contents();
		ob_end_clean();

		$response->html = $output;
		return $this->output($response);
	}

	public function verify($key)
	{
		$post = [
			'apikey' => $key, 
			'product' => 'komento'
		];
		$resource = curl_init();

		curl_setopt($resource, CURLOPT_URL, SI_VERIFIER);
		curl_setopt($resource, CURLOPT_POST , true);
		curl_setopt($resource, CURLOPT_TIMEOUT, 120);
		curl_setopt($resource, CURLOPT_POSTFIELDS, $post);
		curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, false);

		$result = curl_exec($resource);
		curl_close($resource);

		if (!$result) {
			return false;
		}

		$result = json_decode($result);

		return $result;
	}
}