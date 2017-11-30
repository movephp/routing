<?php

namespace Movephp\Routing\Route\Parameter;

use Movephp\Routing\Exception;

class Factory
{
    /**
     *
     */
    private const TYPES = [
        ParameterUntyped::class,
        ParameterBool::class,
        ParameterInt::class,
        ParameterString::class,
        ParameterArr::class,
        ParameterArrInt::class,
        ParameterArrStr::class,
        ParameterEnum::class
    ];

    /**
     * @param string $routePattern
     * @return ParameterAbstract[]
     */
    public static function makeFromPattern(string $routePattern): array
    {
        $matchPattern = array_map(
            function ($type) {
                return $type::matchPattern();
            },
            self::TYPES
        );
        $regex = '/(' . implode(')|(', $matchPattern) . ')/ius';
        preg_match_all($regex, $routePattern, $matches);

        $parameters = [];
        foreach ($matches[0] as $match) {
            $parameters[] = self::make($match);
        }
        return $parameters;
    }

    /**
     * @param string $match
     * @return ParameterAbstract
     * @throws Exception\UnknownParameterTypeException
     */
    private static function make(string $match): ParameterAbstract
    {
        foreach (self::TYPES as $type) {
            if (preg_match('/' . $type::matchPattern() . '/ius', $match)) {
                return new $type($match);
            }
        }
        throw new Exception\UnknownParameterTypeException(sprintf(
            'Route parameter "%s" does not match any type',
            $match
        ));
    }
}