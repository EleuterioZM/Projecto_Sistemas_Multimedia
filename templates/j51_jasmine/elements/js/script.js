jQuery(document).ready(function($) {

  var j51Template = 'j51_jasmine';

  // has width100
  $('.control-group:has(.width100)').addClass('container-width100');

  // group index for legend
  var $legends = $('h3.legend'),
    group = 0;

  $legends.each(function() {

    var $legend = $(this),
      $legendGroup = $legend.closest('.control-group'),
      isSub = $legend.is('.sub-legend');
    // add legend class
    $legendGroup.addClass(isSub ? 'sub-legend-group' : 'top-legend-group');
    var $params = $legendGroup.nextUntil(function() {
      var $next = $(this),
        $nextIsLegend = $next.has('h3.legend').length,
        $nextIsSubLegend = $nextIsLegend && $next.find('h3.legend').is('.sub-legend');
      if (!isSub && $nextIsLegend && $nextIsSubLegend) {
        $next.find('h3.legend').data('top-legend', $legend);
      }
      return !$next.is('.control-group') || ($nextIsLegend && (isSub || !$nextIsSubLegend));
    });

    // store its legend
    $params.data('legend', $legend);
    $legend.data('params', $params);
  });

  // grouping legend and params
  $('.form-grid').each(function() {
    var $pane = $(this),
      $topLegends = $pane.find('.top-legend-group');
    $('<div />').addClass('group-legends').appendTo($pane).append($topLegends);
    $topLegends.each(function() {
      var $legend = $(this).find('h3'),
        $params = $legend.data('params'),
        $subLegends = $params.filter('.sub-legend-group'),
        $topGroup = $('<div />');

      $topGroup.addClass('top-group').appendTo($pane);
      var $subGroupDirect = $('<div />').addClass('sub-group sub-group-direct').appendTo($topGroup).append(
        $('<div />').addClass('sub-group-inner').append($params)
      );
      $subLegends.each(function() {
        var $subLegendGroup = $(this),
          $subLegend = $subLegendGroup.find('h3'),
          $params = $subLegend.data('params');
        $('<div />').addClass('sub-group').appendTo($topGroup).append(
          $('<div />').addClass('sub-group-inner').append($subLegendGroup).append(
            $('<div />').addClass('sub-group-params').append($params)
          )
        );
      });
      // remove empty group
      if (!$subGroupDirect.find('.sub-group-inner').children().length) $subGroupDirect.remove();
      // add sub-group-direct class to top-group-enabler
      $topGroup.find('.top-group-enabler').closest('.sub-group').addClass('sub-group-direct');
      // store for later use
      $(this).data('top-group', $topGroup);
    });
  });

  // show/hide top group
  var showTopGroup = function($legendGroup) {
    var $topGroup = $legendGroup.data('top-group'),
      $tabPane = $legendGroup.closest('joomla-tab-element'),
      $otherLegendGroups = $tabPane.find('.top-legend-group').not($legendGroup),
      $otherTopGroups = $tabPane.find('.top-group').not($topGroup);
    $otherTopGroups.removeClass('active').hide();
    $topGroup.addClass('active').fadeIn();
    $legendGroup.addClass('active');
    $otherLegendGroups.removeClass('active');

    $(document).trigger('switchLegendGroup', $legendGroup);
  }

  $('.top-legend-group').on('click', function() {
    showTopGroup($(this));
    if (localStorage) {
      localStorage.setItem('last_active_group', '#' + $(this).closest('joomla-tab-element').attr('id') +
        ' .top-legend-group:nth-child(' + ($(this).index() + 1) + ')');
    }
  });

  // last active
  var $lastActiveGroup;
  if (localStorage && localStorage.getItem('last_active_group')) {
    $lastActiveGroup = $(localStorage.getItem('last_active_group'));
  }

  setTimeout(function() {
    $('.tab-pane .top-legend-group:first-child').trigger('click');
    $lastActiveGroup.trigger('click');
  }, 500);

  // preset loader
  document.querySelector('.preset-loader').addEventListener('click', function(e) {
    e.preventDefault();

    var preset = document.querySelector('[name="preset"]').value;

    Joomla.request({
      method: 'POST',
      url: 'index.php?option=com_ajax&template=' + j51Template + '&format=json&method=loadPreset' +
        '&preset=' + preset + '&id=' + Joomla.getOptions('j51.template.style') +
        '&' + Joomla.getOptions('csrf.token') + '=1',
      onSuccess: function(data) {
        var json = JSON.parse(data);

        if (!json.success) {
          Joomla.renderMessages({
            warning: ['Preset could not be loaded: ' + json.message]
          });
          return;
        }

        alert('Preset got applied successfully! Page will reload.');

        window.location.reload();
      }
    });

    return false;
  });

  document.querySelector('.preset-export').addEventListener('click', function(e) {
    e.preventDefault();

    location = 'index.php?option=com_ajax&template=' + j51Template + '&format=raw&method=export&id=' + Joomla.getOptions('j51.template.style') +
      '&' + Joomla.getOptions('csrf.token') + '=1';

    return false;
  });

  document.querySelector('.preset-input').addEventListener('change', function(e) {
    e.preventDefault();

    var file = e.target.files[0];
    if (!file) {
      return;
    }
    var reader = new FileReader();
    reader.onload = function(e) {
      Joomla.request({
        method: 'POST',
        url: 'index.php?option=com_ajax&template=' + j51Template + '&format=json&method=import' +
          '&id=' + Joomla.getOptions('j51.template.style') + '&' + Joomla.getOptions('csrf.token') + '=1',
        data: e.target.result,
        onSuccess: function(data) {
          var json = JSON.parse(data);

          if (!json.success) {
            Joomla.renderMessages({
              warning: ['Settings could not being imported: ' + json.message]
            });
            return;
          }

          alert('Settings got imported successfully! Site will reload.');

          window.location.reload();
        }
      });
    };
    reader.readAsText(file);

    return false;
  });

});
