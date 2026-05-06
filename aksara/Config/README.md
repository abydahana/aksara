# Aksara Configuration Files

The files in this directory contain specific adjustments required for Aksara CMS to function properly on top of the CodeIgniter 4 framework. 

**IMPORTANT**: When updating CodeIgniter to a newer version, **do not blindly overwrite** these configuration files. You must carefully merge any new CodeIgniter changes with the existing Aksara customizations listed below.

Here is the breakdown of files and their modifications:

- **App.php**: Overrides `$baseURL`, `$appTimezone`, and other core properties to use Aksara's global constants (`BASE_URL`, `TIMEZONE`, etc.).
- **Autoload.php**: Maps the `Aksara` namespace to `APPPATH` and `Modules` to `ROOTPATH . 'modules'` in the `$psr4` array to enable the HMVC modular architecture.
- **Constants.php**: Defines environment fallbacks and custom constants (e.g., `BASE_URL`, `DB_DSN`, `UPLOAD_PATH`, `TIMEZONE`) required by the application and installer.
- **Cors.php**: Contains specific Cross-Origin Resource Sharing (CORS) rules tailored for Aksara APIs.
- **Database.php**: Alters the default database connections to dynamically read from `.env` based constants (`DB_DRIVER`, `DB_HOSTNAME`, `DB_USERNAME`, etc.).
- **Events.php**: May contain custom pre-system or post-system event hooks used by the CMS.
- **Filters.php**: Registers global aliases and filter rules, such as adding exceptions to skip the debug toolbar for `themes/*` and `assets/*`.
- **Logger.php**: Contains custom log threshold and handler configurations.
- **Mimes.php**: Extends the default allowed MIME types to support additional file uploads required by the CMS.
- **Pager.php**: Overrides default pagination templates to use Aksara's UI components.
- **Paths.php**: Adjusts core directory paths (`$systemDirectory`, `$appDirectory`, `$writableDirectory`) to align with Aksara's custom folder structure relative to the `vendor` directory.
- **Routes.php**: Integrates dynamic directory-based module routing (`Aksara\Laboratory\Router`) and overrides default namespace and 404 behavior.
- **Security.php**: Configures CSRF protection settings to utilize Aksara's `COOKIE_NAME` constant.
- **Session.php**: Modifies session storage properties like `$cookieName` and `$expiration` to use Aksara's `COOKIE_NAME` and `SESSION_EXPIRATION` constants.
- **Validation.php**: Registers custom validation rules and UI error templates used throughout Aksara forms.