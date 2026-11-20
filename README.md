<p align="center"><a href="https://www.localzet.com" target="_blank">
  <img src="https://static.zorin.space/media/logos/LocalzetGroup.png" width="400">
</a></p>

<p align="center">
  <a href="https://packagist.org/packages/triangle/view">
  <img src="https://img.shields.io/packagist/dt/triangle/view?label=%D0%A1%D0%BA%D0%B0%D1%87%D0%B8%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F" alt="Скачивания">
</a>
  <a href="https://github.com/Triangle-org/View">
  <img src="https://img.shields.io/github/commit-activity/t/Triangle-org/View?label=%D0%9A%D0%BE%D0%BC%D0%BC%D0%B8%D1%82%D1%8B" alt="Коммиты">
</a>
  <a href="https://packagist.org/packages/triangle/view">
  <img src="https://img.shields.io/packagist/v/triangle/view?label=%D0%92%D0%B5%D1%80%D1%81%D0%B8%D1%8F" alt="Версия">
</a>
  <a href="https://packagist.org/packages/triangle/view">
  <img src="https://img.shields.io/packagist/dependency-v/triangle/view/php?label=PHP" alt="Версия PHP">
</a>
  <a href="https://github.com/Triangle-org/View">
  <img src="https://img.shields.io/github/license/Triangle-org/View?label=%D0%9B%D0%B8%D1%86%D0%B5%D0%BD%D0%B7%D0%B8%D1%8F" alt="Лицензия">
</a>
</p>

# Triangle View

Triangle View provides a small adapter layer for rendering PHP templates and, when installed, Blade, Twig, or ThinkPHP templates.

## Compatibility

- PHP 8.1 or newer.
- Triangle Engine `^8.1` or `^9.0`.
- Engine 9 runs on Localzet Server 7; Engine 8 remains supported for existing applications.

Install the package with Composer:

```sh
composer require triangle/view
```

The package's `Install` helper copies the default `view.php` configuration into the application's config directory when it does not already exist. The default handler is `raw`; set `VIEW_HANDLER` to `blade`, `twig`, or `think` to select an optional adapter. Install the corresponding template engine package separately.

By default, a template named `page` for the `dashboard` application is loaded from `app/dashboard/view/page.phtml`. The `view()` helper returns an Engine response and merges variables assigned to the current request with the variables passed to the helper; explicit helper variables take precedence.

The `raw` handler executes PHP templates and does not escape output automatically. Escape untrusted values in the template, for example with `htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')`.

Run the Engine compatibility and rendering integration check with:

```sh
composer test
```

For Russian documentation, see [README.ru.md](README.ru.md).

Copyright © 2016-2026 [Localzet Group](https://www.localzet.com/). All rights reserved.
