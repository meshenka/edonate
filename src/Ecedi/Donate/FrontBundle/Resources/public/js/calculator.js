/* global jQuery: false */
/**
 *
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @package eDonate
 * @license  http://opensource.org/licenses/MIT MIT
 * @copyright 2015 Agence Ecedi http://ecedi.fr
 */
'use strict';

(function ($) {

  var Donate = {};

  Donate.calculator = {

    recalc: function() {
      $('[id^=calc-reduced]').calc(
        'price - (pourcentage * price/100)',
        {
          price: $('[id^=calc-amount]'),
          pourcentage: $('[id^=calc-pourcentage]')
        },
        function (s){
          return s.toFixed(2).replace(/\./,',');
        },
        function (){
          //var sum = $this.sum();
        }
      );
    },

    attach: function() {

      //init du calculator
      $.Calculation.setDefaults({
        onParseError: function(){
          this.css('backgroundColor', '#cc0000');
        },
        onParseClear: function (){
          this.css('backgroundColor', '');
        }
      });

      //si on coche la case
      $('#checkISF').change(function() {
        if($(this).is(':checked')) {
          $('#calc-pourcentage').text('75');
          Donate.calculator.recalc();
        } else {
          $('#calc-pourcentage').text('66');
          Donate.calculator.recalc();
        }
      });

      //quand le montant change
      var amount = $('#calc-amount');
      amount.bind('amount', function(event, amount) {
        $(this).text(amount);
        Donate.calculator.recalc();
      });

      Donate.calculator.recalc();
    }
  };

  Donate.calculator.attach();

})(jQuery);