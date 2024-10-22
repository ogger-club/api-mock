<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use UnexpectedValueException;

use function is_string;

abstract class AbstractApplicationController extends AbstractController
{
    protected function createProjectPathFromParameter(): string
    {
        return $this->getStringParameter('kernel.project_dir');
    }

    protected function getStringParameter(string $name): string
    {
        $result = $this->getParameter($name);

        if (!is_string($result)) {
            throw new UnexpectedValueException('Parameter value is not a string.');
        }

        return $result;
    }
}
