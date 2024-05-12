Komento
.require()
.script('site/comments/wrapper')
.done(function($) {

	// Implement the wrapper
	$('[data-kt-wrapper][data-component="<?php echo $component; ?>"][data-cid="<?php echo $cid; ?>"]').implement(Komento.Controller.Wrapper, {
		total: parseInt("<?php echo $commentCount;?>"),
		lastchecktime: '<?php echo FH::date()->toSql(); ?>',
		initList: <?php echo $this->my->allow('read_comment') && (!isset($ajaxcall) || $ajaxcall == 0) ? 'true' : 'false';?>,
		prism: <?php echo $this->config->get('bbcode_code') ? 'true' : 'false';?>,
		guest: <?php echo $this->my->guest ? 'true' : 'false'; ?>,
		isRTL: <?php echo FH::isRTL() ? 'true' : 'false'; ?>,
		component: "<?php echo $component; ?>",
		cid: "<?php echo $component; ?>",
		contentLink: "<?php echo $contentLink ?>",
		sort: "<?php echo $activeSort; ?>"
	});
});