/* global jQuery: false */
'use strict';

(function($){
	$(document).ready(function(){
    var language = $('html').attr('lang');
    var chosenselectText = {'en': "Select an option", 'fr': 'Choisissez une option'};  
    $("#donate_addressCountry, #donate_civility").chosen({disable_search_threshold: 10, placeholder_text_single: chosenselectText[language]});
        
		var spotFreeAmount = $('#donate_amount_manual');
		var preselectedAmounts = $('#amounts');
		
        /*attribution de la class checked à l'élément sélectionné par defaut*/
        preselectedAmounts.find('input').each( function() {
            var _this = $(this);
            if ( _this.is(':checked')) 
            {
                 _this.parent('label').addClass('checked');
            }
        });
        
        preselectedAmounts.find('input').click(function(){
			var _this = $(this);
			var amount = _this.val();

			//on remonte une class checked sur le parent
			preselectedAmounts.find('label').removeClass('checked');
			_this.parent('label').addClass('checked');

			if(amount === '') {
				//si on a cocher none, alors on vide le manuelInput
				spotFreeAmount.val('');
			} else {
				        
				if(isNumber(amount)) {
					
					spotFreeAmount.val('');
					// Mise à jour du montant du don pour la calculette
					$('#calc-amount').trigger('amount', amount);
				}

				if(amount === 'manual') {
					spotFreeAmount.focus();
				}
			}
		});

		spotFreeAmount.keyup(function(){
			var amount = $(this).val();
			amount = amount.replace(',','.');
			amount = Number(amount.replace(/[^0-9\.]+/g,''));
			amount = amount.toFixed(2);
			if(isNumber(amount)) {
				$('#calc-amount').trigger('amount', amount);
			}else {
				$('#calc-amount').text('...');
			}
			//check manual 
			preselectedAmounts.find('input[value=manual]').click();
		});

	});

	var isNumber = function(n) {
		return !isNaN(parseFloat(n)) && isFinite(n);
	};
})(jQuery);