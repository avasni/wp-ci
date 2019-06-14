<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc6bbba79070f8dc1e0af07ceea71287f
{
    public static $files = array (
        'ce89ac35a6c330c55f4710717db9ff78' => __DIR__ . '/..' . '/kriswallsmith/assetic/src/functions.php',
        'decc78cc4436b1292c6c0d151b19445c' => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib/bootstrap.php',
        '3919eeb97e98d4648304477f8ef734ba' => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib/Crypt/Random.php',
        '89ff252b349d4d088742a09c25f5dd74' => __DIR__ . '/..' . '/yahnis-elsts/plugin-update-checker/plugin-update-checker.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Component\\Process\\' => 26,
            'Symfony\\Component\\EventDispatcher\\' => 34,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Component\\Process\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/process',
        ),
        'Symfony\\Component\\EventDispatcher\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/event-dispatcher',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
    );

    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'System' => 
            array (
                0 => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib',
            ),
        ),
        'O' => 
        array (
            'OpenCloud' => 
            array (
                0 => __DIR__ . '/..' . '/rackspace/php-opencloud/lib',
                1 => __DIR__ . '/..' . '/rackspace/php-opencloud/tests',
            ),
        ),
        'N' => 
        array (
            'Net' => 
            array (
                0 => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib',
            ),
        ),
        'M' => 
        array (
            'Math' => 
            array (
                0 => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib',
            ),
        ),
        'G' => 
        array (
            'Guzzle\\Tests' => 
            array (
                0 => __DIR__ . '/..' . '/guzzle/guzzle/tests',
            ),
            'Guzzle' => 
            array (
                0 => __DIR__ . '/..' . '/guzzle/guzzle/src',
            ),
        ),
        'F' => 
        array (
            'File' => 
            array (
                0 => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib',
            ),
        ),
        'E' => 
        array (
            'Eher\\OAuth' => 
            array (
                0 => __DIR__ . '/..' . '/eher/oauth/src',
            ),
        ),
        'C' => 
        array (
            'Crypt' => 
            array (
                0 => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib',
            ),
            'ComponentInstaller' => 
            array (
                0 => __DIR__ . '/..' . '/robloach/component-installer/src',
            ),
        ),
        'A' => 
        array (
            'Aws' => 
            array (
                0 => __DIR__ . '/..' . '/aws/aws-sdk-php/src',
            ),
            'Assetic' => 
            array (
                0 => __DIR__ . '/..' . '/kriswallsmith/assetic/src',
            ),
        ),
    );

    public static $classMap = array (
        'Psr\\Log\\AbstractLogger' => __DIR__ . '/..' . '/psr/log/Psr/Log/AbstractLogger.php',
        'Psr\\Log\\InvalidArgumentException' => __DIR__ . '/..' . '/psr/log/Psr/Log/InvalidArgumentException.php',
        'Psr\\Log\\LogLevel' => __DIR__ . '/..' . '/psr/log/Psr/Log/LogLevel.php',
        'Psr\\Log\\LoggerAwareInterface' => __DIR__ . '/..' . '/psr/log/Psr/Log/LoggerAwareInterface.php',
        'Psr\\Log\\LoggerAwareTrait' => __DIR__ . '/..' . '/psr/log/Psr/Log/LoggerAwareTrait.php',
        'Psr\\Log\\LoggerInterface' => __DIR__ . '/..' . '/psr/log/Psr/Log/LoggerInterface.php',
        'Psr\\Log\\LoggerTrait' => __DIR__ . '/..' . '/psr/log/Psr/Log/LoggerTrait.php',
        'Psr\\Log\\NullLogger' => __DIR__ . '/..' . '/psr/log/Psr/Log/NullLogger.php',
        'Psr\\Log\\Test\\DummyTest' => __DIR__ . '/..' . '/psr/log/Psr/Log/Test/LoggerInterfaceTest.php',
        'Psr\\Log\\Test\\LoggerInterfaceTest' => __DIR__ . '/..' . '/psr/log/Psr/Log/Test/LoggerInterfaceTest.php',
        'Symfony\\Component\\EventDispatcher\\ContainerAwareEventDispatcher' => __DIR__ . '/..' . '/symfony/event-dispatcher/ContainerAwareEventDispatcher.php',
        'Symfony\\Component\\EventDispatcher\\Debug\\TraceableEventDispatcher' => __DIR__ . '/..' . '/symfony/event-dispatcher/Debug/TraceableEventDispatcher.php',
        'Symfony\\Component\\EventDispatcher\\Debug\\TraceableEventDispatcherInterface' => __DIR__ . '/..' . '/symfony/event-dispatcher/Debug/TraceableEventDispatcherInterface.php',
        'Symfony\\Component\\EventDispatcher\\Debug\\WrappedListener' => __DIR__ . '/..' . '/symfony/event-dispatcher/Debug/WrappedListener.php',
        'Symfony\\Component\\EventDispatcher\\DependencyInjection\\RegisterListenersPass' => __DIR__ . '/..' . '/symfony/event-dispatcher/DependencyInjection/RegisterListenersPass.php',
        'Symfony\\Component\\EventDispatcher\\Event' => __DIR__ . '/..' . '/symfony/event-dispatcher/Event.php',
        'Symfony\\Component\\EventDispatcher\\EventDispatcher' => __DIR__ . '/..' . '/symfony/event-dispatcher/EventDispatcher.php',
        'Symfony\\Component\\EventDispatcher\\EventDispatcherInterface' => __DIR__ . '/..' . '/symfony/event-dispatcher/EventDispatcherInterface.php',
        'Symfony\\Component\\EventDispatcher\\EventSubscriberInterface' => __DIR__ . '/..' . '/symfony/event-dispatcher/EventSubscriberInterface.php',
        'Symfony\\Component\\EventDispatcher\\GenericEvent' => __DIR__ . '/..' . '/symfony/event-dispatcher/GenericEvent.php',
        'Symfony\\Component\\EventDispatcher\\ImmutableEventDispatcher' => __DIR__ . '/..' . '/symfony/event-dispatcher/ImmutableEventDispatcher.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc6bbba79070f8dc1e0af07ceea71287f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc6bbba79070f8dc1e0af07ceea71287f::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitc6bbba79070f8dc1e0af07ceea71287f::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitc6bbba79070f8dc1e0af07ceea71287f::$classMap;

        }, null, ClassLoader::class);
    }
}
