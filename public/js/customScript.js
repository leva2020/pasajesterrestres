/*
 * Front End : David Pérez
 * davidres@gmail.com
 */

jQuery(document).ready(function($) {
	$('.weather-slider').bxSlider({
    controls: false
  });
});

$(function() {
    var currencies = []; // my array
    var txt = {};
    $.getJSON('/ochoa', null, function(data) {
        var obj = jQuery.parseJSON(data.ciudades);
        if (typeof obj == 'object') {
          ciudades = obj[0];
          ciudades.Table.forEach(function formatCities(element) {
              txt = {
                  value: element.nombre,
                  data: element.idlocali
              };
              currencies.push(txt);
          });

          $('#origen-cities').autocomplete({
              lookup: currencies,
              onSelect: function (suggestion) {
                  activarDestinos(suggestion.data);
                  var idCity = suggestion.data;
                  var city = suggestion.value;
                  $('#origen-cities').val(city);
                  $('#id-origen-cities').val(idCity);
              }
          });
        }
    });
});

function activarDestinos(idOrigen) {
    $(function(){
        $('#loading-ciudades').html('<img src="/images/bx_loader.gif" />');
        var txt = {}; // my object
        var currencies =  []; // my array
        $.getJSON('/ochoa/destino/' + idOrigen, null, function(data) {
            var obj = jQuery.parseJSON(data.ciudades);
            jQuery.each(obj.LocalidadTO, function(i, item){
                txt = {
                    value: item.Nombre,
                    data: item.Codigo
                };
                currencies.push(txt);
            });
            $('#destino-cities').removeAttr("disabled");
            $('#loading-ciudades').html('');
            // setup autocomplete function pulling from currencies[] array
            $('#destino-cities').autocomplete({
                lookup: currencies,
                onSelect: function (suggestion) {
                    var idCity = suggestion.data;
                    var city = suggestion.value;
                    $('#destino-cities').val(city);
                    $('#id-destino-cities').val(idCity);
                }
            });
        });
    });
}

function setCurrentDate() {
    date = new Date();
    var shortName = $('.date-selection.start').attr('shortName');
    day = getDayName(date, shortName);
    month = getMonthName(date, shortName);
    nday = date.getDate();

    $('.date-selection.start .txt-month').html(month);
    $('.date-selection.start .txt-date').html(nday);
    $('.date-selection.start .txt-day').html(day);
    $('.date-selection.end .txt-month').html(month);
    $('.date-selection.end .txt-date').html(nday);
    $('.date-selection.end .txt-day').html(day);
    $('#dateRegreso').val(date.dateFormat("Ymd"));
    $('#dateIda').val(date.dateFormat("Ymd"));
}

function getDayName(date, shortName) {
    var Days = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
    if (shortName) {
       return Days[date.getDay()].substr(0,3);
    } else {
       return Days[date.getDay()];
    }
}

function getMonthName(date, shortName) {
    var Months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    if (shortName) {
        return Months[date.getMonth()].substr(0,3);
    } else {
        return Months[date.getMonth()];
    }
}

$(document).ready(function () {
    setCurrentDate();
    jQuery('#datetimepickerIda').datetimepicker({
        i18n:{
            es:{
                months:[
                    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                ],
                dayOfWeek:[
                    "Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"
                ]
            }
        },
        todayHighlight: true,
        todayBtn: true,
        timepicker: false,
        format: 'Y/m/d',
        lang: 'es',
        closeOnDateSelect: true,
        minDate: (new Date()).toLocaleDateString(),
        onChangeDateTime: function(dp, $input){
            var shortName = $('.date-selection.start').attr('shortName');
            var date = dp.dateFormat("d");
            var month = getMonthName(dp, shortName);
            var day = getDayName(dp, shortName);
      
            $('.date-selection.start .txt-month').html(month);
            $('.date-selection.start .txt-date').html(date);
            $('.date-selection.start .txt-day').html(day);
            $('#dateIda').val(dp.dateFormat("Ymd"));
        },
        onShow:function(ct){
            this.setOptions({
                maxDate: jQuery('#datetimepickerRegreso').val()?jQuery('#datetimepickerRegreso').val():false
            });
        }
    });
    jQuery('#datetimepickerRegreso').datetimepicker({
        i18n:{
            es:{
                months:[
                    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                ],
                dayOfWeek:[
                    "Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"
                ]
            }
        },
        todayHighlight: true,
        todayBtn: true,
        timepicker: false,
        format: 'Y/m/d',
        lang: 'es',
        closeOnDateSelect: true,
        onChangeDateTime: function(dp, $input){
            var shortName = $('.date-selection.end').attr('shortName');
            var date = dp.dateFormat("d");
            var month = getMonthName(dp, shortName);
            var day = getDayName(dp, shortName);

            $('.date-selection.end .txt-month').html(month);
            $('.date-selection.end .txt-date').html(date);
            $('.date-selection.end .txt-day').html(day);
            $('#dateRegreso').val(dp.dateFormat("Ymd"));
        },
        onShow:function( ct ){
            this.setOptions({
                minDate:jQuery('#datetimepickerIda').val()?jQuery('#datetimepickerIda').val():false
            });
        }
   });
});

function dataSendPost() {
    var ciudadOrigen = $('#origen-cities').val();
    var idCiudadOrigen = $('#origen-cities').attr('idcity');
    var ciudadDestino = $('#destino-cities').val();
    var idCiudadDestino = $('#destino-cities').attr('idcity');
    var fechaIda = jQuery('#datetimepickerIda').val();

    $.post( "/tarifas/ochoa", { origen: ciudadOrigen, destino: ciudadDestino, idOrigen: idCiudadOrigen, idDestino: idCiudadDestino, fecha: fechaIda })
    .done(function( data ) {
      alert( "Data Loaded: " + data );
    });

}

function sumarPasajeros() {
    data = parseInt($(".txt-number").text());
    $(".txt-number").html(data+1);
    $('#passNumber').val($(".txt-number").text());
}

function disminuirPasajeros() {
    data = parseInt($(".txt-number").text());
    if (data > 0) {
        $(".txt-number").html(data-1);
    }
    $('#passNumber').val($(".txt-number").text());
}

function activarSoloIda(element) {
    $('.ticket-options .btn-1').removeClass('active');
    $('.ticket-options .btn-1').attr('style', 'cursor:pointer');
    $('.ticket-options .btn-1').attr('onclick', 'activarSoloIda($(this))');
    if ($('#datetimepickerRegreso').css('display') == 'none') {
        $('#datetimepickerRegreso').show();
        $('#tipo-pasaje').val('1');
    } else {
        $('#datetimepickerRegreso').hide();
        $('#tipo-pasaje').val('0');
    }
    element.addClass('active');
    element.attr('style', 'cursor:default');
    element.attr("onclick", null);
}

function acumularValor(element) {
    var elementTotalCompra = jQuery('#totalCompra');
    var suma = elementTotalCompra.attr('valor');
    var identificador = jQuery(element).closest('tr').find('#valorPasaje').attr('identificador');
    suma = parseInt(suma) + parseInt(jQuery(element).closest('tr').find('#valorPasaje').attr('valor'));
    elementTotalCompra.attr('valor', suma);
    elementTotalCompra.html('$' + suma);
    jQuery('#totalCompraForm').val(suma);
    jQuery('#submitContinue').removeAttr('disabled');

    var nRodamiento = jQuery(element).closest('tr').find('#valorPasaje').attr('nRodamiento');
    jQuery('#numeroRodamiento' + identificador).val(nRodamiento);
    var idRuta = jQuery(element).closest('tr').find('#valorPasaje').attr('idRuta');
    jQuery('#idReferencia' + identificador).val(idRuta);
    var referencia = jQuery(element).closest('tr').find('#valorPasaje').attr('referencia');
    jQuery('#referencia' + identificador).val(referencia);
    var idRuta = jQuery(element).closest('tr').find('#valorPasaje').attr('idRuta');
    jQuery('#idRuta' + identificador).val(idRuta);
    var ruta = jQuery(element).closest('tr').find('#valorPasaje').attr('ruta');
    jQuery('#ruta' + identificador).val(ruta);
    var servicio = jQuery(element).closest('tr').find('#valorPasaje').attr('servicio');
    jQuery('#servicio' + identificador).val(servicio);
    var puestosLibres = jQuery(element).closest('tr').find('#valorPasaje').attr('puestosLibres');
    jQuery('#puestosLibres' + identificador).val(puestosLibres);
    var dataFecha = jQuery(element).closest('tr').find('#dataFecha'  + identificador).attr('data');
    jQuery('#fecha' + identificador).val(dataFecha);
    var hora = jQuery(element).closest('tr').find('#dataHora'  + identificador).attr('data');
    jQuery('#hora' + identificador).val(hora);
    var valorPasaje = parseInt(jQuery(element).closest('tr').find('#valorPasaje').attr('valor'));
    jQuery('#valorPasaje' + identificador).val(valorPasaje);
}

function setInfantes(element) {
    var identificador = jQuery(element).attr('id');
    jQuery('#formSubmit').find("#" + identificador).val(element.prop('checked'));
}

function seleccionarPuesto(element) {
    
    var idRod = element.attr('idRodamiento');
    var nPuesto = element.attr('puesto');
    var disp = element.attr('disp');

    $.post( '/bloquear-puesto', { idRodamiento: idRod, puesto: nPuesto, disponible: disp } )
    .done(function( data ) {
        var obj = jQuery.parseJSON(data);
//        alert( "EjecucionExitosa: " + obj.EjecucionExitosa );
        if (obj.EjecucionExitosa == 'true') {
            alert("Puesto reservado exitosamente");
        } else if (obj.EjecucionExitosa == 'false') {
            alert("El puesto que intentas reservar ya está ocupado");
        }
//        alert( "IdVenta: " + obj.IdVenta );
//        alert( "MensajeValidacion: " + obj.MensajeValidacion );
//        alert( "NumeroTiquetes: " + obj.NumeroTiquetes );
    });
    
    var clase = element.attr('class');
    
    if (clase == 'selected') {
        unSelectPlace(element);
        return false;
    } else if (clase == 'occupy') {
        return false;
    }
    
    element.removeClass(clase).addClass('selected');
    jQuery('#submitContinue').removeAttr('disabled');
    
    var elementTotalCompra = jQuery('#totalCompra');
    var suma = elementTotalCompra.attr('valor');
//    var suma = 0;
    suma = parseInt(suma) + parseInt(jQuery(element).closest('article').find('#valorPasaje').attr('valor'));
    elementTotalCompra.attr('valor', suma);
    elementTotalCompra.html('$' + suma);
    
    $('#totalCompraForm').val(suma);
}

function unSelectPlace(element) {
    element.removeClass("selected").addClass('free');

    var elementTotalCompra = jQuery('#totalCompra');
    var suma = elementTotalCompra.attr('valor');
//    var suma = 0;
    suma = parseInt(suma) - parseInt(jQuery(element).closest('article').find('#valorPasaje').attr('valor'));
    elementTotalCompra.attr('valor', suma);
    elementTotalCompra.html('$' + suma);
    
    $('#totalCompraForm').val(suma);
}

$('.test-popup-link').magnificPopup({
    type: 'inline',
    midClick: true,
    modal: true,
    preloader: false
   // other options
});

$(document).on('click', '.popup-modal-dismiss', function (e) {
 e.preventDefault();
 $.magnificPopup.close();
});

$('.inline-popups').magnificPopup({
//     delegate: 'a',
    removalDelay: 500, //delay removal by X to allow out-animation
    callbacks: {
        beforeOpen: function() {
            this.st.mainClass = this.st.el.attr('data-effect');
        }
    },
    midClick: true // allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.
});
