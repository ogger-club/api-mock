<?php

declare(strict_types=1);

use App\Kernel;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return static function (array $context) {
    assert(isset($context['APP_ENV']) && is_string($context['APP_ENV']));
    assert(isset($context['APP_DEBUG']));

    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
