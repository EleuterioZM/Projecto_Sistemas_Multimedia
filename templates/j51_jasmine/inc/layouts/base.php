<?php

?>
<?php if ($helper->blockExists($this, 'base-1')) { ?>
<div id="container_base1_modules" class="module_block border_block">
	<div class="wrapper960">
		<div id="base-1">
		<?php j51Block($this, 'base-1'); ?>
		</div>
	</div>
</div>
<?php }?>
<?php if ($helper->blockExists($this, 'base-2') || $this->countModules( 'footer-1' ) || $this->countModules( 'footer-2' )) { ?>
<div id="container_base2_modules" class="module_block border_block">
	<div class="wrapper960">
		<div id="base-2">
		<?php j51Block($this, 'base-2'); ?>
		</div>
		<?php if ($this->countModules( 'footer-1' )) : ?>    
			<div id="footer-1" class="block_holder_margin">
				<jdoc:include type="modules" name="footer-1" style="mod_standard" />
				<div class="clear"></div>
			</div>
		<?php endif; ?>
		<?php if ($this->countModules( 'footer-2' )) : ?>  
			<div id="footer-2" class="block_holder_margin">
				<jdoc:include type="modules" name="footer-2" style="mod_standard" />
				<div class="clear"></div>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php }?>