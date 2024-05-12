<?php if (!isset($init) || $init) { ?>
Komento.module("init", function($) {

	this.resolve();

	<?php echo $contents; ?>
}).done();

<?php } else { ?>
	<?php echo $contents; ?>
<?php } ?>