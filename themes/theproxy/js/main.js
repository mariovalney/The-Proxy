/**
 * The JS file to AvantDoc theme
 * 
 * Created by Mário Valney <mariovalney@gmail.com>
 */

Array.prototype.clean = function(deleteValue) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] == deleteValue) {         
            this.splice(i, 1);
            i--;
        }
    }
    return this;
};

(function ( $ ) {
    var canSubmit = true;
    var regex = {
        ip: "([0-9]{1,3}[.]{1}){3}[0-9]{1,3}",
        dados: "[\\d\\w\\s]{0,50}"
    }

    $.fn.validate = function() {
        this.filter("form").each( function() {
            canSubmit = true;
            var form = $(this);

            // Verifica se algum elemento com required está vazio
            form.find('[required]').each(function(index, el) {
                if ( $(el).val() == "" ) {
                    $(el).removeClass('valid');
                    $(el).addClass('invalid');
                    canSubmit = false;
                } else {
                    $(el).removeClass('invalid');
                    $(el).addClass('valid');
                }
            });

            // Verifica se algum elemento com mascara de IP está errado
            form.find('.mask-ip').each(function(index, el) {
                if ( $(el).val() != "" ) {
                    var input = $(el).val();
                    var padrao = new RegExp(regex.ip);
                    var regexResult = ( padrao.exec(input) != null ) ? padrao.exec(input) : 0;

                    if ( ( regexResult.index == 0 ) && ( regexResult[0] == regexResult.input ) ) {
                        $(el).removeClass('invalid');
                        $(el).addClass('valid');
                    } else {
                        $(el).removeClass('valid');
                        $(el).addClass('invalid');
                        canSubmit = false;
                    }
                }
            });

            // Verifica se algum elemento com mascara de PROTOCOLO está errado
            form.find('.mask-protocol').each(function(index, el) {
                if ( $(el).val() != "" ) {
                    var input = $(el).val();

                    if ( (input == 'TCP') || (input == 'UDP') || (input == 'ICMP') ) {
                        $(el).removeClass('invalid');
                        $(el).addClass('valid');
                    } else {
                        $(el).removeClass('valid');
                        $(el).addClass('invalid');
                        canSubmit = false;
                    }
                }
            });

            // Verifica se algum elemento com mascara de PORTA está errado
            form.find('.mask-door').each(function(index, el) {
                if ( $(el).val() != "" ) {
                    var input = parseInt( $(el).val() );

                    if ( (input >= 0) && (input <= 65536) ) {
                        $(el).removeClass('invalid');
                        $(el).addClass('valid');
                    } else {
                        $(el).removeClass('valid');
                        $(el).addClass('invalid');
                        canSubmit = false;
                    }
                }
            });            

            // Verifica se algum elemento com mascara de DADOS está errado
            form.find('.mask-dados').each(function(index, el) {
                if ( $(el).val() != "" ) {
                    var input = $(el).val();
                    var padrao = new RegExp(regex.dados);
                    var regexResult = ( padrao.exec(input) != null ) ? padrao.exec(input) : 0;

                    if ( ( regexResult.index == 0 ) && ( regexResult[0] == regexResult.input ) ) {
                        $(el).removeClass('invalid');
                        $(el).addClass('valid');
                    } else {
                        $(el).removeClass('valid');
                        $(el).addClass('invalid');
                        canSubmit = false;
                    }
                }
            });
        });

        return canSubmit;
    };

    $.fn.addMask = function( type ) {
        if (type === "ip") {
            this.filter("input").each( function() {

                $(this).on('keyup trymask', function(event) {
                    event.preventDefault();

                    var ip = $(this).val();

                    if (ip.slice(-1) !== '.') {

                        if (ip.length < 3 && ip.indexOf(".") <= 0) {
                            ip = ip.replace(/[^0-9]/g, '');
                        }

                        if (ip.length > 3 || ip.indexOf(".") > 0) {
                            var arrayIp = ip.split('.');
                            ip = '';

                            for (var i = 0; i < arrayIp.length; i++) {
                                arrayIp[i] = arrayIp[i].replace(/[^0-9]/g, '');

                                if (arrayIp[i].length > 3) {
                                    arrayIp[i] = arrayIp[i].slice(0, 3);
                                }
                                
                                if (arrayIp[i] < 0) {
                                    arrayIp[i] = 0;
                                }

                                if (arrayIp[i] > 254) {
                                    arrayIp[i] = 254;
                                }

                                if (i <= 3) {
                                    ip = ip + arrayIp[i] + ".";
                                }
                            };

                            if (ip.slice(-1) === '.') {
                                ip = ip.slice(0, -1);
                            }
                        } else if (ip.length == 3) {
                            ip = ip.replace(/[^0-9]/g, '');

                            if (ip < 0) {
                                ip = 0;
                            }

                            if (ip > 254) {
                                ip = 254;
                            }
                        }

                    } else {
                        if (ip.length == 1) {
                            ip = "";
                        }
                    }

                    $(this).val(ip);

                    if (event.type == "keyup") {
                        $(this).change();
                    };

                });
            });
        }

        if (type === "protocolo") {
            this.filter("input").each( function() {

                $(this).on('keyup trymask', function(event) {
                    event.preventDefault();

                    var protocolo = $(this).val();
                    var protocolo = protocolo.toUpperCase();
                    protocolo = protocolo.replace(/[^A-Z]/g, '');

                    if (protocolo.length > 4) {
                        protocolo = protocolo.slice(0, 4);
                    };

                    $(this).val(protocolo);

                    if (event.type == "keyup") {
                        $(this).change();
                    };

                });
            });
        }

        if (type === "porta") {
            this.filter("input").each( function() {

                $(this).on('keyup trymask', function(event) {
                    event.preventDefault();

                    var porta = $(this).val();
                    var porta = porta.replace(/[^0-9]/g, '');

                    if ((parseInt(porta) < 0) || (parseInt(porta) > 65536)) {
                        porta = porta.slice(0, -1);
                    }

                    $(this).val(porta);

                    if (event.type == "keyup") {
                        $(this).change();
                    };

                });
            });
        }

        if (type === "dados") {
            this.filter("input").each( function() {

                $(this).on('keyup trymask', function(event) {
                    event.preventDefault();

                    var dados = $(this).val();
                    var dados = dados.replace(/[^\d\w\s]/g, '');

                    if (dados.length > 50) {
                        dados = dados.slice(0, 50);
                    }

                    $(this).val(dados);

                    if (event.type == "keyup") {
                        $(this).change();
                    };

                });
            });
        }

        return this;
    };
}( jQuery ));

$(document).ready(function(){

	// MODAIS
	$('.modal-trigger').leanModal();

    if ( $('.modal.open-on-ready').length == 1 ) {
        $('.modal.open-on-ready').openModal();
        $('.modal.open-on-ready').find('form').validate();
    };

    // MASCARAS
    $('.mask-ip').addMask('ip');
    $('.mask-protocol').addMask('protocolo');
    $('.mask-door').addMask('porta');
    $('.mask-dados').addMask('dados');

    // EDIT
    $('.table-actions .edit').on('click', function(event) {
        event.preventDefault();

        var ID = $(this).parents('tr.registry').find('.registry-ID').attr('data-value');
        var ip_origem = $(this).parents('tr.registry').find('.registry-ip_origem').attr('data-value');
        var ip_destino = $(this).parents('tr.registry').find('.registry-ip_destino').attr('data-value');
        var protocolo = $(this).parents('tr.registry').find('.registry-protocolo').attr('data-value');
        var porta = $(this).parents('tr.registry').find('.registry-porta').attr('data-value');
        var dados = $(this).parents('tr.registry').find('.registry-dados').attr('data-value');

        $('#editregistry .editing-id').text(ID);

        $('#editregistry input#ID').val(ID);

        $('#editregistry input#ip_origem').val(ip_origem).siblings('label').addClass('active');
        $('#editregistry input#ip_destino').val(ip_destino).siblings('label').addClass('active');
        $('#editregistry input#protocolo').val(protocolo).siblings('label').addClass('active');
        $('#editregistry input#porta').val(porta).siblings('label').addClass('active');
        $('#editregistry input#dados').val(dados).siblings('label').addClass('active');

        $('#editregistry').openModal();
    });

    // DELETE
    $('.table-actions .delete').on('click', function(event) {
        event.preventDefault();

        var ID = $(this).parents('tr.registry').find('.registry-ID').attr('data-value');

        $('#deleteregistry .delete-link').attr('href', '?delete=' + ID);
        $('#deleteregistry').openModal();
    });

    // IMPORT
    $('.btn-import-trigger').on('click', function(event) {
        event.preventDefault();
        $('input#file').trigger('click');
    });

    $('input#file').on('change', function(event) {
        event.preventDefault();
        $(this).parents('form').submit();
    });

    // FORM
    $('#addregistryform').submit(function(event) {
        if ( $(this).validate() ) {
            return true;
        } else {
            Materialize.toast('Verifique os campos marcados de vermelho no formulário.', 3000);
            return false;
        }
    });

    $('#editregistryform').submit(function(event) {
        if ( $(this).validate() ) {
            return true;
        } else {
            Materialize.toast('Verifique os campos marcados de vermelho no formulário.', 3000);
            return false;
        }
    });
});