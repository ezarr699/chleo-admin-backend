<?php

use App\Providers\AppServiceProvider;

$moduleProviders = [];

foreach (glob(dirname(__DIR__).'/Modules/*/*ServiceProvider.php') as $file) {
    $module = basename(dirname($file));
    $moduleProviders[] = "Modules\\{$module}\\{$module}ServiceProvider";
}

return [
    AppServiceProvider::class,
    ...$moduleProviders,
];
