<?php

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

use support\Response;
use Triangle\View\Render\Blade;
use Triangle\View\Render\Raw;
use Triangle\View\Render\ThinkPHP;
use Triangle\View\Render\Twig;

/**
 * @param array $data
 * @param null $status
 * @param array $headers
 * @return Response
 * @throws Throwable
 */
function responseView(array $data, $status = null, array $headers = []): Response
{
    if (
        ($status == 200 || $status == 500)
        && (!empty($data['status']) && is_numeric($data['status']))
        && ($data['status'] >= 100 && $data['status'] < 600)
    ) {
        $status = $data['status'];
    }
    $template = ($status == 200) ? 'success' : 'error';

    return new Response($status, $headers, Raw::renderSys($template, $data));
}

/**
 * @param string|array|null $template
 * @param array $vars
 * @param string|null $app
 * @param string|null $plugin
 * @param int $http_code
 * @return Response
 */
function view(mixed $template = null, array $vars = [], string $app = null, string $plugin = null, int $http_code = 200): Response
{
    [$template, $vars, $app, $plugin] = template_inputs($template, $vars, $app, $plugin);
    $handler = config($plugin ? config('app.plugin_alias', 'plugin') . ".$plugin.view.handler" : 'view.handler');
    return new Response($http_code, [], $handler::render($template, $vars, $app, $plugin));
}

/**
 * @param string|array|null $template
 * @param array $vars
 * @param string|null $app
 * @param string|null $plugin
 * @return Response
 * @throws Throwable
 */
function raw_view(mixed $template = null, array $vars = [], string $app = null, string $plugin = null): Response
{
    return new Response(200, [], Raw::render(...template_inputs($template, $vars, $app, $plugin)));
}

/**
 * @param string|array|null $template
 * @param array $vars
 * @param string|null $app
 * @param string|null $plugin
 * @return Response
 */
function blade_view(mixed $template = null, array $vars = [], string $app = null, string $plugin = null): Response
{
    return new Response(200, [], Blade::render(...template_inputs($template, $vars, $app, $plugin)));
}

/**
 * @param string|array|null $template
 * @param array $vars
 * @param string|null $app
 * @param string|null $plugin
 * @return Response
 */
function think_view(mixed $template = null, array $vars = [], string $app = null, string $plugin = null): Response
{
    return new Response(200, [], ThinkPHP::render(...template_inputs($template, $vars, $app, $plugin)));
}

/**
 * @param string|array|null $template
 * @param array $vars
 * @param string|null $app
 * @param string|null $plugin
 * @return Response
 */
function twig_view(mixed $template = null, array $vars = [], string $app = null, string $plugin = null): Response
{
    return new Response(200, [], Twig::render(...template_inputs($template, $vars, $app, $plugin)));
}

/**
 * @param string|array|null $template
 * @param array $vars
 * @param string|null $app
 * @param string|null $plugin
 * @return array
 */
function template_inputs(mixed $template, array $vars, ?string $app, ?string $plugin): array
{
    $request = request();
    $plugin = $plugin === null ? ($request->plugin ?? '') : $plugin;
    if (is_array($template)) {
        $vars = $template;
        $template = null;
    }
    if ($template === null && $controller = $request->controller) {
        $controllerSuffix = config($plugin ? "plugin.$plugin.app.controller_suffix" : "app.controller_suffix", '');
        $controllerName = $controllerSuffix !== '' ? substr($controller, 0, -strlen($controllerSuffix)) : $controller;
        $path = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', substr(strrchr($controllerName, '\\'), 1)));
        $actionFileBaseName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $request->action));
        $template = "$path/$actionFileBaseName";
    }
    return [$template, $vars, $app, $plugin];
}