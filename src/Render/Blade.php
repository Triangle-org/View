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

use Jenssegers\Blade\Blade as BladeView;
use function app_path;
use function array_merge;
use function base_path;
use function config;
use function runtime_path;

/**
 * Класс Blade
 * Этот класс представляет собой адаптер шаблонизатора (jenssegers/blade) и наследует от абстрактного класса AbstractView.
 * Он также реализует интерфейс ViewInterface.
 */
class Blade extends AbstractRender implements RenderInterface
{
    /**
     * Рендеринг представления.
     * @param string $template Шаблон для рендеринга
     * @param array $vars Переменные, которые должны быть доступны в шаблоне
     * @param string|null $app Приложение, которому принадлежит шаблон (необязательно)
     * @param string|null $plugin Плагин, которому принадлежит шаблон (необязательно)
     * @return string Результат рендеринга
     */
    public static function render(string $template, array $vars, string $app = null, string $plugin = null): string
    {
        static $views = [];
        $request = request();

        $app ??= $request->app ?? '';
        $plugin ??= $request->plugin ?? '';

        $configPrefix = $plugin ? config('app.plugin_alias', 'plugin') . ".$plugin." : '';
        $baseViewPath = $plugin ? base_path("plugin/$plugin/app") : app_path();

        if ($template[0] === '/') {
            $viewPath = base_path();
            $template = substr($template, 1);
        } else {
            $viewPath = $app === '' ?
                "$baseViewPath/view" :
                "$baseViewPath/$app/view";
        }

        if (!isset($views[$viewPath])) {
            $views[$viewPath] = new BladeView($viewPath, runtime_path('views'));

            $extension = config("{$configPrefix}view.extension");
            if ($extension) {
                $extension($views[$viewPath]);
            }
        }

        if (isset($request->_view_vars)) {
            $vars = array_merge((array)$request->_view_vars, $vars);
        }

        return $views[$viewPath]->render($template, $vars);
    }
}
