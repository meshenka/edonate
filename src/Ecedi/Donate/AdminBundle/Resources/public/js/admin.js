/* jshint undef: true, unused: true */
/* global confirm: false */
/* global jQuery: false */
'use strict';

(function ($) {
	$(document).ready(function() {
		if($('input.datepicker').length) {
			$('input.datepicker').datepicker({
				inline: true,
				showOtherMonths: true,
				dayNamesMin: ['DIM', 'LUN', 'MAR', 'MER', 'JEU', 'VEN', 'SAM'],
			});
		}
    // Confirmation de suppression utilisateur
	if($('#account_submit_delete').length > 0) {
      var deleteUser = true;
      $('#account_submit_delete').click(function(event){
        deleteUser = confirm('Etes-vous sûr de vouloir supprimer cet utilisateur ?');
        if(!deleteUser) {
          event.preventDefault();
        }
      });
    }
	});

})(jQuery);

/**
 * Console polyfil
 * @see https://github.com/paulmillr/console-polyfill/blob/master/index.js
 */
(function (con) {
	var prop, method;
	var empty = {};
	var dummy = function() {};
	var properties = 'memory'.split(',');
	var methods = ('assert,count,debug,dir,dirxml,error,exception,group,' +
		'groupCollapsed,groupEnd,info,log,markTimeline,profile,profileEnd,' +
		'time,timeEnd,trace,warn').split(',');
	while (prop = properties.pop()) {
		con[prop] = con[prop] || empty;
	}

	while (method = methods.pop()) {
		con[method] = con[method] || dummy;
	}
 })(window.console = window.console || {});
