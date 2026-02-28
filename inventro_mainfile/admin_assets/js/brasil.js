/**
 * Máscaras e funções brasileiras para o sistema Inventro
 * - Máscaras para CPF, CNPJ, CEP e Telefone
 * - Busca de CEP via ViaCEP
 * - Validação de CPF/CNPJ
 * - Formatação de datas DD/MM/AAAA
 */

$(document).ready(function() {
    // Aplicar máscaras
    aplicarMascaras();
    
    // Configurar datepickers para formato brasileiro
    configurarDatepickers();
});

/**
 * Aplicar máscaras nos campos
 */
function aplicarMascaras() {
    // Máscara CPF: 000.000.000-00
    $('.cpf-mask').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        $(this).val(value.substring(0, 14));
    });
    
    // Máscara CNPJ: 00.000.000/0000-00
    $('.cnpj-mask').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        value = value.replace(/^(\d{2})(\d)/, '$1.$2');
        value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
        value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
        $(this).val(value.substring(0, 18));
    });
    
    // Máscara CEP: 00000-000
    $('.cep-mask').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
        $(this).val(value.substring(0, 9));
    });
    
    // Máscara Telefone: (00) 00000-0000
    $('.telefone-mask').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length <= 10) {
            // Telefone fixo: (00) 0000-0000
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
        } else {
            // Celular: (00) 00000-0000
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
        }
        $(this).val(value.substring(0, 15));
    });
}

/**
 * Alternar entre CPF e CNPJ
 */
function toggleCpfCnpj(tipo) {
    if (tipo === 'F') {
        $('#cpf_group').show();
        $('#cnpj_group').hide();
        $('#cnpj').val('');
    } else {
        $('#cpf_group').hide();
        $('#cnpj_group').show();
        $('#cpf').val('');
    }
}

/**
 * Buscar CEP via ViaCEP
 */
function buscarCEP() {
    var cep = $('#cep').val().replace(/\D/g, '');
    
    if (cep.length !== 8) {
        alert('CEP inválido! O CEP deve ter 8 dígitos.');
        return;
    }
    
    // Mostrar loading
    $('#cep').prop('disabled', true);
    
    $.ajax({
        url: 'https://viacep.com.br/ws/' + cep + '/json/',
        dataType: 'json',
        success: function(data) {
            if (data.erro) {
                alert('CEP não encontrado!');
            } else {
                // Preencher campos
                if ($('#address').length) {
                    var endereco = data.logradouro;
                    if (data.bairro) endereco += ', ' + data.bairro;
                    $('#address').val(endereco);
                }
                if ($('textarea[name="address"]').length) {
                    var endereco = data.logradouro;
                    if (data.bairro) endereco += ', ' + data.bairro;
                    $('textarea[name="address"]').val(endereco);
                }
                if ($('#cidade').length) $('#cidade').val(data.localidade);
                if ($('#estado').length) $('#estado').val(data.uf);
            }
        },
        error: function() {
            alert('Erro ao buscar CEP. Verifique sua conexão.');
        },
        complete: function() {
            $('#cep').prop('disabled', false);
        }
    });
}

/**
 * Validar CPF
 */
function validarCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    
    if (cpf.length !== 11) return false;
    
    // Eliminar CPFs inválidos conhecidos
    if (/^(\d)\1{10}$/.test(cpf)) return false;
    
    // Validar dígitos verificadores
    var soma = 0;
    for (var i = 0; i < 9; i++) {
        soma += parseInt(cpf.charAt(i)) * (10 - i);
    }
    var resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.charAt(9))) return false;
    
    soma = 0;
    for (var i = 0; i < 10; i++) {
        soma += parseInt(cpf.charAt(i)) * (11 - i);
    }
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.charAt(10))) return false;
    
    return true;
}

/**
 * Validar CNPJ
 */
function validarCNPJ(cnpj) {
    cnpj = cnpj.replace(/\D/g, '');
    
    if (cnpj.length !== 14) return false;
    
    // Eliminar CNPJs inválidos conhecidos
    if (/^(\d)\1{13}$/.test(cnpj)) return false;
    
    // Validar dígitos verificadores
    var tamanho = cnpj.length - 2;
    var numeros = cnpj.substring(0, tamanho);
    var digitos = cnpj.substring(tamanho);
    var soma = 0;
    var pos = tamanho - 7;
    
    for (var i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }
    
    var resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado !== parseInt(digitos.charAt(0))) return false;
    
    tamanho = tamanho + 1;
    numeros = cnpj.substring(0, tamanho);
    soma = 0;
    pos = tamanho - 7;
    
    for (var i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }
    
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado !== parseInt(digitos.charAt(1))) return false;
    
    return true;
}

/**
 * Formatar data para padrão brasileiro (DD/MM/AAAA)
 */
function formatarDataBR(data) {
    if (!data) return '';
    var partes = data.split('-');
    if (partes.length === 3) {
        return partes[2] + '/' + partes[1] + '/' + partes[0];
    }
    return data;
}

/**
 * Converter data brasileira para padrão americano (AAAA-MM-DD)
 */
function formatarDataUS(data) {
    if (!data) return '';
    var partes = data.split('/');
    if (partes.length === 3) {
        return partes[2] + '-' + partes[1] + '-' + partes[0];
    }
    return data;
}

/**
 * Configurar datepickers para formato brasileiro
 */
function configurarDatepickers() {
    if ($.fn.datepicker) {
        $.fn.datepicker.defaults.format = 'dd/mm/yyyy';
        $.fn.datepicker.defaults.language = 'pt-BR';
        $.fn.datepicker.defaults.autoclose = true;
        $.fn.datepicker.defaults.todayHighlight = true;
    }
}

/**
 * Formatar valor para moeda brasileira
 */
function formatarMoeda(valor) {
    return valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

/**
 * Converter moeda brasileira para número
 */
function converterMoedaParaNumero(valor) {
    return parseFloat(valor.replace(/[^\d,]/g, '').replace(',', '.'));
}
