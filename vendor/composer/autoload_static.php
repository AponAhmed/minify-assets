<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6abad33b8f0b66466e7db476afa316d8
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'MatthiasMullie\\PathConverter\\' => 29,
            'MatthiasMullie\\Minify\\' => 22,
            'MFY\\libs\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'MatthiasMullie\\PathConverter\\' => 
        array (
            0 => __DIR__ . '/..' . '/matthiasmullie/path-converter/src',
        ),
        'MatthiasMullie\\Minify\\' => 
        array (
            0 => __DIR__ . '/..' . '/matthiasmullie/minify/src',
        ),
        'MFY\\libs\\' => 
        array (
            0 => __DIR__ . '/../..' . '/libs',
        ),
    );

    public static $prefixesPsr0 = array (
        'J' => 
        array (
            'JShrink' => 
            array (
                0 => __DIR__ . '/..' . '/tedivm/jshrink/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6abad33b8f0b66466e7db476afa316d8::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6abad33b8f0b66466e7db476afa316d8::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit6abad33b8f0b66466e7db476afa316d8::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit6abad33b8f0b66466e7db476afa316d8::$classMap;

        }, null, ClassLoader::class);
    }
}
