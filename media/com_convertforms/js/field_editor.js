!function(){"use strict";document.addEventListener("ConvertFormsBeforeSubmit",function(e){e=e.detail.instance.selector.querySelectorAll('.cf-control-group[data-type="editor"] textarea');0!=e.length&&e.forEach(function(e){var t=Joomla.editors.instances[e.id];t&&(e.value=t.getValue())})})}();

