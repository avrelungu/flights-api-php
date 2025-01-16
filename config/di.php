<?php

use App\Serializer\Serializer;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use League\Container\Argument\Literal\ArrayArgument;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

require dirname(__DIR__) . '/vendor/autoload.php';

$container = new Container();
$container->delegate(new ReflectionContainer(true));

# Settings
$settings = require_once __DIR__ . '/settings.php';
$container->add('settings', new ArrayArgument($settings));

# Maintenance Mode
$container->add('maintenance_mode', function() use ($settings) {
    return $settings['app']['maintenance_mode'] === 'true';
});

# Services
$container->addShared(EntityManagerInterface::class, function() use ($settings) : EntityManagerInterface {
    $config = ORMSetup::createAttributeMetadataConfiguration(
        paths: $settings['doctrine']['metadata_dirs'],
        isDevMode: $settings['doctrine']['dev_mode']
    );

    $dsn = $settings['doctrine']['connection']['dsn'];
    $dsnParser = new DsnParser();
    $connectionParams = $dsnParser->parse($dsn);

    $connection = DriverManager::getConnection(
        params: $connectionParams,
        config: $config
    );
    return new EntityManager($connection, $config);
});

$container->add(\Symfony\Component\Serializer\SerializerInterface::class, function() {
    $encoders = [
        new \Symfony\Component\Serializer\Encoder\XmlEncoder(),
        new \Symfony\Component\Serializer\Encoder\JsonEncoder()
    ];

    $classMetadataFactory = new \Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory(
        new \Symfony\Component\Serializer\Mapping\Loader\AttributeLoader()
    );

    $metadataAwareNameConverter = new \Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter($classMetadataFactory);

    $normalizers = [
        new \Symfony\Component\Serializer\Normalizer\DateTimeNormalizer(),
        new \Symfony\Component\Serializer\Normalizer\ObjectNormalizer(
            $classMetadataFactory,
            $metadataAwareNameConverter,
        ),
    ];

    return new \Symfony\Component\Serializer\Serializer($normalizers, $encoders);
});

$container->add(ValidatorInterface::class, function() {
    return Validation::createValidatorBuilder()
        ->enableAttributeMapping()
        ->getValidator();
});

$container->add(LoggerInterface::class, function() use ($settings) {
    $logger = new Logger($settings['log']['name']);
    $streamHandler = new StreamHandler(
        $settings['log']['file'],
        Level::fromName($settings['log']['level'])
    );

    $logger->pushHandler($streamHandler);

    return $logger;
});

$container->addShared(Serializer::class)
    ->addArguments([SerializerInterface::class]);

return $container;