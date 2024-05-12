Komento.ready(function($){
	if (!Komento.editorInitialized) {
		Komento.theme = '<?php echo $this->config->get('layout_appearance'); ?>'
		Komento.bbcode = <?php echo $this->config->get('enable_bbcode') ? 'true' : 'false'; ?>;

		<?php  if ($this->config->get('enable_bbcode')) { ?>
		Komento.bbcodeButtons = function() {
			var settings = {
				fdTheme: Komento.theme,
				previewParserVar: 'data',
				markupSet: [],
				resizeHandle: false,
				onTab: {
					keepDefault: false,
					replaceWith: '    '
				}
			};

			<?php if ($this->config->get('bbcode_bold')) { ?>
			settings.markupSet.push({
				name: "<?php echo JText::_('COM_KOMENTO_BBCODE_BOLD', true);?>",
				key:'B',
				openWith:'[b]',
				closeWith:'[/b]',
				className:'fd-markitup-bold'
			});
			<?php } ?>

			<?php if ($this->config->get('bbcode_italic')) { ?>
			settings.markupSet.push({
				name: "<?php echo JText::_('COM_KOMENTO_BBCODE_ITALIC', true); ?>",
				key:'I',
				openWith:'[i]',
				closeWith:'[/i]',
				className:'fd-markitup-italic'
			});
			<?php } ?>

			<?php if ($this->config->get('bbcode_underline')) { ?>
			settings.markupSet.push({
				name: "<?php echo JText::_('COM_KOMENTO_BBCODE_UNDERLINE', true); ?>",
				key:'U',
				openWith:'[u]',
				closeWith:'[/u]',
				className:'fd-markitup-underline'
			});
			<?php } ?>

			<?php if ($this->config->get('bbcode_quote')) { ?>
			settings.markupSet.push({
				name: "<?php echo JText::_('COM_KOMENTO_BBCODE_QUOTE', true); ?>",
				openWith:'[quote]',
				closeWith:'[/quote]',
				className:'fd-markitup-quote'
			});
			<?php } ?>

			

			<?php if ($this->config->get('bbcode_link') || $this->config->get('bbcode_picture') || $this->config->get('bbcode_video') || $this->config->get('bbcode_giphy')) { ?>
			settings.markupSet.push({separator:'---------------' });
			<?php } ?>

			<?php if ($this->config->get('bbcode_link')) { ?>
			settings.markupSet.push({
				name: "<?php echo JText::_('COM_KOMENTO_BBCODE_LINK', true); ?>",
				key:'L',
				openWith:'[url="[![Link:!:http://]!]"(!( title="[![Title]!]")!)]', closeWith:'[/url]',
				placeHolder: "<?php echo JText::_('COM_KOMENTO_BBCODE_LINK_TEXT', true); ?>",
				className:'fd-markitup-link'
			});
			<?php } ?>

			<?php if ($this->config->get('bbcode_picture')) { ?>
			settings.markupSet.push({
				name: "<?php echo JText::_('COM_KOMENTO_BBCODE_PICTURE', true); ?>",
				key:'P',
				replaceWith:'[img][![Url]!][/img]',
				className:'fd-markitup-picture'
			});
			<?php } ?>

			<?php if ($this->config->get('bbcode_video')) { ?>
			settings.markupSet.push({
				name: "<?php echo JText::_('COM_KOMENTO_BBCODE_VIDEO', true); ?>",
				replaceWith: function(h) {
					Komento.dialog({
						"content": Komento.ajax('site/views/bbcode/video', {"caretPosition": h.caretPosition, "element": $(h.textarea).attr('id') })
					});
				},
				className: 'fd-markitup-video'
			});
			<?php } ?>

			

			<?php if ($this->config->get('bbcode_bulletlist') || $this->config->get('bbcode_numericlist') || $this->config->get('bbcode_bullet')) { ?>
			settings.markupSet.push({separator:'---------------' });
			<?php } ?>

			<?php if ($this->config->get('bbcode_bulletlist')) { ?>
			settings.markupSet.push({
				name: "<?php echo JText::_('COM_KOMENTO_BBCODE_BULLETLIST', true); ?>",
				openWith:'[list]\n[*]',
				closeWith:'\n[/list]',
				className:'fd-markitup-bullet'
			});
			<?php } ?>

			<?php if ($this->config->get('bbcode_numericlist')) { ?>
			settings.markupSet.push({
				name: "<?php echo JText::_('COM_KOMENTO_BBCODE_NUMERICLIST', true); ?>",
				openWith:'[list=[![Starting number:!:1]!] type=decimal]\n[*]',
				closeWith:'\n[/list]',
				className:'fd-markitup-numeric'
			});
			<?php } ?>

			<?php if ($this->config->get('bbcode_bullet')) { ?>
			settings.markupSet.push({
				name: "<?php echo JText::_('COM_KOMENTO_BBCODE_BULLET', true); ?>",
				openWith:'[*]',
				className:'fd-markitup-list'
			});
			<?php } ?>

			<?php if ($this->config->get('bbcode_code') || $this->config->get('bbcode_spoiler')) { ?>
			settings.markupSet.push({separator:'---------------' });
			<?php } ?>

			<?php if ($this->config->get('bbcode_code')) { ?>
			settings.markupSet.push({
				name: "<?php echo JText::_('COM_KOMENTO_BBCODE_CODE', true); ?>",
				openWith:'[code type="xml"]',
				closeWith:'[/code]',
				className:'fd-markitup-code'
			});
			<?php } ?>

			<?php if ($this->config->get('bbcode_spoiler', true)) { ?>
			settings.markupSet.push({
				name: "<?php echo JText::_('COM_KT_BBCODE_SPOILER', true); ?>",
				openWith:'[spoiler]',
				closeWith:'[/spoiler]',
				className:'fd-markitup-spoiler'
			});
			<?php } ?>

			return settings;
		};
		<?php } ?>

		Komento.editorInitialized = true;
	}
});

Komento.require()
.script('site/structure/structure')
.done(function($) {

	var options = {
		"cid": "<?php echo $cid; ?>",
		"component": "<?php echo $component; ?>",
		"type": "<?php echo $type; ?>",
		"commentOptions": JSON.stringify(<?php echo json_encode($commentOptions, JSON_NUMERIC_CHECK); ?>),
		"initialLoad": <?php echo $loadFromAjax ? 'true' : 'false'; ?>,
		"returnUrl": "<?php echo base64_encode($loginReturn); ?>"
	};

	$('[data-kt-structure="<?php echo $identifier; ?>"]').implement(Komento.Controller.Structure, options);
});
