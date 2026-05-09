# Aksara Installer Configuration Files

The files in this directory contain specific adjustments required for the Aksara CMS Installer module to function correctly. 

**IMPORTANT**: When updating CodeIgniter to a newer version, **do not blindly overwrite** these configuration files. You must carefully merge any new CodeIgniter changes with the existing Aksara customizations listed below.

Here is the breakdown of files and their modifications:

- **App.php**: Overrides `$baseURL` to use the dynamically defined `BASE_URL` constant, ensuring the installer runs correctly regardless of the directory path it's placed in.
- **Paths.php**: Adjusts core directory paths (`$systemDirectory`, `$appDirectory`, `$writableDirectory`, `$envDirectory`) to point to the correct locations. Specifically, `$systemDirectory` points to the `vendor` directory and `$appDirectory` is set to the `install` folder instead of the main `aksara` application folder.
- **Routes.php**: Redirects the root URL `/` to the `Install::index` method, making the installer the default entry point for the application.
