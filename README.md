# About Aksara WebGIS
**Aksara WebGIS** is a powerful Geographic Information System application built on top of [Aksara CMS](https://aksaracms.com). It provides a comprehensive platform for rendering, managing, and editing spatial data (including MVT vector tiles) directly from your browser. 

**Aksara WebGIS** supports multi-database connections including **MySQL/MariaDB**, **PostgreSQL (PostGIS)**, **SQLite3 (SpatiaLite)**, **SQL Server (MSSQL)**, and **Oracle (OCI8)**, allowing you to seamlessly integrate with your existing spatial infrastructure.

# Server Requirements
PHP version 8.1 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)
- [libcurl](http://php.net/manual/en/curl.requirements.php)

Additionally, make sure that the following extensions are enabled in your PHP:

- json
- xml

## Spatial Database Extensions
To enable spatial and geometric capabilities in **Aksara WebGIS**, your database server **must** have spatial extensions installed. Below is the installation guide for the supported databases across various platforms:

### 1. MySQL / MariaDB
MySQL (version 5.7.6+) and MariaDB (version 10.1+) both feature native spatial data extensions built directly into their core engines. They support standard OpenGIS geometry types like `GEOMETRY`, `POINT`, `LINESTRING`, and `POLYGON` out-of-the-box. No additional library installation is required!

### 2. PostgreSQL (PostGIS)
PostGIS adds support for geographic objects to the PostgreSQL object-relational database.
- **Ubuntu/Debian Linux:**
  ```bash
  sudo apt update
  sudo apt install postgresql postgis
  ```
- **macOS (via Homebrew):**
  ```bash
  brew install postgresql postgis
  ```
- **Windows:**
  Download the PostgreSQL installer from EnterpriseDB. During installation, launch the **Stack Builder** utility, select your PostgreSQL installation, and under the "Spatial Extensions" category, check and install **PostGIS**.

*Note: Once installed, don't forget to enable it in your database by running the SQL query: `CREATE EXTENSION postgis;`*

### 3. SQLite3 (SpatiaLite)
SpatiaLite is an open source library intended to extend the SQLite core to support fully fledged Spatial SQL capabilities.
- **Ubuntu/Debian Linux:**
  ```bash
  sudo apt update
  sudo apt install sqlite3 libsqlite3-mod-spatialite
  ```
- **macOS (via Homebrew):**
  ```bash
  brew install sqlite3 libspatialite
  ```
  *(Note: Due to PHP's strict extension path resolution, you **must** configure `sqlite3.extension_dir` in your `php.ini` to point to `/opt/homebrew/lib` (or your Brew path). Furthermore, since Homebrew creates a symlink for the library, you must replace the symlink with the actual file by running `rm /opt/homebrew/lib/mod_spatialite.dylib && cp /opt/homebrew/Cellar/libspatialite/*/lib/mod_spatialite.dylib /opt/homebrew/lib/mod_spatialite.dylib`. Finally, restart your PHP/Apache service).*
- **Windows:**
  1. Download the `mod_spatialite` pre-compiled binaries from the [SpatiaLite website](https://www.gaia-gis.it/fossil/libspatialite/index).
  2. Extract the `.dll` files into a directory included in your system's PATH, or directly into your PHP extension directory.
  3. Ensure the extension can be loaded by SQLite.

### 4. Microsoft SQL Server
SQL Server comes with native support for Spatial Data Types (`geometry` and `geography`) starting from SQL Server 2008. No additional installation or extension is required!

### 5. Oracle (OCI8)
Oracle databases provide spatial features natively through **Oracle Locator** (available in all editions by default) and **Oracle Spatial and Graph** (for advanced routing and 3D features). You do not need to install external extensions; simply ensure your database is provisioned with Oracle Spatial components enabled.

# Installation
There are two installation methods you can choose:
### Composer Installation
- Run `composer create-project abydahana/webgis` anywhere inside your root directory of your web server. 
- Access your project from the browser.
- Follow the installation wizard. Ensure you select a database type that supports spatial features.

### Manual Installation
- Download the source code and extract its content to the directory of your webserver.
- Run `composer install` from the root of the project directory.
- Access your project from the browser and follow the installation wizard.

# Contributing
We welcome contributions to the **Aksara WebGIS** project! Whether you can code, write documentation, or help find bugs, all contributions are welcome.
