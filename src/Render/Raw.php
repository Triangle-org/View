<?php declare(strict_types=1);

/**
 * @package     Triangle View Component
 * @link        https://github.com/Triangle-org/View
 *
 * @author      Ivan Zorin <creator@localzet.com>
 * @copyright   Copyright (c) 2023-2024 Triangle Framework Team
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

namespace Triangle\View\Render;

use Throwable;
use function config;
use function extract;
use function ob_end_clean;
use function ob_get_clean;
use function ob_start;

/**
 * Класс Raw
 * Этот класс представляет собой движок шаблонизации PHP и наследует от абстрактного класса AbstractView.
 * Он также реализует интерфейс ViewInterface.
 */
class Raw extends AbstractRender implements RenderInterface
{
    /**
     * Рендеринг представления.
     * @param string $template Шаблон для рендеринга
     * @param array $vars Переменные, которые должны быть доступны в шаблоне
     * @param string|null $app Приложение, которому принадлежит шаблон (необязательно)
     * @param string|null $plugin Плагин, которому принадлежит шаблон (необязательно)
     * @return string Результат рендеринга
     * @throws Throwable
     */
    public static function render(string $template, array $vars, string $app = null, string $plugin = null): string
    {
        $configPrefix = $plugin ? config('app.plugin_alias', 'plugin') . ".$plugin." : '';

        foreach (config("{$configPrefix}view.options.pre_renders", []) as $render) {
            if (isset($render['template'])) {
                static::assign($render['vars'] ?? []);
                static::addPreRender($render['template'], $render['app'] ?? null, $render['plugin'] ?? null);
            }
        }

        foreach (config("{$configPrefix}view.options.post_renders", []) as $render) {
            if (isset($render['template'])) {
                static::assign($render['vars'] ?? []);
                static::addPostRender($render['template'], $render['app'] ?? null, $render['plugin'] ?? null);
            }
        }

        $preRenders = static::getPreRenders();
        $curRender = ['template' => $template, 'app' => $app, 'plugin' => $plugin];
        $postRenders = static::getPostRenders();

        extract(config("{$configPrefix}view.options.vars", []));
        if (isset($request->_view_vars)) {
            extract((array)$request->_view_vars);
        }
        
        extract($vars);
        ob_start();

        try {
            foreach (array_merge($preRenders, [$curRender], $postRenders) as $render) {
                $file = static::build($render);
                if (file_exists($file)) {
                    include $file;
                }
            }
        } catch (Throwable $throwable) {
            ob_end_clean();
            throw $throwable;
        }

        return ob_get_clean();
    }

    /**
     * Рендеринг системного представления.
     * @param string $template Шаблон для рендеринга
     * @param array $vars Переменные, которые должны быть доступны в шаблоне
     * @return false|string Результат рендеринга
     * @throws Throwable
     */
    public static function renderSys(string $template, array $vars): false|string
    {
        $request = request();
        $plugin = $request->plugin ?? '';
        $configPrefix = $plugin ? config('app.plugin_alias', 'plugin') . ".$plugin." : '';

        $view = config("{$configPrefix}view.templates.system.$template", __DIR__ . "/../Templates/$template.phtml");

        if (isset($request->_view_vars)) {
            extract((array)$request->_view_vars);
        }
        
        extract($vars);
        ob_start();

        try {
            include $view;
        } catch (Throwable $throwable) {
            ob_end_clean();
            throw $throwable;
        }

        return ob_get_clean();
    }
}
