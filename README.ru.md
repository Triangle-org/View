# Triangle View

Triangle View предоставляет небольшой слой адаптеров для PHP-шаблонов и, при установленной зависимости, для Blade, Twig или ThinkPHP.

## Совместимость

- PHP 8.1 или новее.
- Triangle Engine `^8.1` или `^9.0`.
- Engine 9 работает на Localzet Server 7; поддержка Engine 8 сохранена для существующих приложений.

Установите пакет через Composer:

```sh
composer require triangle/view
```

Класс `Install` копирует конфигурацию `view.php` в каталог конфигурации приложения, если файла там ещё нет. По умолчанию используется обработчик `raw`; переменная `VIEW_HANDLER` со значением `blade`, `twig` или `think` выбирает соответствующий необязательный адаптер. Установите пакет выбранного шаблонизатора отдельно.

По умолчанию шаблон `page` приложения `dashboard` загружается из `app/dashboard/view/page.phtml`. Помощник `view()` возвращает ответ Engine и объединяет переменные текущего запроса с параметрами вызова; явные параметры имеют приоритет.

Обработчик `raw` исполняет PHP-шаблоны и не экранирует вывод автоматически. Экранируйте недоверенные значения в шаблоне, например через `htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')`.

Запуск интеграционной проверки Engine и рендеринга:

```sh
composer test
```

English version: [README.md](README.md).

Copyright © 2016-2026 [Localzet Group](https://www.localzet.com/). All rights reserved.
