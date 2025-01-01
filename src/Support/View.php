<?php declare(strict_types=1);

/**
 * @package     Triangle View Component
 * @link        https://github.com/Triangle-org/View
 *
 * @author      Ivan Zorin <creator@localzet.com>
 * @copyright   Copyright (c) 2023-2025 Triangle Framework Team
 * @license     https://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License v3.0
 *
 *              This program is free software: you can redistribute it and/or modify
 *              it under the terms of the GNU Affero General Public License as published
 *              by the Free Software Foundation, either version 3 of the License, or
 *              (at your option) any later version.
 *
 *              This program is distributed in the hope that it will be useful,
 *              but WITHOUT ANY WARRANTY; without even the implied warranty of
 *              MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *              GNU Affero General Public License for more details.
 *
 *              You should have received a copy of the GNU Affero General Public License
 *              along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 *              For any questions, please contact <triangle@localzet.com>
 */

namespace Triangle\View\Support;

use function config;

/**
 * Класс View
 * Этот класс предоставляет статические методы для работы с представлениями.
 *
 * @link https://symfony.com/doc/current/templates.html
 */
class View
{
    /**
     * Метод для присвоения значения переменной представления.
     * Этот метод используется для передачи данных из вашего приложения в представление.
     *
     * @param mixed $name Имя переменной.
     * @param mixed|null $value Значение переменной.
     *
     * @link https://symfony.com/doc/current/templates.html#template-variables
     */
    public static function assign(mixed $name, mixed $value = null): void
    {
        $request = request();
        $plugin = $request->plugin ?? '';
        $handler = config($plugin ? config('app.plugin_alias', 'plugin') . ".$plugin.view.handler" : 'view.handler');
        $handler::assign($name, $value);
    }

    /**
     * Метод для получения всех переменных представления.
     * Этот метод возвращает массив всех переменных, которые были переданы в представление.
     *
     * @return array Массив переменных представления.
     *
     * @link https://symfony.com/doc/current/templates.html#template-variables
     */
    public static function vars(): array
    {
        $request = request();
        $plugin = $request->plugin ?? '';
        $handler = config($plugin ? config('app.plugin_alias', 'plugin') . ".$plugin.view.handler" : 'view.handler');
        return $handler::vars();
    }
}
