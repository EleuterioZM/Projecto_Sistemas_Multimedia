<?php if (KT::push()->isEnabled()) { ?>
Komento.require()
.script('https://cdn.onesignal.com/sdks/OneSignalSDK.js')
.done(function($) {
	var OneSignal = window.OneSignal || [];
	OneSignal.push(["init", {
		appId: "<?php echo $this->config->get('onesignal_app_id');?>",

		<?php if ($subdomain) { ?>
		subdomainName: '<?php echo $subdomain;?>',
		<?php } ?>

		welcomeNotification: {
			<?php if ($this->config->get('onesignal_show_welcome')) { ?>
				"title": "<?php echo JText::_('COM_KT_ONESIGNAL_WELCOME_TITLE', true);?>",
				"message": "<?php echo JText::_('COM_KT_ONESIGNAL_WELCOME_MESSAGE', true);?>"
			<?php } else { ?>
				disable: true
			<?php } ?>
		},

		<?php if ($this->config->get('onesignal_safari_id')) { ?>
		safari_web_id: "<?php echo $this->config->get('onesignal_safari_id');?>",
		<?php } ?>
		autoRegister: true,
		notifyButton: {
			enable: false
		}
	}]);


	OneSignal.push(function() {

		OneSignal.getTags(function(tags) {

			<?php if (isset($this->my) && $this->my->id) { ?>
				OneSignal.push(['sendTags', {
					"id": "<?php echo $this->my->id;?>",
					"type": "user"
				}]);
			<?php } else { ?>
				if (tags.id == undefined) {
					OneSignal.push(['sendTags', {
						"id": "0",
						"type": "guest"
					}]);
				}
			<?php } ?>
			
		});
	});
});
<?php } ?>