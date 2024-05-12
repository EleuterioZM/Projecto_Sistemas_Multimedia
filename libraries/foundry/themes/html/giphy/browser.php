<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="t-hidden" data-fd-giphy-browser-wrapper>
	<div id="fd">
		<div class="<?php echo $appearance; ?> si-theme-<?php echo $accent;?>">
			<div class="o-dropdown o-dropdown md:w-[400px]">
				<div class="fd-giphy-browser is-open t-hidden" data-fd-giphy-browser>
					<div class="fd-giphy" data-fd-giphy-container>
						<div class="fd-giphy-browser__input-search p-xs border-b border-solid border-gray-200">
							<?php echo $this->fd->html('form.text', 'giphy-search', '', null, [
								'placeholder' => JText::_('FD_GIPHY_SEARCH_PLACEHOLDER'),
								'attributes' => 'data-fd-giphy-search'
							]); ?>
						</div>
						<div class="font-bold text-center t-hidden pb-xs" data-fd-giphy-trending>
							<?php echo JText::_('FD_GIPHY_TRENDING'); ?>
						</div>

						<?php echo $this->fd->html('tabs.render', [
							$this->fd->html('tabs.item', 'giphy-gifs', 'FD_GIPHY_GIFS_TAB_TITLE', function() {
							?>
								<div class="tab-pane max-h-[320px] overflow-hidden overflow-y-auto relative px-sm is-loading" data-fd-giphy-gifs-content>
									<div class="fd-giphy-list-container" data-fd-giphy-gifs-list></div>

									<?php echo $this->fd->html('loader.standard', ['class' => 'mx-auto my-md']); ?>

									<?php echo $this->fd->html('html.emptyList', 'FD_GIPHY_GIFS_EMPTY_MESSAGE', [
										'icon' => 'fdi fa fa-giphy',
										'attributes' => 'data-fd-giphy-gifs-empty'
									]); ?>
								</div>
							<?php

							}, true),

							$this->fd->html('tabs.item', 'giphy-stickers', 'FD_GIPHY_STICKERS_TAB_TITLE', function() {
							?>
								<div class="tab-pane max-h-[320px] overflow-hidden overflow-y-auto relative px-sm" data-fd-giphy-stickers-content>
									<div class="fd-giphy-list-container" data-fd-giphy-stickers-list></div>

									<?php echo $this->fd->html('loader.standard', ['class' => 'mx-auto my-md']); ?>

									<?php echo $this->fd->html('html.emptyList', 'FD_GIPHY_STICKERS_EMPTY_MESSAGE', [
										'icon' => 'fdi fa fa-giphy',
										'attributes' => 'data-fd-giphy-stickers-empty'
									]); ?>
								</div>
							<?php
							}),
						], 'line', 'horizontal', ['tabHeaderClass' => 'mb-sm justify-evenly', 'tabHeaderItemClass' => 'flex-grow text-center']); ?>
					</div>

					<div class="fd-giphy-browser__result-footer">
						<div class="fd-powered-by-giphy px-xs py-xs leading-xs flex items-center">
							<span class="text-xs mr-2xs">Powered by</span>
							<svg height="14" viewBox="0 0 60 14" width="60" xmlns="http://www.w3.org/2000/svg"><g fill="none" transform="translate(2 1)"><path d="m5.16247271 0c1.30917608 0 2.46314058.26666667 3.70483336 1.50666667l-1.55886431 1.51999999c-.6883297-.64-1.52512265-.81333333-2.15271739-.81333333-1.53861931 0-2.53062386.90666667-2.53062386 2.72666667 0 1.2.63434305 2.67333334 2.53062386 2.67333334.49937644 0 1.28218278-.09333334 1.82204919-.48v-1.2h-2.38216059v-2.06h4.60910961v4.14c-.59385307 1.18666666-2.23369735 1.82666666-4.05574654 1.82666666-3.73182667 0-5.14897604-2.46-5.14897604-4.9 0-2.43333334 1.61959928-4.94 5.16247271-4.94zm21.65539199.28v3.54h3.3944102v-3.54h2.6385973v9.32h-2.6385973v-3.52666666h-3.3944102v3.52666666h-2.6655905v-9.32zm9.845814-.01333333 1.9570158 3.19999999 2.0312474-3.19999999h3.0030071v.12l-3.7453235 5.48666667v3.71333332h-2.6655905v-3.71333332l-3.5833634-5.50000001v-.10666666zm-17.282474 0c2.5306239 0 3.7858133 1.57333333 3.7858133 3.39333333 0 1.90666666-1.2686861 3.38-3.7858133 3.40666666h-1.9165258v2.52h-2.6655905v-9.31999999zm-6.2287089 0v9.31999999h-2.6520938v-9.31999999zm6.2287089 2.25999999h-1.9165258v2.32h1.9165258c.7423164 0 1.1202229-.53333332 1.1202229-1.14666666s-.3914032-1.17333334-1.1202229-1.17333334z" fill="var(--giphy-txt)" transform="translate(13.345051 1.093333)"/><path d="m8.09799641 4h1.34966602v6.6666666h-1.34966602z" fill="#93f"/><path d="m0 10.6666666h9.44766243v1.3333334h-9.44766243z" fill="#0cf"/><path d="m0 1.33333341h1.34966603v9.33333339h-1.34966603z" fill="#0f9"/><path d="m0 0h5.39866435v1.33333341h-5.39866435z" fill="#fff35c"/><path d="m8.09799648 2.66666666v-1.33333333h-1.34966606v-1.33333333h-1.34966607v1.34.00666667 1.32666667 1.32666666h1.34966607 1.34966606 1.34966607v-1.33333334z" fill="#f66"/><g fill="#0f0f0f" opacity=".4" transform="translate(4.048998)"><path d="m1.34966607 0v1.33333333h-1.34966607z"/><path d="m4.04899832 5.34666667v-1.34666667h1.34966607z"/></g></g></svg>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>