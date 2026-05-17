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
    'noRuleSets' => 'Keine Regelsätze in der Validierungskonfiguration angegeben.',
    'ruleNotFound' => '{0} ist keine gültige Regel.',
    'groupNotFound' => '{0} ist keine Validierungsregelgruppe.',
    'groupNotArray' => 'Die Regelgruppe {0} muss ein Array sein.',
    'invalidTemplate' => '{0} ist keine gültige Validierungsvorlage.',

    // Rule Messages
    'alpha' => 'Das Feld {field} darf nur alphabetische Zeichen enthalten.',
    'alpha_dash' => 'Das Feld {field} darf nur alphanumerische Zeichen, Unterstriche und Bindestriche enthalten.',
    'alpha_numeric' => 'Das Feld {field} darf nur alphanumerische Zeichen enthalten.',
    'alpha_numeric_punct' => 'Das Feld {field} darf nur alphanumerische Zeichen, Leerzeichen und ~ ! # $ % & * - _ + = | : . enthalten.',
    'alpha_numeric_space' => 'Das Feld {field} darf nur alphanumerische Zeichen und Leerzeichen enthalten.',
    'alpha_space' => 'Das Feld {field} darf nur alphabetische Zeichen und Leerzeichen enthalten.',
    'decimal' => 'Das Feld {field} muss eine Dezimalzahl enthalten.',
    'differs' => 'Das Feld {field} muss sich vom Feld {param} unterscheiden.',
    'equals' => 'Das Feld {field} muss genau {param} entsprechen.',
    'exact_length' => 'Das Feld {field} muss genau {param} Zeichen lang sein.',
    'greater_than' => 'Das Feld {field} muss eine Zahl größer als {param} enthalten.',
    'greater_than_equal_to' => 'Das Feld {field} muss eine Zahl größer oder gleich {param} enthalten.',
    'hex' => 'Das Feld {field} darf nur hexadezimale Zeichen enthalten.',
    'in_list' => 'Das Feld {field} muss eines der folgenden sein: {param}.',
    'integer' => 'Das Feld {field} muss eine Ganzzahl enthalten.',
    'is_natural' => 'Das Feld {field} darf nur Ziffern enthalten.',
    'is_natural_no_zero' => 'Das Feld {field} darf nur Ziffern enthalten und muss größer als null sein.',
    'is_not_unique' => 'Das Feld {field} muss einen zuvor vorhandenen Wert in der Datenbank enthalten.',
    'is_unique' => 'Das Feld {field} muss einen eindeutigen Wert enthalten.',
    'less_than' => 'Das Feld {field} muss eine Zahl kleiner als {param} enthalten.',
    'less_than_equal_to' => 'Das Feld {field} muss eine Zahl kleiner oder gleich {param} enthalten.',
    'matches' => 'Das Feld {field} stimmt nicht mit dem Feld {param} überein.',
    'max_length' => 'Das Feld {field} darf {param} Zeichen nicht überschreiten.',
    'min_length' => 'Das Feld {field} muss mindestens {param} Zeichen lang sein.',
    'not_equals' => 'Das Feld {field} darf nicht {param} sein.',
    'not_in_list' => 'Das Feld {field} darf keines der folgenden sein: {param}.',
    'numeric' => 'Das Feld {field} darf nur Zahlen enthalten.',
    'regex_match' => 'Das Feld {field} ist nicht im richtigen Format.',
    'required' => 'Das Feld {field} ist erforderlich.',
    'required_with' => 'Das Feld {field} ist erforderlich, wenn {param} vorhanden ist.',
    'required_without' => 'Das Feld {field} ist erforderlich, wenn {param} nicht vorhanden ist.',
    'string' => 'Das Feld {field} muss ein gültiger String sein.',
    'timezone' => 'Das Feld {field} muss eine gültige Zeitzone sein.',
    'valid_base64' => 'Das Feld {field} muss ein gültiger Base64-String sein.',
    'valid_email' => 'Das Feld {field} muss eine gültige E-Mail-Adresse enthalten.',
    'valid_emails' => 'Das Feld {field} muss alle gültigen E-Mail-Adressen enthalten.',
    'valid_ip' => 'Das Feld {field} muss eine gültige IP-Adresse enthalten.',
    'valid_url' => 'Das Feld {field} muss eine gültige URL enthalten.',
    'valid_date' => 'Das Feld {field} muss ein gültiges Datum enthalten.',

    // Credit Cards
    'valid_cc_num' => '{field} scheint keine gültige Kreditkartennummer zu sein.',

    // Files
    'uploaded' => '{field} ist keine gültige hochgeladene Datei.',
    'max_size' => '{field} ist eine zu große Datei.',
    'is_image' => '{field} ist keine gültige hochgeladene Bilddatei.',
    'mime_in' => '{field} hat keinen gültigen MIME-Typ.',
    'ext_in' => '{field} hat keine gültige Dateiendung.',
    'max_dims' => '{field} ist entweder kein Bild, oder es ist zu breit oder zu hoch.',

    // Custom
    'unique' => 'Das eingegebene {field} ist bereits registriert.',
    'boolean' => 'Das Feld {field} kann nur angekreuzt oder ignoriert werden.',
    'currency' => 'Das Feld {field} kann nur als Währung formatiert werden.',
    'valid_year' => 'Das Feld {field} muss das Jahresformat verwenden.',
    'valid_hex' => 'Das Feld {field} darf nur einen Hex-Farbcode enthalten.'
];
