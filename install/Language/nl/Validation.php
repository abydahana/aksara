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
    'noRuleSets' => 'Geen regelsets gespecificeerd in de Validatie configuratie.',
    'ruleNotFound' => '{0} is geen geldige regel.',
    'groupNotFound' => '{0} is geen validatieregelgroep.',
    'groupNotArray' => '{0} regelgroep moet een array zijn.',
    'invalidTemplate' => '{0} is geen geldige Validatie template.',

    // Rule Messages
    'alpha' => 'Het {field} veld mag alleen alfabetische tekens bevatten.',
    'alpha_dash' => 'Het {field} veld mag alleen alfanumerieke tekens, underscores en streepjes bevatten.',
    'alpha_numeric' => 'Het {field} veld mag alleen alfanumerieke tekens bevatten.',
    'alpha_numeric_punct' => 'Het {field} veld mag alleen alfanumerieke tekens, spaties en  ~ ! # $ % & * - _ + = | : . tekens bevatten.',
    'alpha_numeric_space' => 'Het {field} veld mag alleen alfanumerieke tekens en spaties bevatten.',
    'alpha_space' => 'Het {field} veld mag alleen alfabetische tekens en spaties bevatten.',
    'decimal' => 'Het {field} veld moet een decimaal getal bevatten.',
    'differs' => 'Het {field} veld moet verschillen van het {param} veld.',
    'equals' => 'Het {field} veld moet exact zijn: {param}.',
    'exact_length' => 'Het {field} veld moet exact {param} tekens lang zijn.',
    'greater_than' => 'Het {field} veld moet een getal bevatten groter dan {param}.',
    'greater_than_equal_to' => 'Het {field} veld moet een getal bevatten groter dan of gelijk aan {param}.',
    'hex' => 'Het {field} veld mag alleen hexadecimale tekens bevatten.',
    'in_list' => 'Het {field} veld moet een van de volgende zijn: {param}.',
    'integer' => 'Het {field} veld moet een geheel getal bevatten.',
    'is_natural' => 'Het {field} veld mag alleen cijfers bevatten.',
    'is_natural_no_zero' => 'Het {field} veld mag alleen cijfers bevatten en moet groter zijn dan nul.',
    'is_not_unique' => 'Het {field} veld moet een eerder bestaande waarde in de database bevatten.',
    'is_unique' => 'Het {field} veld moet een unieke waarde bevatten.',
    'less_than' => 'Het {field} veld moet een getal bevatten kleiner dan {param}.',
    'less_than_equal_to' => 'Het {field} veld moet een getal bevatten kleiner dan of gelijk aan {param}.',
    'matches' => 'Het {field} veld komt niet overeen met het {param} veld.',
    'max_length' => 'Het {field} veld kan niet groter zijn dan {param} tekens.',
    'min_length' => 'Het {field} veld moet minstens {param} tekens lang zijn.',
    'not_equals' => 'Het {field} veld kan niet zijn: {param}.',
    'not_in_list' => 'Het {field} veld mag geen van de volgende zijn: {param}.',
    'numeric' => 'Het {field} veld mag alleen cijfers bevatten.',
    'regex_match' => 'Het {field} veld heeft niet het juiste formaat.',
    'required' => 'Het {field} veld is verplicht.',
    'required_with' => 'Het {field} veld is verplicht wanneer {param} aanwezig is.',
    'required_without' => 'Het {field} veld is verplicht wanneer {param} niet aanwezig is.',
    'string' => 'Het {field} veld moet een geldige string zijn.',
    'timezone' => 'Het {field} veld moet een geldige tijdzone zijn.',
    'valid_base64' => 'Het {field} veld moet een geldige base64 string zijn.',
    'valid_email' => 'Het {field} veld moet een geldig e-mailadres bevatten.',
    'valid_emails' => 'Het {field} veld moet allemaal geldige e-mailadressen bevatten.',
    'valid_ip' => 'Het {field} veld moet een geldig IP bevatten.',
    'valid_url' => 'Het {field} veld moet een geldige URL bevatten.',
    'valid_date' => 'Het {field} veld moet een geldige datum bevatten.',

    // Credit Cards
    'valid_cc_num' => '{field} lijkt geen geldig creditcardnummer te zijn.',

    // Files
    'uploaded' => '{field} is geen geldig geüpload bestand.',
    'max_size' => '{field} is een te groot bestand.',
    'is_image' => '{field} is geen geldig geüpload afbeeldingsbestand.',
    'mime_in' => '{field} heeft geen geldig mime-type.',
    'ext_in' => '{field} heeft geen geldige bestandsextensie.',
    'max_dims' => '{field} is geen afbeelding, of is te breed of te hoog.',

    // Custom
    'unique' => 'Het ingevoerde {field} is al geregistreerd.',
    'boolean' => 'Het {field} veld kan alleen worden aangevinkt of genegeerd.',
    'currency' => 'Het {field} veld kan alleen als valuta worden opgemaakt.',
    'valid_year' => 'Het {field} veld moet het jaartal formaat gebruiken.',
    'valid_hex' => 'Het {field} veld mag alleen een hexadecimale kleurcode bevatten.'
];
