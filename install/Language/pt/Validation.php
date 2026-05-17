<?php

/**
 * This file is part of Aksara CMS, both framework and publishing
 * platform.
 *
 * @author     Aby Dahana <abydahana@gmail.com>
 * @copyright  (c) Aksara Laboratory <https://aksaracms.com>
 * @license    MIT License
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.txt file.
 *
 * When the signs come, those who don't believe at "that time"
 * will have only two choices, commit suicide or become brutal.
 */

return [
    // Core Messages
    'noRuleSets' => 'Nenhum conjunto de regras especificado na configuração de Validação.',
    'ruleNotFound' => '{0} não é uma regra válida.',
    'groupNotFound' => '{0} não é um grupo de regras de validação.',
    'groupNotArray' => 'O grupo de regras {0} deve ser um array.',
    'invalidTemplate' => '{0} não é um modelo de Validação válido.',

    // Rule Messages
    'alpha' => 'O campo {field} só pode conter caracteres alfabéticos.',
    'alpha_dash' => 'O campo {field} só pode conter caracteres alfanuméricos, sublinhados e traços.',
    'alpha_numeric' => 'O campo {field} só pode conter caracteres alfanuméricos.',
    'alpha_numeric_punct' => 'O campo {field} só pode conter caracteres alfanuméricos, espaços e ~ ! # $ % & * - _ + = | : .',
    'alpha_numeric_space' => 'O campo {field} só pode conter caracteres alfanuméricos e espaços.',
    'alpha_space' => 'O campo {field} só pode conter caracteres alfabéticos e espaços.',
    'decimal' => 'O campo {field} deve conter um número decimal.',
    'differs' => 'O campo {field} deve ser diferente do campo {param}.',
    'equals' => 'O campo {field} deve ser exatamente: {param}.',
    'exact_length' => 'O campo {field} deve ter exatamente {param} caracteres.',
    'greater_than' => 'O campo {field} deve conter um número maior que {param}.',
    'greater_than_equal_to' => 'O campo {field} deve conter um número maior ou igual a {param}.',
    'hex' => 'O campo {field} só pode conter caracteres hexadecimais.',
    'in_list' => 'O campo {field} deve ser um de: {param}.',
    'integer' => 'O campo {field} deve conter um número inteiro.',
    'is_natural' => 'O campo {field} só deve conter dígitos.',
    'is_natural_no_zero' => 'O campo {field} só deve conter dígitos e deve ser maior que zero.',
    'is_not_unique' => 'O campo {field} deve conter um valor existente previamente no banco de dados.',
    'is_unique' => 'O campo {field} deve conter um valor único.',
    'less_than' => 'O campo {field} deve conter um número menor que {param}.',
    'less_than_equal_to' => 'O campo {field} deve conter um número menor ou igual a {param}.',
    'matches' => 'O campo {field} não corresponde ao campo {param}.',
    'max_length' => 'O campo {field} não pode exceder {param} caracteres de comprimento.',
    'min_length' => 'O campo {field} deve ter pelo menos {param} caracteres de comprimento.',
    'not_equals' => 'O campo {field} não pode ser: {param}.',
    'not_in_list' => 'O campo {field} não deve ser um de: {param}.',
    'numeric' => 'O campo {field} deve conter apenas números.',
    'regex_match' => 'O campo {field} não está no formato correto.',
    'required' => 'O campo {field} é obrigatório.',
    'required_with' => 'O campo {field} é obrigatório quando {param} está presente.',
    'required_without' => 'O campo {field} é obrigatório quando {param} não está presente.',
    'string' => 'O campo {field} deve ser uma string válida.',
    'timezone' => 'O campo {field} deve ser um fuso horário válido.',
    'valid_base64' => 'O campo {field} deve ser uma string base64 válida.',
    'valid_email' => 'O campo {field} deve conter um endereço de e-mail válido.',
    'valid_emails' => 'O campo {field} deve conter todos os endereços de e-mail válidos.',
    'valid_ip' => 'O campo {field} deve conter um IP válido.',
    'valid_url' => 'O campo {field} deve conter uma URL válida.',
    'valid_date' => 'O campo {field} deve conter uma data válida.',

    // Credit Cards
    'valid_cc_num' => '{field} não parece ser um número de cartão de crédito válido.',

    // Files
    'uploaded' => '{field} não é um arquivo enviado válido.',
    'max_size' => '{field} é um arquivo muito grande.',
    'is_image' => '{field} não é um arquivo de imagem enviado válido.',
    'mime_in' => '{field} não tem um tipo mime válido.',
    'ext_in' => '{field} não tem uma extensão de arquivo válida.',
    'max_dims' => '{field} não é uma imagem, ou é muito largo ou muito alto.',

    // Custom
    'unique' => 'O {field} inserido já está registrado.',
    'boolean' => 'O campo {field} só pode estar marcado ou ignorado.',
    'currency' => 'O campo {field} só pode ser formatado como moeda.',
    'valid_year' => 'O campo {field} deve usar o formato de ano.',
    'valid_hex' => 'O campo {field} só pode conter um código de cor hex.'
];
