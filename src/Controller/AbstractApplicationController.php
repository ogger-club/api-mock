<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use UnexpectedValueException;

use function is_string;

abstract class AbstractApplicationController extends AbstractController
{
    protected function createProjectPathFromParameter(): string
    {
        return $this->getStringParameter('kernel.project_dir');
    }

    protected function getClientIp(Request $request): string
    {
        $cip = $request->getClientIp();

        if ($cip === null) {
            throw new UnexpectedValueException('Error retrieving client IP.');
        }

        return $cip;
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
