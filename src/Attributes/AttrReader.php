<?php

namespace CrudBooster\Attributes;

use Illuminate\Support\Facades\Log;
use ReflectionClass;
use ReflectionMethod;

class AttrReader
{
    public static function getMethods($clazz, $attributeName): array
    {
        $reflectionClass = new ReflectionClass($clazz);
        $methods = [];
        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $methodAttributes = $method->getAttributes($attributeName);
            if (!empty($methodAttributes)) {
                // Get attribute arguments
                $methodAttribute = $methodAttributes[0]->getArguments();
                if(count($methodAttribute) !== 1) {
                    Log::error('Method attribute must have one argument', ['method' => $method->getName()]);
                    continue;
                }
                // This map key with input type and value with method name
                $methods[$methodAttribute[0]] = $method->getName();
            }
        }
        return $methods;
    }

    public static function getOrderedMethods($clazz, $attributeName): array
    {
        $reflectionClass = new ReflectionClass($clazz);
        $methods = [];
        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $methodAttributes = $method->getAttributes($attributeName);
            if (!empty($methodAttributes)) {
                // Get attribute arguments
                $methodAttribute = $methodAttributes[0]->getArguments();
                $orderNo = $methodAttribute['order'] ?? 0;
                // This map key with input type and value with method name
                $methods[] = ['order' => $orderNo, 'method' => $method->getName()];
            }
        }
        usort($methods, function($a, $b) {
            return $a['order'] <=> $b['order'];
        });

        return array_map(function($method) {
            return $method['method'];
        }, $methods);
    }

    public static function getNoArgumentMethods($clazz, $attributeName): array
    {
        $reflectionClass = new ReflectionClass($clazz);
        $methods = [];
        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $methodAttributes = $method->getAttributes($attributeName);
            if (!empty($methodAttributes)) {
                $methods[] = $method->getName();
            }
        }
        return $methods;
    }
}
