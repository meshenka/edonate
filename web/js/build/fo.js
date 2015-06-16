/**
 * WARNING this filed is generated with gulp. Do not modify here!
 *
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @version 2.4.1
 * @package eDonate
 * @build Tue Jun 16 2015 11:07:50 GMT+0200 (CEST)
 * @license  http://opensource.org/licenses/MIT MIT
 * @copyright 2015 Agence Ecedi http://ecedi.fr
 */
"use strict";!function(c){var t={};t.calculator={recalc:function(){c("[id^=calc-reduced]").calc("price - (pourcentage * price/100)",{price:c("[id^=calc-amount]"),pourcentage:c("[id^=calc-pourcentage]")},function(c){return c.toFixed(2).replace(/\./,",")},function(){})},attach:function(){c.Calculation.setDefaults({onParseError:function(){this.css("backgroundColor","#cc0000")},onParseClear:function(){this.css("backgroundColor","")}}),c("#checkISF").change(function(){c(this).is(":checked")?(c("#calc-pourcentage").text("75"),t.calculator.recalc()):(c("#calc-pourcentage").text("66"),t.calculator.recalc())});var a=c("#calc-amount");a.bind("amount",function(a,e){c(this).text(e),t.calculator.recalc()}),t.calculator.recalc()}},t.calculator.attach()}(jQuery),function(c){c(document).ready(function(){var t=c("#calc-amount"),a=c("html").attr("lang"),e={en:"Select an option",fr:"Choisissez une option"};c("#donate_addressCountry, #donate_civility").chosen({disable_search_threshold:10,placeholder_text_single:e[a]});var n=c(".amount_selector");n.amountSelector().on("preselected-clicked.as",function(a){"manual"!==a.amount&&t.trigger("amount",a.amount),n.not(c(this)).trigger("reset.as");var e=this.className.match(/tunnel\-([a-zA-Z0-9\-_]*)/);e&&c("#donate_payment_method button").each(function(){var t=c(this);t.hasClass(e[0])?t.show():t.hide()})}).on("manual-clicked.as",function(c){t.trigger("amount",c.amount)});var o=function(c){return!isNaN(parseFloat(c))&&isFinite(c)};c(".amount_selector input:checked").each(function(){var t=c(this);o(t.val())&&t.trigger("click")})})}(jQuery);