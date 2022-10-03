# About Aksara
Aksara is a CodeIgniter based CRUD Toolkit you can use to build complex applications become shorter, secure and more reliable just in a few lines of code. Serving both CMS or Framework, produce both HEADLESS (RESTful API) or TRADITIONAL (Browser Based) just by writing single controller. Yet it's reusable, scalable and ready to use!

# Server Requirements
PHP version 7.4 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [libcurl](http://php.net/manual/en/curl.requirements.php) to connect with **[Aksara Market](http://www.aksaracms.com/market)**
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- xml (enabled by default - don't turn it off)

**[Aksara](http://www.aksaracms.com)** can be run under the **MySQLi**, **PostgreSQL**, **SQL Server** and **SQLite3** or **BOTH OF IT** without changing any single code. The installer will guide you well when picking up the database type during installation, so follow the step carefully and make sure you reads every highlighted notes.

# Installation
There are two installation methods that you can choose:
### Composer Installation
- Run "`composer create-project abydahana/aksara aksaracms`" anywhere inside your root directory of your web server. The command will create "`aksaracms`" folder. If you omit the "`aksaracms`" argument, the command will create an "`aksara`" folder instead, which can be renamed as appropriate;
- Access your project from the browser and;
- Follow the installation wizard.

### Manual Installation
- Download the source code and extract its content to the directory of your webserver;
- Run "`composer install`" from the root of Aksara project directory;
- Access your project from the browser and;
- Follow the installation wizard.

### Installation with Docker
- clone source code with git clone "`https://github.com/abydahana/aksara.git`";
- Run "`docker-compose up -d --build`" from the root of Aksara project directory;
- Access root of Aksara project directory from terminal and run "`docker-compose exec aksara composer install`";
- Access your project from the browser and;
- Follow the installation wizard.

**Yes, as simple as that!**

# Some Screenshot
| ![frame_generic_light](https://user-images.githubusercontent.com/10624446/110242393-729b6b00-7f88-11eb-9ecc-2cb1c27c5945.png) | ![frame_generic_light (1)](https://user-images.githubusercontent.com/10624446/110242375-67483f80-7f88-11eb-8126-fba2051ae95b.png) | ![frame_generic_light (2)](https://user-images.githubusercontent.com/10624446/110242377-69120300-7f88-11eb-95ff-9e8b002c51be.png) |
| :---: | :---: | :---: |
| ![frame_generic_light (3)](https://user-images.githubusercontent.com/10624446/110242379-6a433000-7f88-11eb-9510-31eb17ea1613.png) | ![frame_generic_light (4)](https://user-images.githubusercontent.com/10624446/110242381-6b745d00-7f88-11eb-9120-53a464c46b34.png) | ![frame_generic_light (5)](https://user-images.githubusercontent.com/10624446/110242382-6c0cf380-7f88-11eb-977d-1b89624a0efb.png) |
| ![frame_generic_light (6)](https://user-images.githubusercontent.com/10624446/110242384-6ca58a00-7f88-11eb-9992-e90779dd2eeb.png) | ![frame_generic_light (7)](https://user-images.githubusercontent.com/10624446/110242386-6d3e2080-7f88-11eb-9e6e-de40f620feb8.png) | ![frame_generic_light (8)](https://user-images.githubusercontent.com/10624446/110242387-6e6f4d80-7f88-11eb-8c81-3e505eb2fd1d.png) |
| ![frame_generic_light (9)](https://user-images.githubusercontent.com/10624446/110242389-6f07e400-7f88-11eb-9089-5cd5cc3f2ec2.png) | ![frame_generic_light (10)](https://user-images.githubusercontent.com/10624446/110242390-70391100-7f88-11eb-9734-a20b9e9005eb.png) | ![frame_generic_light (11)](https://user-images.githubusercontent.com/10624446/110242391-70d1a780-7f88-11eb-8d04-69128749b6e0.png) |
| ![frame_generic_light (12)](https://user-images.githubusercontent.com/10624446/110242392-7202d480-7f88-11eb-8f23-6c1c3edf9ea2.png) | * | * |
