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

class AksaraPrivileges extends Seeder
{
    public function run()
    {
        $this->_syncPrivilegeCatalog();
        $this->_syncGroupPrivileges();
    }

    private function _syncPrivilegeCatalog(): void
    {
        $this->_upsertPrivilegeCatalog('administrative/account', ['index', 'update']);
        $this->_upsertPrivilegeCatalog('administrative/account/passkey', ['index', 'register', 'verify', 'delete']);
        $this->_upsertPrivilegeCatalog('administrative/cleaner', ['index', 'session', 'cache']);
        $this->_upsertPrivilegeCatalog('administrative/updater', ['index', 'update', 'upload', 'migrate']);
        $this->_upsertPrivilegeCatalog('apis/debug', ['index'], 'apis/debug_tool');
        $this->_upsertPrivilegeCatalog('cms/pages', ['index', 'create', 'read', 'update', 'delete', 'export', 'print', 'pdf', 'translate', 'preview']);
        $this->_upsertPrivilegeCatalog('xhr/uploader', ['index', 'upload', 'delete'], 'xhr/summernote');
    }

    private function _upsertPrivilegeCatalog(string $path, array $privileges, ?string $oldPath = null): void
    {
        $data = [
            'path' => $path,
            'privileges' => json_encode($privileges),
            'last_generated' => date('Y-m-d H:i:s')
        ];

        $exists = $this->db->table('app_groups_privileges')
            ->where('path', $path)
            ->countAllResults();

        if ($exists) {
            $this->db->table('app_groups_privileges')
                ->where('path', $path)
                ->update($data);

            if ($oldPath) {
                $this->db->table('app_groups_privileges')
                    ->where('path', $oldPath)
                    ->delete();
            }

            return;
        }

        if ($oldPath) {
            $oldExists = $this->db->table('app_groups_privileges')
                ->where('path', $oldPath)
                ->countAllResults();

            if ($oldExists) {
                $this->db->table('app_groups_privileges')
                    ->where('path', $oldPath)
                    ->update($data);

                return;
            }
        }

        $this->db->table('app_groups_privileges')->insert($data);
    }

    private function _syncGroupPrivileges(): void
    {
        $groups = $this->db->table('app_groups')
            ->select('group_id, group_name, group_privileges')
            ->get()
            ->getResultArray();

        foreach ($groups as $group) {
            $privileges = json_decode((string) $group['group_privileges'], true);

            if (! is_array($privileges)) {
                continue;
            }

            $original = $privileges;

            $this->_renamePrivilegePath($privileges, 'apis/debug_tool', 'apis/debug');
            $this->_renamePrivilegePath($privileges, 'xhr/summernote', 'xhr/uploader');

            if (isset($privileges['administrative/account'])) {
                $this->_replacePrivilege($privileges['administrative/account'], 'edit', 'update');
                $this->_setPrivileges($privileges, 'administrative/account/passkey', ['index', 'register', 'verify', 'delete']);
            }

            if (isset($privileges['administrative/cleaner'])) {
                $hadClean = in_array('clean', $privileges['administrative/cleaner'], true);

                $this->_removePrivileges($privileges['administrative/cleaner'], ['clean']);

                if ($hadClean) {
                    $this->_addPrivileges($privileges['administrative/cleaner'], ['session', 'cache']);
                }
            }

            if (isset($privileges['administrative/updater']) && in_array('update', $privileges['administrative/updater'], true)) {
                $this->_addPrivileges($privileges['administrative/updater'], ['upload', 'migrate']);
            }

            if (isset($privileges['administrative/logs/activities'])) {
                $this->_addPrivileges($privileges['administrative/logs/activities'], ['export']);
            }

            if (isset($privileges['apis/debug'])) {
                $this->_setPrivileges($privileges, 'apis/debug', ['index']);
            }

            if (isset($privileges['cms/pages'])) {
                $builderPrivileges = ['builder-preview', 'builder-images', 'builder-upload', 'builder-delete'];
                $hadBuilderPrivileges = (bool) array_intersect($builderPrivileges, $privileges['cms/pages']);

                $this->_removePrivileges($privileges['cms/pages'], $builderPrivileges);

                if ($hadBuilderPrivileges) {
                    $this->_addPrivileges($privileges['cms/pages'], ['preview']);
                }
            }

            if (isset($privileges['xhr/uploader'])) {
                $this->_setPrivileges($privileges, 'xhr/uploader', ['index', 'upload', 'delete']);
            }

            if ('Subscriber' === $group['group_name']) {
                $this->_setPrivileges($privileges, 'xhr/uploader', ['index', 'upload', 'delete']);
            }

            if ($original === $privileges) {
                continue;
            }

            $this->db->table('app_groups')
                ->where('group_id', $group['group_id'])
                ->update([
                    'group_privileges' => json_encode($privileges, JSON_UNESCAPED_SLASHES)
                ]);
        }
    }

    private function _renamePrivilegePath(array &$privileges, string $oldPath, string $newPath): void
    {
        if (! isset($privileges[$oldPath])) {
            return;
        }

        $newPrivileges = $privileges[$newPath] ?? [];
        $privileges[$newPath] = $this->_uniquePrivileges(array_merge($newPrivileges, $privileges[$oldPath]));

        unset($privileges[$oldPath]);
    }

    private function _setPrivileges(array &$privileges, string $path, array $methods): void
    {
        $privileges[$path] = $this->_uniquePrivileges($methods);
    }

    private function _addPrivileges(array &$privileges, array $methods): void
    {
        $privileges = $this->_uniquePrivileges(array_merge($privileges, $methods));
    }

    private function _removePrivileges(array &$privileges, array $methods): void
    {
        $privileges = array_values(array_diff($privileges, $methods));
    }

    private function _replacePrivilege(array &$privileges, string $oldMethod, string $newMethod): void
    {
        if (! in_array($oldMethod, $privileges, true)) {
            return;
        }

        $this->_removePrivileges($privileges, [$oldMethod]);
        $this->_addPrivileges($privileges, [$newMethod]);
    }

    private function _uniquePrivileges(array $privileges): array
    {
        return array_values(array_unique($privileges));
    }
}
