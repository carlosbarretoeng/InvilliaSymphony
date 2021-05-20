<?php

declare(strict_types=1);

namespace App\Tests;

use ReflectionClass;
use ReflectionException;

trait TestUtils
{
    /**
     * @throws ReflectionException
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}