'use strict';
/* global jQuery */
/* global console */

/**
 *
 * API 
 *   when selecting a predefined amount it throws a preselected-clicked.as event
 *   when a manual amount is keyuped  it throws a manual-clicked.as event
 */
(function($){

	var isNumber = function(n) {
		return !isNaN(parseFloat(n)) && isFinite(n);
	};

	$.fn.amountSelector = function() {

		var _this = this;

		//this is the selector
		var manual = this.find('input[type=text]');
		var preselected = this.find('input[type=radio]');

		//public Event Driven API
		manual.on('keyup', function() {
			var e = $(this);
			var amount = e.val();
			amount = amount.replace(',','.'); //we force . instead of ,
			amount = Number(amount.replace(/[^0-9\.]+/g,'')); //only numbers and  .
			amount = amount.toFixed(2); // trim to 2 digits after .

			var ev = $.Event('Manual Amount');
			
			if(isNumber(amount)) {
				ev.value = amount;
			} else {
				ev.value = null;
			}
			ev.emitter = e;

			//throws public events
			_this.trigger( 'manual-clicked.as', ev);
			console.log('trigger manual-clicked.as');

			//internal behavior
			//on keyup we force the preselected to manual
			preselected.find('input[value=manual]').click();
		});

		//public Event Driven API
		preselected.on('click', function() {
			var e = $(this);

			//throws public events
			var ev = $.Event('Preselected Amount');
			ev.value = e.val();
			ev.emitter = e;
			_this.trigger( 'preselected-clicked.as', ev);
			console.log('trigger preselected-clicked.as');

			//internal behavior
			if(e.val() !== 'manual') {
				manual.val('');
			} else {
				manual.focus();
			}
		});

		
	};
})(jQuery);