/* global jQuery: false */
'use strict';
(function($){
    $(document).ready(function(){

        var $amount = $('#calc-amount');

        //chosen
        var language = $('html').attr('lang');
        var chosenselectText = {'en': 'Select an option', 'fr': 'Choisissez une option'};
        $('#donate_addressCountry, #donate_civility').chosen({disable_search_threshold: 10, placeholder_text_single: chosenselectText[language]});

        /*attribution de la class checked à l'élément sélectionné par defaut*/
        var preselectedAmounts = $('#amounts');
        preselectedAmounts.find('input').each( function() {
            var _this = $(this);
            if ( _this.is(':checked'))
            {
                 _this.parent('label').addClass('checked');
            }
        });

        preselectedAmounts.find('input').click(function(){
            var _this = $(this);
            //on remonte une class checked sur le parent
            preselectedAmounts.find('label').removeClass('checked');
            _this.parent('label').addClass('checked');
         });

        //plug amountSelector behavior
        var $amountSelector = $('.amount_selector');
        $amountSelector
        .amountSelector()
        .on('preselected-clicked.as', function(e){
            if(e.amount !== 'manual') {
                $amount.trigger('amount', e.amount);
            }

            $amountSelector.not($(this)).trigger('reset.as');
            //automaticaly pass others amount_selector to none

            var matches = this.className.match(/tunnel\-([a-zA-Z0-9\-_]*)/);

            if(matches) {
                $('#donate_payment_method button').each(function() {
                    var $_this = $(this);

                    if($_this.hasClass(matches[0])) {
                        $_this.show();
                    } else {
                        $_this.hide();
                    }
                });
            }

        })
        .on('manual-clicked.as', function(e){
            $amount.trigger('amount', e.amount);
        })
        ;

        var isNumber = function(n) {
            return !isNaN(parseFloat(n)) && isFinite(n);
        };

        //Set default buttons on form submit
        $('.amount_selector input:checked').each(function(){
            var $_this = $(this);
            if(isNumber($_this.val())){
                $_this.trigger('click');
            }
        });

    });

})(jQuery);