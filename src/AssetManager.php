<?php

namespace Metalogico\Formello;

class AssetManager
{
    protected static array $scripts = [];

    protected static array $styles = [];

    /**
     * Add a script to the queue
     */
    public static function addScript(string $script): void
    {
        if (! in_array($script, static::$scripts)) {
            static::$scripts[] = $script;
        }
    }

    /**
     * Add a style to the queue
     */
    public static function addStyle(string $style): void
    {
        if (! in_array($style, static::$styles)) {
            static::$styles[] = $style;
        }
    }

    /**
     * Get all registered scripts
     */
    public static function getScripts(): array
    {
        return static::$scripts;
    }

    /**
     * Get all registered styles
     */
    public static function getStyles(): array
    {
        return static::$styles;
    }

    /**
     * Reset registered assets (useful for testing)
     */
    public static function reset(): void
    {
        static::$scripts = [];
        static::$styles = [];
    }

}
