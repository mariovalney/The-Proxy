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
        nome: "[\\d\\w\\s]{0,20}",
        dados: "[\\d\\w\\s]{0,50}",
        dadosrule: "[\\d\\w\\s]{0,30}"
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
                    if ( $(el).val() == "*" ) {
                        $(el).removeClass('invalid');
                        $(el).addClass('valid');
                    } else {
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
                    if ($(el).val() == '*') {
                        $(el).removeClass('invalid');
                        $(el).addClass('valid');
                    } else {
                        var input = parseInt( $(el).val() );

                        if ( (input >= 0) && (input <= 65536)) {
                            $(el).removeClass('invalid');
                            $(el).addClass('valid');
                        } else {
                            $(el).removeClass('valid');
                            $(el).addClass('invalid');
                            canSubmit = false;
                        }
                    }
                }
            });            

            // Verifica se algum elemento com mascara de DADOS está errado
            form.find('.mask-nome').each(function(index, el) {
                if ( $(el).val() != "" ) {
                    var input = $(el).val();
                    var padrao = new RegExp(regex.nome);
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

            // Verifica se algum elemento com mascara de DADOS PARA REGRAS está errado
            form.find('.mask-dados-for-rule').each(function(index, el) {
                if ( $(el).val() != "" ) {
                    var input = $(el).val();
                    var padrao = new RegExp(regex.dadosrule);
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

                    if (ip != '*') {

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

                    if (porta != '*') {
                        var porta = porta.replace(/[^0-9]/g, '');

                        if ((parseInt(porta) < 0) || (parseInt(porta) > 65536)) {
                            porta = porta.slice(0, -1);
                        }
                    }

                    $(this).val(porta);

                    if (event.type == "keyup") {
                        $(this).change();
                    };

                });
            });
        }

        if (type === "nome") {
            this.filter("input").each( function() {

                $(this).on('keyup trymask', function(event) {
                    event.preventDefault();

                    var dados = $(this).val();
                    var dados = dados.replace(/[^\d\w\s]/g, '');

                    if (dados.length > 20) {
                        dados = dados.slice(0, 20);
                    }

                    $(this).val(dados);

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

        if (type === "dados-for-rule") {
            this.filter("input").each( function() {

                $(this).on('keyup trymask', function(event) {
                    event.preventDefault();

                    var dados = $(this).val();
                    var dados = dados.replace(/[^\d\w\s]/g, '');

                    if (dados.length > 30) {
                        dados = dados.slice(0, 30);
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

    // SELECTS
    $('select').material_select();

    // GENERAL INVALIDATED
    $('.validate.invalidated').removeClass('valid').addClass('invalid');

    // MASCARAS
    $('.mask-ip').addMask('ip');
    $('.mask-protocol').addMask('protocolo');
    $('.mask-door').addMask('porta');
    $('.mask-nome').addMask('nome');
    $('.mask-dados').addMask('dados');
    $('.mask-dados-for-rule').addMask('dados-for-rule');

    // EDIT
    $('.table-actions .edit').on('click', function(event) {
        event.preventDefault();

        if ($(this).parents('.table-actions').hasClass('table-actions-rules')) {
            var ID = $(this).parents('tr.rule').attr('data-value');
            var nome = $(this).parents('tr.rule').find('.rule-nome').attr('data-value');
            var prioridade = $(this).parents('tr.rule').find('.rule-prioridade').attr('data-value');
            var ip_origem = $(this).parents('tr.rule').find('.rule-ip_origem').attr('data-value');
            var ip_destino = $(this).parents('tr.rule').find('.rule-ip_destino').attr('data-value');
            var direction = $(this).parents('tr.rule').find('.rule-direction').attr('data-value');
            var protocolo = $(this).parents('tr.rule').find('.rule-protocolo').attr('data-value');
            var porta_inicial = $(this).parents('tr.rule').find('.rule-portas').attr('data-value-initial');
            var porta_final = $(this).parents('tr.rule').find('.rule-portas').attr('data-value-final');
            var action = $(this).parents('tr.rule').find('.rule-action').attr('data-value');
            var dados = $(this).parents('tr.rule').find('.rule-dados').attr('data-value');

            $('#editrule .editing-id').text(ID);

            $('#editrule input#ID').val(ID);

            $('#editrule input#prioridade').val(prioridade).siblings('label').addClass('active');
            $('#editrule input#nome').val(nome).siblings('label').addClass('active');
            $('#editrule input#ip_origem').val(ip_origem).siblings('label').addClass('active');
            $('#editrule input#ip_destino').val(ip_destino).siblings('label').addClass('active');
            $('#editrule input#direction').val(direction).siblings('label').addClass('active');
            $('#editrule input#protocolo').val(protocolo).siblings('label').addClass('active');
            $('#editrule input#porta_inicial').val(porta_inicial).siblings('label').addClass('active');
            $('#editrule input#porta_final').val(porta_final).siblings('label').addClass('active');
            $('#editrule input#action').val(action).siblings('label').addClass('active');
            $('#editrule input#dados').val(dados).siblings('label').addClass('active');

            $('#editrule').openModal();

        } else {

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
        }
    });

    // DELETE
    $('.delete').on('click', function(event) {
        event.preventDefault();

        if ($(this).parents('.table-actions').hasClass('table-actions-rules')) {
            var ID = $(this).parents('tr.rule').attr('data-value');

            $('#deleterule .delete-link').attr('href', '?delete=' + ID);
            $('#deleterule').openModal();
        
        } else {

            var ID = $(this).parents('tr.registry').find('.registry-ID').attr('data-value');

            $('#deleteregistry .delete-link').attr('href', '?delete=' + ID);
            $('#deleteregistry').openModal();
        }
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

    // FORM RULES
    $('#addruleform').submit(function(event) {
        if ( $(this).validate() ) {
            return true;
        } else {
            Materialize.toast('Verifique os campos marcados de vermelho no formulário.', 3000);
            return false;
        }
    });

    $('#editruleform').submit(function(event) {
        if ( $(this).validate() ) {
            return true;
        } else {
            Materialize.toast('Verifique os campos marcados de vermelho no formulário.', 3000);
            return false;
        }
    });

    // FORM REGISTRIES
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