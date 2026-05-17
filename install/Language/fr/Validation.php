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
    'noRuleSets' => 'Aucun ensemble de règles spécifié dans la configuration de Validation.',
    'ruleNotFound' => '{0} n\'est pas une règle valide.',
    'groupNotFound' => '{0} n\'est pas un groupe de règles de validation.',
    'groupNotArray' => 'Le groupe de règles {0} doit être un tableau.',
    'invalidTemplate' => '{0} n\'est pas un modèle de Validation valide.',

    // Rule Messages
    'alpha' => 'Le champ {field} ne peut contenir que des caractères alphabétiques.',
    'alpha_dash' => 'Le champ {field} ne peut contenir que des caractères alphanumériques, des traits de soulignement et des tirets.',
    'alpha_numeric' => 'Le champ {field} ne peut contenir que des caractères alphanumériques.',
    'alpha_numeric_punct' => 'Le champ {field} ne peut contenir que des caractères alphanumériques, des espaces et les caractères ~ ! # $ % & * - _ + = | : .',
    'alpha_numeric_space' => 'Le champ {field} ne peut contenir que des caractères alphanumériques et des espaces.',
    'alpha_space' => 'Le champ {field} ne peut contenir que des caractères alphabétiques et des espaces.',
    'decimal' => 'Le champ {field} doit contenir un nombre décimal.',
    'differs' => 'Le champ {field} doit être différent du champ {param}.',
    'equals' => 'Le champ {field} doit être exactement : {param}.',
    'exact_length' => 'Le champ {field} doit avoir une longueur exacte de {param} caractères.',
    'greater_than' => 'Le champ {field} doit contenir un nombre supérieur à {param}.',
    'greater_than_equal_to' => 'Le champ {field} doit contenir un nombre supérieur ou égal à {param}.',
    'hex' => 'Le champ {field} ne peut contenir que des caractères hexadécimaux.',
    'in_list' => 'Le champ {field} doit être l\'un des suivants : {param}.',
    'integer' => 'Le champ {field} doit contenir un entier.',
    'is_natural' => 'Le champ {field} ne doit contenir que des chiffres.',
    'is_natural_no_zero' => 'Le champ {field} ne doit contenir que des chiffres et doit être supérieur à zéro.',
    'is_not_unique' => 'Le champ {field} doit contenir une valeur déjà existante dans la base de données.',
    'is_unique' => 'Le champ {field} doit contenir une valeur unique.',
    'less_than' => 'Le champ {field} doit contenir un nombre inférieur à {param}.',
    'less_than_equal_to' => 'Le champ {field} doit contenir un nombre inférieur ou égal à {param}.',
    'matches' => 'Le champ {field} ne correspond pas au champ {param}.',
    'max_length' => 'Le champ {field} ne peut pas dépasser {param} caractères.',
    'min_length' => 'Le champ {field} doit comporter au moins {param} caractères.',
    'not_equals' => 'Le champ {field} ne peut pas être : {param}.',
    'not_in_list' => 'Le champ {field} ne doit pas faire partie de : {param}.',
    'numeric' => 'Le champ {field} doit contenir uniquement des nombres.',
    'regex_match' => 'Le champ {field} n\'est pas au bon format.',
    'required' => 'Le champ {field} est requis.',
    'required_with' => 'Le champ {field} est requis lorsque {param} est présent.',
    'required_without' => 'Le champ {field} est requis lorsque {param} n\'est pas présent.',
    'string' => 'Le champ {field} doit être une chaîne valide.',
    'timezone' => 'Le champ {field} doit être un fuseau horaire valide.',
    'valid_base64' => 'Le champ {field} doit être une chaîne base64 valide.',
    'valid_email' => 'Le champ {field} doit contenir une adresse e-mail valide.',
    'valid_emails' => 'Le champ {field} doit contenir toutes les adresses e-mail valides.',
    'valid_ip' => 'Le champ {field} doit contenir une adresse IP valide.',
    'valid_url' => 'Le champ {field} doit contenir une URL valide.',
    'valid_date' => 'Le champ {field} doit contenir une date valide.',

    // Credit Cards
    'valid_cc_num' => '{field} ne semble pas être un numéro de carte de crédit valide.',

    // Files
    'uploaded' => '{field} n\'est pas un fichier téléchargé valide.',
    'max_size' => '{field} est un fichier trop volumineux.',
    'is_image' => '{field} n\'est pas un fichier image téléchargé valide.',
    'mime_in' => '{field} n\'a pas un type mime valide.',
    'ext_in' => '{field} n\'a pas une extension de fichier valide.',
    'max_dims' => '{field} n\'est pas une image, ou est trop large ou trop haute.',

    // Custom
    'unique' => 'Le {field} saisi est déjà enregistré.',
    'boolean' => 'Le champ {field} ne peut être que coché ou ignoré.',
    'currency' => 'Le champ {field} ne peut être formaté qu\'en devise.',
    'valid_year' => 'Le champ {field} doit utiliser le format de l\'année.',
    'valid_hex' => 'Le champ {field} ne peut contenir qu\'un code de couleur hexadécimal.'
];
