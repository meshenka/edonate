/* global jQuery: false */
'use strict';
(function($){
	$(document).ready(function(){

	    var amount = $('#calc-amount');

		//chosen
	    var language = $('html').attr('lang');
	    var chosenselectText = {'en': 'Select an option', 'fr': 'Choisissez une option'};
	    $('#donate_addressCountry, #donate_civility').chosen({disable_search_threshold: 10, placeholder_text_single: chosenselectText[language]});
	    
	    //plug amountSelector behavior
	    $('.amount_selector')
	    .amountSelector()
	    .on('preselected-clicked.as', function(e){
			if(e.amount !== 'manual') {
				amount.trigger('amount', e.amount);
			}
	    })
	    .on('manual-clicked.as', function(e){
			amount.trigger('amount', e.amount);
	    })
	    ;


	});

})(jQuery);