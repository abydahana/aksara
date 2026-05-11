# Aksara CMS Configuration Files

The files in this directory contain specific adjustments required for Aksara CMS to function correctly.

**IMPORTANT**: When updating CodeIgniter to a newer version, **do not blindly overwrite** these configuration files. You must carefully merge any new CodeIgniter changes with the existing Aksara customizations listed below.

Here is the breakdown of files and their modifications:

- **App.php**: Overrides `$baseURL` to use the `BASE_URL` constant; changes `$permittedURIChars` to `'a-z 0-9~%.:_\-{}'`.
- **Autoload.php**: Adds to `$psr4` array: `'Aksara' => APPPATH`, `'Modules' => ROOTPATH . 'modules'`.
- **Constants.php**: Sets `APP_NAMESPACE` to `'Aksara'`.
- **Database.php**: Modifies `$default` array to use constants like `DB_DSN`, `DB_HOSTNAME`, etc.
- **Events.php**: Modifies the 'pre_system' event to comment out the zlib output compression exception throw and return true instead.
- **Mimes.php**: Modifies `$mimes['geojson']` array to include geojson mime types.
- **Pager.php**: Adds to `$templates` array: `'pagination' => 'Aksara\Views\templates\pagination'`.
- **Paths.php**: Changes `$systemDirectory` to `__DIR__ . '/../../vendor/codeigniter4/framework/system'`.
- **Routes.php**: Sets default namespace and controller based on file existence; uses `Aksara\Laboratory\Router`.
- **Session.php**: Changes `$cookieName` to `COOKIE_NAME` constant.
- **Validation.php**: Adds to `$ruleSets` array: `Aksara\Laboratory\Validation::class`.
