# Themes Directory

This directory contains all the frontend and backend themes used by **Aksara**. Themes control the visual presentation of your application and can be easily switched without altering the core logic or modules.

## Theme Structure
Each theme is located in its own folder within this directory. A standard theme structure looks like this:

```text
themes/
└── your-theme/
    ├── assets/
    │   ├── css/
    │   ├── js/
    │   └── images/
    ├── components/
    │   └── (Reusable UI components like breadcrumbs, pagination, etc.)
    ├── layout.php      # The main HTML wrapper
    ├── header.php      # Top navigation
    ├── sidebar.php     # Side navigation (if applicable)
    ├── breadcrumb.php  # Breadcrumb template
    └── package.json    # Theme metadata (Required for Addon Manager)
```

## How to Create a Custom Theme

1. **Create the Theme Folder**: Create a new folder inside `themes/` with your theme's name (e.g., `MyCustomTheme`).
2. **Create `package.json`**: This file is required for the CMS to recognize the theme.
   ```json
   {
       "name": "My Custom Theme",
       "description": "A beautiful custom theme for Aksara.",
       "version": "1.0.0",
       "author": "Your Name",
       "type": "frontend"
   }
   ```
   *Note: `type` can be either `frontend` or `backend`.*
3. **Create `layout.php`**: This is the master template. It must include the placeholder where the core module views will be injected.
   ```html
   <!DOCTYPE html>
   <html lang="en">
   <head>
       <title><?= $template->meta->title; ?></title>
       <?= aksara_header(); ?>
   </head>
   <body>
       <?php include 'header.php'; ?>
       
       <main class="container">
           <!-- THIS IS WHERE MODULE CONTENT WILL BE INJECTED -->
           <?= $template->content; ?>
       </main>
       
       <?php include 'footer.php'; ?>
       <?= aksara_footer(); ?>
   </body>
   </html>
   ```
4. **Activate Your Theme**: Go to your application's **Settings**, navigate to the **Themes** section, and select your newly created theme from the dropdown menu.

## Best Practices
- **Never modify core themes**: If you want to make changes to a built-in theme, create a copy of it first. Modifying core themes will cause conflicts when you update Aksara to a newer version.
- **Use `aksara_header()` and `aksara_footer()`**: Always include these helpers in your `layout.php`. They inject necessary system CSS/JS and localized variables required by Aksara's form builders and AJAX architecture.
