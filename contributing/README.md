## Guidelines

Before we look into how to contribute to Aksara, here are some guidelines.
Your Pull Requests (PRs) need to meet our guidelines.

If your Pull Requests fail to pass these guidelines, they will be declined,
and you will need to re-submit when you've made the changes.
This might sound a bit tough, but it is required for us to maintain the quality
of the codebase.

### PHP Style

- [Aksara Coding Style Guide](./STYLEGUIDE.md)

All code must conform to our [Style Guide](./STYLEGUIDE.md), which is
based on PSR-12.

This makes certain that all submitted code is of the same format as the existing
code and ensures that the codebase will be as readable as possible.

You can fix most of the coding style violations by running this command in your
terminal:

```console
composer cs-fix
```