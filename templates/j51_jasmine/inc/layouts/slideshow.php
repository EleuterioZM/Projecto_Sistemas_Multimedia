<?php
defined( '_JEXEC' ) or die( 'Restricted index access' );
?>

<div id ="slideshow" class="block_holder">
	<?php if ($this->countModules( 'showcase' )) : ?>
		<div class="showcase">
			<div class="showcase_padding">
				<jdoc:include type="modules" name="showcase" style="mod_standard" />
			</div>
		</div>

		<div class ="mobile_showcase">	
			<div class="showcase_padding">			
			<?php if(($this->params->get('mobile_showcase_sw') == '1') && ($this->params->get('splashimage_mobile') == '')) : ?>
			<img src=<?php echo $this->baseurl ?>/<?php echo $mobile_showcase; ?> alt='ShowcaseImage' />
			<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>
<div class="clear"></div>
</div>
