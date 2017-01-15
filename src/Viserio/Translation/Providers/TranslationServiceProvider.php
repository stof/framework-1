<?php
declare(strict_types=1);
namespace Viserio\Translation\Providers;

use Interop\Container\ContainerInterface;
use Interop\Container\ServiceProvider;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Viserio\Contracts\Support\Traits\ServiceProviderConfigAwareTrait;
use Viserio\Contracts\Translation\TranslationManager as TranslationManagerContract;
use Viserio\Contracts\Translation\Translator as TranslatorContract;
use Viserio\Parsers\FileLoader;
use Viserio\Translation\MessageSelector;
use Viserio\Translation\PluralizationRules;
use Viserio\Translation\TranslationManager;

class TranslationServiceProvider implements ServiceProvider
{
    use ServiceProviderConfigAwareTrait;

    public const PACKAGE = 'viserio.translation';

    /**
     * {@inheritdoc}
     */
    public function getServices()
    {
        return [
            TranslationManagerContract::class => [self::class, 'createTranslationManager'],
            TranslationManager::class         => function (ContainerInterface $container) {
                return $container->get(TranslationManagerContract::class);
            },
            TranslatorContract::class => [self::class, 'createTranslator'],
            'translator'              => function (ContainerInterface $container) {
                return $container->get(TranslatorContract::class);
            },
        ];
    }

    public static function createTranslationManager(ContainerInterface $container): TranslationManager
    {
        $manager = new TranslationManager(
            new PluralizationRules(),
            new MessageSelector()
        );

        $manager->setLoader($container->get(FileLoader::class));

        if (($locale = self::getConfig($container, 'locale')) !== null) {
            $manager->setLocale($locale);
        }

        if (($directories = self::getConfig($container, 'directories')) !== null) {
            $manager->setDirectories($directories);
        }

        if (($imports = self::getConfig($container, 'files')) !== null) {
            foreach ((array) $imports as $import) {
                $manager->import($import);
            }
        }

        if ($container->has(PsrLoggerInterface::class)) {
            $manager->setLogger($container->get(PsrLoggerInterface::class));
        }

        return $manager;
    }

    public static function createTranslator(ContainerInterface $container): TranslatorContract
    {
        return $container->get(TranslationManager::class)->getTranslator();
    }
}