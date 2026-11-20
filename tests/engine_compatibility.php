<?php declare(strict_types=1);

/**
 * @copyright Copyright © 2016-2026 Localzet Group. All rights reserved.
 * SPDX-License-Identifier: AGPL-3.0-or-later
 * @license AGPL-3.0-or-later
 */

use Triangle\Engine\Config;
use Triangle\Engine\Path;
use Triangle\Engine\Request;
use Triangle\View\Install;
use Triangle\View\Render\AbstractRender;
use Triangle\View\Render\Raw;
use Triangle\View\Support\View;

require dirname(__DIR__) . '/vendor/autoload.php';

final class EngineCompatibilityRequestProvider
{
    public static ?object $request = null;

    public static function request(): ?object
    {
        return self::$request;
    }
}

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function removeTree(string $path): void
{
    if (!is_dir($path)) {
        @unlink($path);
        return;
    }

    foreach (new FilesystemIterator($path) as $item) {
        removeTree($item->getPathname());
    }

    rmdir($path);
}

$basePath = sys_get_temp_dir() . '/triangle-view-engine-' . bin2hex(random_bytes(8));
$configPath = "$basePath/config";
$appPath = "$basePath/app";
$viewPath = "$appPath/dashboard/view";
$runtimePath = "$basePath/runtime";

foreach ([$configPath, $viewPath, $runtimePath, "$basePath/public"] as $directory) {
    mkdir($directory, 0700, true);
}

Path::$basePath = $basePath;
Path::$configPath = $configPath;
Path::$appPath = $appPath;
Path::$publicPath = "$basePath/public";
Path::$runtimePath = $runtimePath;

file_put_contents(
    "$configPath/server.php",
    "<?php return ['handler' => " . var_export(EngineCompatibilityRequestProvider::class, true) . "];\n",
);

Config::clear();
Config::load($configPath);

ob_start();
Install::install();
ob_end_clean();

check(is_file("$configPath/view.php"), 'Install did not copy the default view configuration.');

Config::clear();
Config::load($configPath);

check(config('view.handler') === Raw::class, 'Engine configuration did not register the default renderer.');

if (!class_exists(\Triangle\Request::class)) {
    class_alias(Request::class, \Triangle\Request::class);
}

if (!class_exists(\Triangle\Response::class)) {
    class_alias(\Triangle\Engine\Response::class, \Triangle\Response::class);
}

$request = new Request('');
$request->app = 'dashboard';
EngineCompatibilityRequestProvider::$request = $request;

file_put_contents("$viewPath/prefix.phtml", 'prefix|');
file_put_contents(
    "$viewPath/page.phtml",
    '<?= htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8") ?>'
        . '|<?= htmlspecialchars($shared, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8") ?>'
        . '|<?= htmlspecialchars($override, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8") ?>'
        . '|<?= $raw ?>|',
);
file_put_contents("$viewPath/suffix.phtml", '|suffix');

View::assign('shared', 'from request & <tag>');
View::assign('override', 'request value');
AbstractRender::addPreRender('prefix', 'dashboard');
AbstractRender::addPreRender('prefix', 'dashboard');
AbstractRender::addPostRender('suffix', 'dashboard');
AbstractRender::addPostRender('suffix', 'dashboard');

$response = view('page', [
    'title' => 'A&B',
    'override' => 'helper wins',
    'raw' => '<strong>raw template output</strong>',
], 'dashboard');
$expected = 'prefix|A&amp;B|from request &amp; &lt;tag&gt;|helper wins|'
    . '<strong>raw template output</strong>||suffix';

check($response instanceof \Triangle\Response, 'The view helper did not return the Engine response type.');
check($response->rawBody() === $expected, 'The renderer lost assigned variables, escaping, or post-render templates.');

Config::clear();
EngineCompatibilityRequestProvider::$request = null;
removeTree($basePath);

echo "ENGINE_VIEW_COMPATIBILITY=ok\n";
echo 'TRIANGLE_ENGINE_VERSION=' . (Composer\InstalledVersions::getPrettyVersion('triangle/engine') ?? 'unknown') . "\n";
