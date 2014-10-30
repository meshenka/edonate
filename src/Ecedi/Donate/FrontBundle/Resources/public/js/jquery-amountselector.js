'use strict';
/* global jQuery */

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

		return this.each(function() {

			var _this = $(this);

			//this is the selector
			var manual = _this.find('input[type=text]');
			var preselected = _this.find('input[type=radio]');

			//public Event Driven API
			manual.on('keyup', function() {
				var e = $(this);
				var amount = e.val();
				amount = amount.replace(',','.'); //we force . instead of ,
				amount = Number(amount.replace(/[^0-9\.]+/g,'')); //only numbers and  .
				amount = amount.toFixed(2); // trim to 2 digits after .

				var ev = $.Event('manual-clicked.as');
				
				if(isNumber(amount)) {
					ev.amount = amount;
				} else {
					ev.amount = null;
				}

				//throws public events
				_this.trigger(ev);

				//internal behavior
				//on keyup we force the preselected to manual
				preselected.find('input[value=manual]').click();
			});

			//public Event Driven API
			preselected.on('click', function() {
				var e = $(this);

				//throws public events
				var ev = $.Event('preselected-clicked.as');
				ev.amount = e.val();
				
				_this.trigger( ev);

				//internal behavior
				if(e.val() !== 'manual') {
					manual.val('');
				} else {
					manual.focus();
				}
			});
		});
		
	};
})(jQuery);