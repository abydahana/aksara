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

namespace Aksara\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AksaraPublicKey extends Seeder
{
    private const PUBLIC_KEY = <<<'PEM'
        -----BEGIN PUBLIC KEY-----
        MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuQ+y3NnoMgJ2PlhmYwFD
        3ZFWSmqp4Qi0/UQOws3CMzViKeLDXxpzg9REni5Y8RJycxDMqql0honSiGL9vxTv
        TXKKxhhot4qHXPx6DDvlq7j4SWDh+KYrGDnbgCzOUazuJdSxwshR5jAkA2ozl35Z
        yIu/rqiKipYfdoo+HF2HrMcE71+QB3WDnJY6/v75s02MchTAKoAeiQ5YZBg229yG
        AaqsoQ8BiEcmw26ovKyQej2DR5rwN948LU1Xc7ISb3c+3d5fcvFvtGKH+0k6qQGg
        p95DIdJC5/DprIOTCmD0WD3iNg9c6tiEReY6+y/3yu0nCPzBf7WBGvw32izWqVDO
        XwIDAQAB
        -----END PUBLIC KEY-----
        PEM;

    public function run()
    {
        $data = [
            'key' => 'aksara_public_key',
            'type' => 'text',
            'value' => self::PUBLIC_KEY
        ];

        if ($this->db->table('app_settings')->where('key', $data['key'])->countAllResults()) {
            $this->db->table('app_settings')
                ->where('key', $data['key'])
                ->update($data);
        } else {
            $this->db->table('app_settings')->insert($data);
        }

        service('cache')->delete('aksara_app_settings');
    }
}
