<?php
namespace NYPL\Starter;

use Stringy\Stringy;

class CachedStringy
{
    protected static $cache = [];

    /**
     * @param string $type
     * @param string $string
     *
     * @return string
     */
    protected static function getCachedString($type = '', $string = '')
    {
        if (isset(self::$cache[$type][$string])) {
            return self::$cache[$type][$string];
        }

        return '';
    }

    /**
     * @param string $type
     * @param string $originalString
     * @param string $stringyString
     *
     * @return string
     */
    protected static function setCachedString($type = '', $originalString = '', $stringyString = '')
    {
        self::$cache[$type][$originalString] = $stringyString;

        return $stringyString;
    }

    /**
     * @param string $type
     * @param string $string
     * @param callable $stringy
     *
     * @return string
     */
    protected static function runStringy($type, $string, callable $stringy)
    {
        if ($cachedString = self::getCachedString($type, $string)) {
            return $cachedString;
        }

        return self::setCachedString(
            $type,
            $string,
            $stringy($string)
        );
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function camelize($string = '')
    {
        return self::runStringy(__METHOD__, $string, function ($string) {
            return (string) Stringy::create($string)->camelize();
        });
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function underscored($string = '')
    {
        return self::runStringy(__METHOD__, $string, function ($string) {
            return (string) Stringy::create($string)->underscored();
        });
    }
}
