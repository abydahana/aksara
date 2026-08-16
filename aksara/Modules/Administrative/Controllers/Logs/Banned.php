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

namespace Aksara\Modules\Administrative\Controllers\Logs;

use DirectoryIterator;
use Throwable;
use Aksara\Laboratory\Core;

class Banned extends Core
{
    public function __construct()
    {
        parent::__construct();

        $this->restrictOnDemo();

        $this->setPermission();
        $this->setTheme('backend');
    }

    public function index()
    {
        $this->setTitle(phrase('Banned Logs'))
        ->setIcon('mdi mdi-shield-alert-outline')

        ->setOutput([
            'banned' => $this->_banned()
        ])

        ->render();
    }

    public function unban()
    {
        $this->permission->mustAjax();

        return $this->_toggle(false);
    }

    public function ban()
    {
        $this->permission->mustAjax();

        return $this->_toggle(true);
    }

    private function _banned()
    {
        $directory = $this->_directory();

        if (! is_dir($directory) || ! is_readable($directory)) {
            return [];
        }

        $banned = [];
        $minimumTimestamp = strtotime('-24 hours');

        foreach (new DirectoryIterator($directory) as $file) {
            if (! $file->isFile() || ! $file->isReadable() || $file->getMTime() < $minimumTimestamp) {
                continue;
            }

            $payload = json_decode(file_get_contents($file->getPathname()), true);

            if (! is_array($payload)) {
                continue;
            }

            $bannedAt = $payload['banned_at'] ?? null;

            if (! is_numeric($bannedAt)) {
                continue;
            }

            $expiresAt = $payload['expires_at'] ?? null;

            $banned[] = (object) [
                'file' => $file->getFilename(),
                'ip' => $payload['ip'] ?? '-',
                'uri' => $payload['uri'] ?? '-',
                'reason' => $payload['reason'] ?? '-',
                'attempts' => $payload['attempts'] ?? 0,
                'banned_at' => $bannedAt,
                'expires_at' => $expiresAt,
                'banned_at_label' => $this->_formatTimestamp($bannedAt),
                'expires_at_label' => $this->_formatTimestamp($expiresAt),
                'is_banned' => is_numeric($expiresAt) && $expiresAt > time()
            ];
        }

        usort($banned, function ($a, $b) {
            return ($b->banned_at ?? 0) <=> ($a->banned_at ?? 0);
        });

        return $banned;
    }

    private function _toggle(bool $ban)
    {
        $filename = basename($this->request->getGet('file') ?: $this->request->getPost('file'));

        if (! $filename) {
            return throw_exception(404, phrase('The selected banned request log was not found.'), go_to(null, ['file' => null]));
        }

        if (! $this->request->getPost('file')) {
            return $this->_confirm($filename, $ban);
        }

        $file = $this->_directory() . DIRECTORY_SEPARATOR . $filename;

        if (! is_file($file) || ! is_readable($file) || ! is_writable($file)) {
            return throw_exception(404, phrase('The selected banned request log was not found.'), go_to(null, ['file' => null]));
        }

        try {
            $payload = json_decode(file_get_contents($file), true);

            if (! is_array($payload)) {
                return throw_exception(403, phrase('Unable to update the selected banned request log.'), go_to(null, ['file' => null]));
            }

            $time = time();

            if ($ban) {
                $payload['banned_at'] = $time;
                $payload['expires_at'] = $time + $this->_banDuration();
            } else {
                $payload['expires_at'] = $time;
            }

            file_put_contents($file, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
        } catch (Throwable $e) {
            return throw_exception(403, phrase('Unable to update the selected banned request log.'), go_to(null, ['file' => null]));
        }

        return throw_exception(301, ($ban ? phrase('The selected request was successfully banned.') : phrase('The selected request was successfully unbanned.')), go_to(null, ['file' => null]));
    }

    private function _confirm(string $filename, bool $ban)
    {
        $action = $ban ? 'ban' : 'unban';
        $icon = $ban ? 'mdi-shield-alert-outline text-danger' : 'mdi-shield-check-outline text-success';
        $title = $ban ? phrase('Are you sure want to ban this request?') : phrase('Are you sure want to unban this request?');
        $description = $ban ? phrase('This action will update the ban time and release time.') : phrase('This action will update the release time to the current time.');
        $button = $ban ? phrase('Ban') : phrase('Unban');
        $buttonIcon = $ban ? 'mdi-shield-alert-outline' : 'mdi-shield-check-outline';

        $html = '
            <form action="' . go_to($action, ['file' => $filename]) . '" method="POST" class="--validate-form">
                <div class="text-center mb-3">
                    <i class="mdi ' . $icon . ' mdi-5x"></i>
                    <br />
                    <h5>
                        ' . $title . '
                    </h5>
                    <p class="fs-5">
                        ' . $description . '
                    </p>
                </div>
                <input type="hidden" name="file" value="' . htmlspecialchars($filename, ENT_QUOTES, 'UTF-8') . '" />
                <hr class="mx--3 border-secondary-subtle" />
                <div class="row">
                    <div class="col-6">
                        <div class="d-grid">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">
                                <i class="mdi mdi-window-close"></i>
                                ' . phrase('Cancel') . '
                            </button>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark btn-sm rounded-pill">
                                <i class="mdi ' . $buttonIcon . '"></i>
                                ' . $button . '
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        ';

        return make_json([
            'status' => 200,
            'meta' => [
                'popup' => true
            ],
            'content' => $html
        ]);
    }

    private function _formatTimestamp(?string $timestamp)
    {
        if (! $timestamp || ! is_numeric($timestamp)) {
            return '-';
        }

        return format_date($timestamp, 'long', true);
    }

    private function _directory()
    {
        return WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . 'banned';
    }

    private function _banDuration()
    {
        if (! defined('INVALID_URI_BAN_DURATION')) {
            return 900;
        }

        return max(1, (int) constant('INVALID_URI_BAN_DURATION'));
    }
}
