## Contributing Guidelines

Thank you for helping improve Aksara CMS.

Aksara is built to keep complex application development shorter, reusable,
secure, and reliable, whether it is used as a CMS, a framework, a traditional
browser-based application, or a headless REST API. Contributions should protect
that philosophy: keep the core lightweight, respect the modular architecture,
and make every change clear enough for the next developer to understand and
extend.

Before opening a Pull Request (PR), please make sure your changes are focused,
consistent with the existing codebase, and aligned with the guidelines below.
PRs that do not meet the project standards may be declined until they are
updated.

### Coding Style

All PHP code must follow the [Aksara Coding Style Guide](./STYLEGUIDE.md),
which is based on PSR-12 with Aksara's own conventions.

To keep formatting consistent, run the Composer fixer before committing your
changes:

```console
composer cs-fix
```

This command applies the project's PHP CS Fixer rules automatically and should
be part of your regular contribution flow before every commit.
