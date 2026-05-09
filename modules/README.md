# Modules Directory

This directory is the core workspace for **Aksara** modules. It utilizes an HMVC (Hierarchical Model View Controller) architecture, allowing you to build modular, scalable, and reusable components without cluttering the core framework.

## Directory Structure
Each module should be contained within its own folder. A standard module structure looks like this:

```text
modules/
└── YourModule/
    ├── Config/
    │   └── Routes.php         # Module-specific routing (Optional)
    ├── Controllers/
    │   └── YourController.php # The main logic (Required)
    ├── Models/
    │   └── YourModel.php      # Database operations (Optional)
    └── Views/
        └── index.php          # Frontend template (Optional)
```

## How to Create a New Module

1. **Create the Folder**: Inside `modules/`, create a new folder using PascalCase (e.g., `Reporting`).
2. **Create the Controller**: Inside `modules/Reporting/Controllers/`, create a file named `Reporting.php`.
   ```php
   <?php
   namespace Modules\Reporting\Controllers;
   
   use Aksara\Laboratory\Core;

   class Reporting extends Core
   {
       public function __construct()
       {
           parent::__construct();
           // Require user authentication (optional)
           $this->restrict_on_demo();
           $this->set_permission();
           $this->set_theme('backend');
       }

       public function index()
       {
           $this->set_title('Spatial Reports')
                ->set_icon('mdi mdi-map-marker')
                ->render('ta__spatial_reports'); // Renders the table automatically
       }
   }
   ```
3. **Access the Module**: Your module will be automatically mapped and available via your browser at `http://yourdomain.com/reporting`.

## Built-in Modules
Aksara includes several pre-built modules located in this directory. Some core examples include:
- **Administrative**: Contains CMS settings, user management, privileges, and core configuration.
- **Addons**: Handles theme and module installations.

## Note on Namespacing
When creating classes inside your module, ensure the namespace maps correctly to the directory structure. For example, a controller located in `modules/Reporting/Controllers/Reporting.php` must strictly use the namespace:
`namespace Modules\Reporting\Controllers;`