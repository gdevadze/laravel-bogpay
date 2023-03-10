<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7923b7334cc550940e9f870f825656d1
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'Devadze\\BogPay\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Devadze\\BogPay\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7923b7334cc550940e9f870f825656d1::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7923b7334cc550940e9f870f825656d1::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit7923b7334cc550940e9f870f825656d1::$classMap;

        }, null, ClassLoader::class);
    }
}
