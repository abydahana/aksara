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
    'noRuleSets' => '検証設定にルールが指定されていません。',
    'ruleNotFound' => '{0} は有効なルールではありません。',
    'groupNotFound' => '{0} は検証ルールグループではありません。',
    'groupNotArray' => '{0} ルールグループは配列である必要があります。',
    'invalidTemplate' => '{0} は有効な検証テンプレートではありません。',

    // Rule Messages
    'alpha' => '{field} フィールドにはアルファベットのみを含めることができます。',
    'alpha_dash' => '{field} フィールドには英数字、アンダースコア、ダッシュのみを含めることができます。',
    'alpha_numeric' => '{field} フィールドには英数字のみを含めることができます。',
    'alpha_numeric_punct' => '{field} フィールドには英数字、スペース、および ~ ! # $ % & * - _ + = | : . などの文字のみを含めることができます。',
    'alpha_numeric_space' => '{field} フィールドには英数字とスペースのみを含めることができます。',
    'alpha_space' => '{field} フィールドにはアルファベットとスペースのみを含めることができます。',
    'decimal' => '{field} フィールドには小数が含まれている必要があります。',
    'differs' => '{field} フィールドは {param} フィールドと異なる必要があります。',
    'equals' => '{field} フィールドは {param} と完全に一致する必要があります。',
    'exact_length' => '{field} フィールドの長さはちょうど {param} 文字である必要があります。',
    'greater_than' => '{field} フィールドには {param} より大きい数値が含まれている必要があります。',
    'greater_than_equal_to' => '{field} フィールドには {param} 以上の数値が含まれている必要があります。',
    'hex' => '{field} フィールドには16進数の文字のみを含めることができます。',
    'in_list' => '{field} フィールドは次のいずれかである必要があります: {param}。',
    'integer' => '{field} フィールドには整数が含まれている必要があります。',
    'is_natural' => '{field} フィールドには数字のみを含めることができます。',
    'is_natural_no_zero' => '{field} フィールドには数字のみを含め、ゼロより大きい必要があります。',
    'is_not_unique' => '{field} フィールドには、データベース内にすでに存在する値が含まれている必要があります。',
    'is_unique' => '{field} フィールドには一意の値が含まれている必要があります。',
    'less_than' => '{field} フィールドには {param} 未満の数値が含まれている必要があります。',
    'less_than_equal_to' => '{field} フィールドには {param} 以下の数値が含まれている必要があります。',
    'matches' => '{field} フィールドが {param} フィールドと一致しません。',
    'max_length' => '{field} フィールドの長さは {param} 文字を超えることはできません。',
    'min_length' => '{field} フィールドの長さは少なくとも {param} 文字である必要があります。',
    'not_equals' => '{field} フィールドは {param} であってはなりません。',
    'not_in_list' => '{field} フィールドは次のいずれかであってはなりません: {param}。',
    'numeric' => '{field} フィールドには数字のみを含めることができます。',
    'regex_match' => '{field} フィールドの形式が正しくありません。',
    'required' => '{field} フィールドは必須です。',
    'required_with' => '{param} が存在する場合、{field} フィールドは必須です。',
    'required_without' => '{param} が存在しない場合、{field} フィールドは必須です。',
    'string' => '{field} フィールドは有効な文字列である必要があります。',
    'timezone' => '{field} フィールドは有効なタイムゾーンである必要があります。',
    'valid_base64' => '{field} フィールドは有効なbase64文字列である必要があります。',
    'valid_email' => '{field} フィールドには有効なメールアドレスが含まれている必要があります。',
    'valid_emails' => '{field} フィールドにはすべて有効なメールアドレスが含まれている必要があります。',
    'valid_ip' => '{field} フィールドには有効なIPが含まれている必要があります。',
    'valid_url' => '{field} フィールドには有効なURLが含まれている必要があります。',
    'valid_date' => '{field} フィールドには有効な日付が含まれている必要があります。',

    // Credit Cards
    'valid_cc_num' => '{field} は有効なクレジットカード番号ではないようです。',

    // Files
    'uploaded' => '{field} は有効なアップロードファイルではありません。',
    'max_size' => '{field} はファイルが大きすぎます。',
    'is_image' => '{field} は有効なアップロード画像ファイルではありません。',
    'mime_in' => '{field} に有効なMIMEタイプがありません。',
    'ext_in' => '{field} に有効なファイル拡張子がありません。',
    'max_dims' => '{field} は画像ではないか、幅または高さが大きすぎます。',

    // Custom
    'unique' => '入力された {field} はすでに登録されています。',
    'boolean' => '{field} フィールドはチェックするまたは無視するのみ可能です。',
    'currency' => '{field} フィールドは通貨としてのみフォーマットできます。',
    'valid_year' => '{field} フィールドは年フォーマットを使用する必要があります。',
    'valid_hex' => '{field} フィールドには16進数のカラーコードのみを含めることができます。'
];
