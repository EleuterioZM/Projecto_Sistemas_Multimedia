<?php if (!$options['lock'] && $this->my->allow('add_comment')) { ?>
FD.require()
.script('shared')
.done(function() {

Komento.require()
.script('site/form/form')
.done(function($) {

	$('[data-kt-form]').implement(Komento.Controller.Form, {
		"formElement": "[data-kt-form-element]",
		"location": <?php echo $this->my->canShareLocation() ? 'true' : 'false';?>,
		"location_key": "<?php echo $this->config->get('location_key');?>",
		"attachments": {
			"enabled": <?php echo $this->my->canUploadAttachments() ? 'true' : 'false';?>,
			"upload_max_size": "<?php echo $this->config->get('upload_max_size');?>mb",
			"upload_max_files": "<?php echo $this->config->get('upload_max_file');?>",
			"extensions": "<?php echo $this->config->get('upload_allowed_extension');?>"
		},
		"bbcode": Komento.bbcode,
		"showCode": <?php echo $this->config->get('bbcode_code') ? 'true' : 'false'; ?>,
		"showCaptcha": <?php echo $showCaptcha ? 'true' : 'false'; ?>,
		"recaptcha": <?php echo $this->config->get('antispam_captcha_type') == 1 && $this->config->get('antispam_captcha_enable') ? 'true' : 'false';?>,
		"recaptcha_invisible": <?php echo $this->config->get('antispam_recaptcha_invisible') && $this->config->get('antispam_captcha_type') == 1 ? 'true' : 'false';?>,
		"markupSet": Komento.bbcodeButtons,
		"characterLimit": <?php echo $this->config->get('antispam_max_length_enable') ? $this->config->get('antispam_max_length') : 'false';?>
	});

	const editor = jQuery('[data-kt-editor]');
	const isMentionEnabled = <?php echo $this->config->get('enable_mention') ? 'true' : 'false'; ?>;

	if (isMentionEnabled) {
		const tributeOptions = {
			'values': function (text, callback) {
				Komento.ajax('site/views/comments/searchUsers', {
					"q": text
				}).done(function(result) {
					callback(result);
				});
			},
			'selectTemplate': function (item) {
				return '@' + item.original.key + '­';
			},
			menuItemTemplate: function (item) {
				return item.string;
			},
			fdAccent: '<?php echo $this->config->get('layout_accent');?>',
			fdTheme: '<?php echo $this->config->get('layout_appearance'); ?>'
		};

		$(document).on('kt.init.tribute', function(event, editor) {
			fd.tribute(editor, tributeOptions); 
		});

		fd.tribute(editor, tributeOptions); 
	}

	
});

});
<?php } ?>