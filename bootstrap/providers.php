<?php

use App\Providers\AppServiceProvider;

$moduleProviders = [];

foreach (glob(dirname(__DIR__).'/app/Modules/*/*ServiceProvider.php') as $file) {
    $module = basename(dirname($file));
    $moduleProviders[] = "App\\Modules\\{$module}\\{$module}ServiceProvider";
}

return [
    AppServiceProvider::class,
    ...$moduleProviders,
];
