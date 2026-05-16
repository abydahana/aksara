<img width="2982" height="1978" alt="frame_generic_dark" src="https://github.com/user-attachments/assets/e449a43f-2f42-4bd1-96fb-619e98c5779e" />

# About Aksara CMS
**Aksara CMS** is a powerful, lightweight, and versatile Content Management System built on top of [CodeIgniter 4](https://codeigniter.com). It provides a robust and comprehensive platform for building everything from simple company profiles to complex web applications and REST APIs seamlessly.

With its modular architecture, developers can easily extend the core functionality without touching the system files. **Aksara CMS** supports multi-database connections out of the box, including **MySQL/MariaDB**, **PostgreSQL**, **SQLite3**, **SQL Server (MSSQL)**, and **Oracle (OCI8)**.

## Key Features
- **Headless-Ready:** Natively supports headless architecture with built-in REST API endpoints.
- **Role-Based Access Control (RBAC):** Granular user management and permission handling.
- **Modular Architecture:** Write clean, modular code that separates your business logic beautifully.
- **Multi-language Support:** Easily create multilingual applications.
- **Dynamic CRUD Builder:** Rapidly build powerful backend interfaces using the integrated Laboratory engine.
- **Modern UI/UX:** Sleek, responsive, and customizable default themes for both frontend and backend.

# Server Requirements
PHP version 8.2 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)
- [libcurl](http://php.net/manual/en/curl.requirements.php)
- json
- xml

# Installation
There are two installation methods you can choose:

### Composer Installation (Recommended)
1. Run the following command anywhere inside your web server's root directory:
   ```bash
   composer create-project abydahana/aksara
   ```
2. Access your project via browser (e.g., `http://localhost/aksara`).
3. Follow the interactive installation wizard to set up your database and administrator account.

### Manual Installation
1. Download the source code and extract its content to your web server's directory.
2. Run `composer install` from the root of the project directory to install dependencies.
3. Access your project via browser and follow the installation wizard.

# Documentation
For comprehensive documentation, tutorials, and API references, please visit the official [Aksara CMS Documentation](https://aksaracms.com).

# Contributing
We welcome contributions to the **Aksara CMS** project! Whether you can write code, improve documentation, or report bugs, all forms of contribution are highly appreciated. 

# License
This project is licensed under the MIT License.
