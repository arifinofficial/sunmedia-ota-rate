<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbae331ec0689c41e031656f5b4531d2a
{
    public static $prefixLengthsPsr4 = array (
        'I' => 
        array (
            'Inc\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Inc\\' => 
        array (
            0 => __DIR__ . '/../..' . '/inc',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitbae331ec0689c41e031656f5b4531d2a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitbae331ec0689c41e031656f5b4531d2a::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}