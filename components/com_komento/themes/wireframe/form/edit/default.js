Komento.require()
.script('site/form/form')
.done(function($) {

	$('[data-kt-edit-form]').implement(Komento.Controller.Form, {
		"formElement": "[data-kt-edit-form]",
		"isEdit": true,
		"currentRating": "<?php echo $comment->ratings; ?>",
		"location": <?php echo $this->my->canShareLocation() ? 'true' : 'false';?>,
		"location_key": "<?php echo $this->config->get('location_key');?>",
		"attachments": {
			"enabled": <?php echo $this->my->canUploadAttachments() ? 'true' : 'false';?>,
			"upload_max_size": "<?php echo $this->config->get('upload_max_size');?>mb",
			"upload_max_files": "<?php echo $this->config->get('upload_max_file');?>",
			"extensions": "<?php echo $this->config->get('upload_allowed_extension');?>",
			"files": '<?php echo json_encode($attachments); ?>'
		}
	});
});

