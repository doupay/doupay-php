<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita360531aff09b4d79e14e0f752c7920f
{
    public static $prefixLengthsPsr4 = array (
        'd' => 
        array (
            'doupay\\doupayphp\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'doupay\\doupayphp\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/doupay/doupay-php',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita360531aff09b4d79e14e0f752c7920f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita360531aff09b4d79e14e0f752c7920f::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
