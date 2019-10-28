<?php

declare(strict_types=1);

/**
 * This file is part of Narrowspark Framework.
 *
 * (c) Daniel Bannert <d.bannert@anolilab.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace ContainerBc7e8a814feacf343ffa5dd9dcd860f7e36b78ffb496c4b7462d0df9f7d5a1b4;

/**
 * This class has been auto-generated by Viserio Container Component.
 */
final class DebugServiceProviderContainer extends \Viserio\Component\Container\AbstractCompiledContainer
{
    /**
     * List of target dirs.
     *
     * @var array
     */
    private $targetDirs = [];

    /**
     * Path to the container dir.
     *
     * @var string
     */
    private $containerDir;

    /**
     * Create a new Compiled Container instance.
     *
     * @param array  $buildParameters
     * @param string $containerDir
     *
     * @var string $containerDir
     */
    public function __construct(array $buildParameters = [], string $containerDir = __DIR__)
    {
        $this->services = $this->privates = [];
        $dir = $this->targetDirs[0] = \dirname($containerDir);

        for ($i = 1; $i <= 5; $i++) {
            $this->targetDirs[$i] = $dir = \dirname($dir);
        }

        $this->containerDir = $containerDir;
        $this->parameters = \array_merge([
            'container.dumper.inline_factories' => true,
            'container.dumper.inline_class_loader' => false,
            'container.dumper.as_files' => true,
        ], $buildParameters);
        $this->methodMapping = [
            \Symfony\Component\VarDumper\Cloner\ClonerInterface::class => 'get46b8e88975048cb31b0f0045017412a8f46d2a70cdb54dc5b7c742c769237ba0',
            \Symfony\Component\VarDumper\Dumper\DataDumperInterface::class => 'get52ff2f4e302a18ec78bae676cc3bd87ac7a5c05257e9e0d8be9be9260311b56f',
            \Symfony\Component\VarDumper\VarDumper::class => 'get4b39bfcd6c1615eaf94a5811a808827fa131f2a8ee3ca76bbc73511c8ac261e3',
            \Twig\Environment::class => 'get8d9ff7a81f29c15daa148bf17605b436a0f079af45d4fe0663dc18bfb7cc9691',
            \Twig\Loader\ChainLoader::class => 'gete6b9cca367454988e84c786bc4263504862cfd0bf91cc9293b8a3c733eb3c8bb',
            \Twig\RuntimeLoader\RuntimeLoaderInterface::class => 'get7cea9b61db5e1b62f129860035f85d49ee5302a6c31f072ca310f26f4a933965',
            \Viserio\Bridge\Twig\Command\DebugCommand::class => 'get82bd7182fb48bedbde7fdb8ea705cf3720cdc161ca8374d6d8c750d55f85d419',
            \Viserio\Bridge\Twig\Command\LintCommand::class => 'getf32036d73b7c99ae4b0444626f14d4a6c18aa429903389f6fe326667f7ecec32',
            \Viserio\Bridge\Twig\Extension\DumpExtension::class => 'get5e982a25346139e718d5327f75105fc4500493b38c9497926f65dcf3d033df69',
            \Viserio\Contract\Filesystem\Filesystem::class => 'get787a79c940e7519357cde00935d657a813a0ce7c7b555cf3cb98d8c2263d18c6',
            \Viserio\Contract\View\EngineResolver::class => 'getff73b374d52eb13f0773f58273de538d62944effed5c8e2a7caa31b6d2180e5b',
            \Viserio\Contract\View\Factory::class => 'getb6b69b8c40146f590f39a23a8d000f621d6288e79e53d4fdbeae0164b92c7d63',
            \Viserio\Contract\View\Finder::class => 'get2bc187b27da90a561b690d6fd54a3000d8276c6e5b8e8167ce59a4b9355b3859',
            \Viserio\Component\OptionsResolver\Command\OptionDumpCommand::class => 'get5a73c93dbe469f9f1fae0210ee64ef2ab32ed536467d0570a89353766859bb62',
            \Viserio\Component\OptionsResolver\Command\OptionReaderCommand::class => 'get51bc2cdf2d87fcaa6a89ede54bc023ccfe784ddb4cc7a7e2be4ab3a7e9204471',
            \Viserio\Component\View\Engine\FileEngine::class => 'get1a6c73ea96b910b7c8cc9eccb8a2efd9908e964162cfa207eeef703514994b04',
            \Viserio\Component\View\Engine\PhpEngine::class => 'getd01d428260fd74997b150257850eb533cb546f9df452b1033a6c2fae22551a64',
            \Viserio\Provider\Twig\Command\CleanCommand::class => 'getb27fd541062d323461cd1a120541021d75cbc7d2bbae5eefde976856c43fbebe',
            \Viserio\Provider\Twig\Engine\TwigEngine::class => 'get60c8946331b96312aa7aa623435675475698643b927851fb2234c12cd9374ea5',
            \Viserio\Provider\Twig\Loader::class => 'get917a35e00325c34c4098b810e960473b05e7514c36627ea7d0456564e774bd56',
            'config' => 'get34bcaa5afa8745d92e6161e8495be3b939c5c6abb4dc2fd1f5a3cfdaba620256',
        ];
        $this->aliases = [
            \Symfony\Component\VarDumper\Cloner\VarCloner::class => \Symfony\Component\VarDumper\Cloner\ClonerInterface::class,
            \Symfony\Component\VarDumper\Dumper\HtmlDumper::class => \Symfony\Component\VarDumper\Dumper\DataDumperInterface::class,
            \Twig\Loader\LoaderInterface::class => \Twig\Loader\ChainLoader::class,
            \Viserio\Component\Filesystem\Filesystem::class => \Viserio\Contract\Filesystem\Filesystem::class,
            \Viserio\Component\View\ViewFactory::class => \Viserio\Contract\View\Factory::class,
            \Viserio\Component\View\ViewFinder::class => \Viserio\Contract\View\Finder::class,
            \Viserio\Provider\Debug\HtmlDumper::class => \Symfony\Component\VarDumper\Dumper\DataDumperInterface::class,
            \Viserio\Provider\Twig\Command\LintCommand::class => \Viserio\Bridge\Twig\Command\LintCommand::class,
            'files' => \Viserio\Contract\Filesystem\Filesystem::class,
            'twig' => \Twig\Environment::class,
            'view' => \Viserio\Contract\View\Factory::class,
            'view.engine.resolver' => \Viserio\Contract\View\EngineResolver::class,
            'view.finder' => \Viserio\Contract\View\Finder::class,
        ];
    }

    /**
     * Returns the public Symfony\Component\VarDumper\Cloner\ClonerInterface shared service.
     *
     * @return \Symfony\Component\VarDumper\Cloner\VarCloner
     */
    protected function get46b8e88975048cb31b0f0045017412a8f46d2a70cdb54dc5b7c742c769237ba0(): \Symfony\Component\VarDumper\Cloner\VarCloner
    {
        $this->services[\Symfony\Component\VarDumper\Cloner\ClonerInterface::class] = $instance = new \Symfony\Component\VarDumper\Cloner\VarCloner();

        $instance->setMaxItems(2500);
        $instance->setMinDepth(1);
        $instance->setMaxString(-1);
        $instance->addCasters([
            \Closure::class => 'Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster::unsetClosureFileInfo',
        ]);

        return $instance;
    }

    /**
     * Returns the public Symfony\Component\VarDumper\Dumper\DataDumperInterface shared service.
     *
     * @return \Viserio\Provider\Debug\HtmlDumper
     */
    protected function get52ff2f4e302a18ec78bae676cc3bd87ac7a5c05257e9e0d8be9be9260311b56f(): \Viserio\Provider\Debug\HtmlDumper
    {
        $this->services[\Symfony\Component\VarDumper\Dumper\DataDumperInterface::class] = $instance = new \Viserio\Provider\Debug\HtmlDumper();

        $instance->addTheme('narrowspark', [
            'default' => 'color:#ffffff; line-height:normal; font:12px "Inconsolata", "Fira Mono", "Source Code Pro", Monaco, Consolas, "Lucida Console", monospace !important; word-wrap: break-word; white-space: pre-wrap; position:relative; z-index:99999; word-break: break-word',
            'num' => 'color:#bcd42a',
            'const' => 'color:#4bb1b1;',
            'str' => 'color:#bcd42a',
            'note' => 'color:#ef7c61',
            'ref' => 'color:#a0a0a0',
            'public' => 'color:#ffffff',
            'protected' => 'color:#ffffff',
            'private' => 'color:#ffffff',
            'meta' => 'color:#ffffff',
            'key' => 'color:#bcd42a',
            'index' => 'color:#ef7c61',
        ]);
        $instance->setTheme('narrowspark');

        return $instance;
    }

    /**
     * Returns the public Symfony\Component\VarDumper\VarDumper shared service.
     *
     * @return \Symfony\Component\VarDumper\VarDumper
     */
    protected function get4b39bfcd6c1615eaf94a5811a808827fa131f2a8ee3ca76bbc73511c8ac261e3(): \Symfony\Component\VarDumper\VarDumper
    {
        return $this->services[\Symfony\Component\VarDumper\VarDumper::class] = new \Symfony\Component\VarDumper\VarDumper();
    }

    /**
     * Returns the public Twig\Environment shared service.
     *
     * @return \Twig\Environment
     */
    protected function get8d9ff7a81f29c15daa148bf17605b436a0f079af45d4fe0663dc18bfb7cc9691(): \Twig\Environment
    {
        $this->services[\Twig\Environment::class] = $instance = new \Twig\Environment(($this->services[\Twig\Loader\ChainLoader::class] ?? $this->gete6b9cca367454988e84c786bc4263504862cfd0bf91cc9293b8a3c733eb3c8bb()), [
            'debug' => true,
            'cache' => '',
        ]);

        if (isset($this->services[\Twig\RuntimeLoader\RuntimeLoaderInterface::class])) {
            $instance->addRuntimeLoader(($this->services[\Twig\RuntimeLoader\RuntimeLoaderInterface::class] ?? null));
        }
        $instance->addExtension(new \Viserio\Bridge\Twig\Extension\StrExtension());
        $instance->addExtension(($this->services[\Viserio\Bridge\Twig\Extension\DumpExtension::class] ?? $this->get5e982a25346139e718d5327f75105fc4500493b38c9497926f65dcf3d033df69()));

        return $instance;
    }

    /**
     * Returns the public Twig\Loader\ChainLoader shared service.
     *
     * @return \Twig\Loader\ChainLoader
     */
    protected function gete6b9cca367454988e84c786bc4263504862cfd0bf91cc9293b8a3c733eb3c8bb(): \Twig\Loader\ChainLoader
    {
        $this->services[\Twig\Loader\ChainLoader::class] = $instance = new \Twig\Loader\ChainLoader();

        $instance->addLoader(($this->services[\Viserio\Provider\Twig\Loader::class] ?? $this->get917a35e00325c34c4098b810e960473b05e7514c36627ea7d0456564e774bd56()));

        return $instance;
    }

    /**
     * Returns the public Twig\RuntimeLoader\RuntimeLoaderInterface shared service.
     *
     * @return \Viserio\Provider\Twig\RuntimeLoader\IteratorRuntimeLoader
     */
    protected function get7cea9b61db5e1b62f129860035f85d49ee5302a6c31f072ca310f26f4a933965(): \Viserio\Provider\Twig\RuntimeLoader\IteratorRuntimeLoader
    {
        return $this->services[\Twig\RuntimeLoader\RuntimeLoaderInterface::class] = new \Viserio\Provider\Twig\RuntimeLoader\IteratorRuntimeLoader(new \Viserio\Component\Container\RewindableGenerator(static function () {
            return new \EmptyIterator();
        }, 0));
    }

    /**
     * Returns the public Viserio\Bridge\Twig\Command\DebugCommand shared service.
     *
     * @return \Viserio\Bridge\Twig\Command\DebugCommand
     */
    protected function get82bd7182fb48bedbde7fdb8ea705cf3720cdc161ca8374d6d8c750d55f85d419(): \Viserio\Bridge\Twig\Command\DebugCommand
    {
        return $this->services[\Viserio\Bridge\Twig\Command\DebugCommand::class] = new \Viserio\Bridge\Twig\Command\DebugCommand(($this->services[\Twig\Environment::class] ?? $this->get8d9ff7a81f29c15daa148bf17605b436a0f079af45d4fe0663dc18bfb7cc9691()));
    }

    /**
     * Returns the public Viserio\Bridge\Twig\Command\LintCommand shared service.
     *
     * @return \Viserio\Provider\Twig\Command\LintCommand
     */
    protected function getf32036d73b7c99ae4b0444626f14d4a6c18aa429903389f6fe326667f7ecec32(): \Viserio\Provider\Twig\Command\LintCommand
    {
        return $this->services[\Viserio\Bridge\Twig\Command\LintCommand::class] = new \Viserio\Provider\Twig\Command\LintCommand(($this->services[\Twig\Environment::class] ?? $this->get8d9ff7a81f29c15daa148bf17605b436a0f079af45d4fe0663dc18bfb7cc9691()), ($this->services[\Viserio\Contract\View\Finder::class] ?? $this->get2bc187b27da90a561b690d6fd54a3000d8276c6e5b8e8167ce59a4b9355b3859()), [
            'viserio' => [
                'view' => [
                    'paths' => [
                        0 => ($this->targetDirs[2] . '/Fixture/'),
                        1 => $this->targetDirs[1],
                    ],
                    'engines' => [
                        'twig' => [
                            'options' => [
                                'debug' => true,
                                'cache' => '',
                            ],
                            'file_extension' => 'html',
                            'templates' => [
                                'test.html' => 'tests',
                            ],
                            'loaders' => [
                                0 => new \Twig\Loader\ArrayLoader(),
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Returns the public Viserio\Bridge\Twig\Extension\DumpExtension shared service.
     *
     * @return \Viserio\Bridge\Twig\Extension\DumpExtension
     */
    protected function get5e982a25346139e718d5327f75105fc4500493b38c9497926f65dcf3d033df69(): \Viserio\Bridge\Twig\Extension\DumpExtension
    {
        return $this->services[\Viserio\Bridge\Twig\Extension\DumpExtension::class] = new \Viserio\Bridge\Twig\Extension\DumpExtension(($this->services[\Symfony\Component\VarDumper\Cloner\ClonerInterface::class] ?? $this->get46b8e88975048cb31b0f0045017412a8f46d2a70cdb54dc5b7c742c769237ba0()), ($this->services[\Symfony\Component\VarDumper\Dumper\DataDumperInterface::class] ?? $this->get52ff2f4e302a18ec78bae676cc3bd87ac7a5c05257e9e0d8be9be9260311b56f()));
    }

    /**
     * Returns the public Viserio\Contract\Filesystem\Filesystem shared service.
     *
     * @return \Viserio\Component\Filesystem\Filesystem
     */
    protected function get787a79c940e7519357cde00935d657a813a0ce7c7b555cf3cb98d8c2263d18c6(): \Viserio\Component\Filesystem\Filesystem
    {
        return $this->services[\Viserio\Contract\Filesystem\Filesystem::class] = new \Viserio\Component\Filesystem\Filesystem();
    }

    /**
     * Returns the public Viserio\Contract\View\EngineResolver service.
     *
     * @return \Viserio\Component\View\Engine\IteratorViewEngineLoader
     */
    protected function getff73b374d52eb13f0773f58273de538d62944effed5c8e2a7caa31b6d2180e5b(): \Viserio\Component\View\Engine\IteratorViewEngineLoader
    {
        return new \Viserio\Component\View\Engine\IteratorViewEngineLoader(new \Viserio\Component\Container\RewindableGenerator(function () {
            yield 'file' => ($this->services[\Viserio\Component\View\Engine\FileEngine::class] ?? $this->get1a6c73ea96b910b7c8cc9eccb8a2efd9908e964162cfa207eeef703514994b04());

            yield 'php' => ($this->services[\Viserio\Component\View\Engine\PhpEngine::class] ?? $this->getd01d428260fd74997b150257850eb533cb546f9df452b1033a6c2fae22551a64());

            yield 'twig' => ($this->services[\Viserio\Provider\Twig\Engine\TwigEngine::class] ?? $this->get60c8946331b96312aa7aa623435675475698643b927851fb2234c12cd9374ea5());

            yield 'html.twig' => ($this->services[\Viserio\Provider\Twig\Engine\TwigEngine::class] ?? $this->get60c8946331b96312aa7aa623435675475698643b927851fb2234c12cd9374ea5());
        }, 4));
    }

    /**
     * Returns the public Viserio\Contract\View\Factory shared service.
     *
     * @return \Viserio\Component\View\ViewFactory
     */
    protected function getb6b69b8c40146f590f39a23a8d000f621d6288e79e53d4fdbeae0164b92c7d63(): \Viserio\Component\View\ViewFactory
    {
        $this->services[\Viserio\Contract\View\Factory::class] = $instance = new \Viserio\Component\View\ViewFactory(new \Viserio\Component\View\Engine\IteratorViewEngineLoader(new \Viserio\Component\Container\RewindableGenerator(function () {
            yield 'file' => ($this->services[\Viserio\Component\View\Engine\FileEngine::class] ?? $this->get1a6c73ea96b910b7c8cc9eccb8a2efd9908e964162cfa207eeef703514994b04());

            yield 'php' => ($this->services[\Viserio\Component\View\Engine\PhpEngine::class] ?? $this->getd01d428260fd74997b150257850eb533cb546f9df452b1033a6c2fae22551a64());

            yield 'twig' => ($this->services[\Viserio\Provider\Twig\Engine\TwigEngine::class] ?? $this->get60c8946331b96312aa7aa623435675475698643b927851fb2234c12cd9374ea5());

            yield 'html.twig' => ($this->services[\Viserio\Provider\Twig\Engine\TwigEngine::class] ?? $this->get60c8946331b96312aa7aa623435675475698643b927851fb2234c12cd9374ea5());
        }, 4)), ($this->services[\Viserio\Contract\View\Finder::class] ?? $this->get2bc187b27da90a561b690d6fd54a3000d8276c6e5b8e8167ce59a4b9355b3859()));

        $instance->share('app', $this);
        $instance->addExtension('twig', 'twig');

        return $instance;
    }

    /**
     * Returns the public Viserio\Contract\View\Finder shared service.
     *
     * @return \Viserio\Component\View\ViewFinder
     */
    protected function get2bc187b27da90a561b690d6fd54a3000d8276c6e5b8e8167ce59a4b9355b3859(): \Viserio\Component\View\ViewFinder
    {
        return $this->services[\Viserio\Contract\View\Finder::class] = new \Viserio\Component\View\ViewFinder([
            'viserio' => [
                'view' => [
                    'paths' => [
                        0 => ($this->targetDirs[2] . '/Fixture/'),
                        1 => $this->targetDirs[1],
                    ],
                    'engines' => [
                        'twig' => [
                            'options' => [
                                'debug' => true,
                                'cache' => '',
                            ],
                            'file_extension' => 'html',
                            'templates' => [
                                'test.html' => 'tests',
                            ],
                            'loaders' => [
                                0 => new \Twig\Loader\ArrayLoader(),
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Returns the public Viserio\Component\OptionsResolver\Command\OptionDumpCommand shared service.
     *
     * @return \Viserio\Component\OptionsResolver\Command\OptionDumpCommand
     */
    protected function get5a73c93dbe469f9f1fae0210ee64ef2ab32ed536467d0570a89353766859bb62(): \Viserio\Component\OptionsResolver\Command\OptionDumpCommand
    {
        return $this->services[\Viserio\Component\OptionsResolver\Command\OptionDumpCommand::class] = new \Viserio\Component\OptionsResolver\Command\OptionDumpCommand();
    }

    /**
     * Returns the public Viserio\Component\OptionsResolver\Command\OptionReaderCommand shared service.
     *
     * @return \Viserio\Component\OptionsResolver\Command\OptionReaderCommand
     */
    protected function get51bc2cdf2d87fcaa6a89ede54bc023ccfe784ddb4cc7a7e2be4ab3a7e9204471(): \Viserio\Component\OptionsResolver\Command\OptionReaderCommand
    {
        return $this->services[\Viserio\Component\OptionsResolver\Command\OptionReaderCommand::class] = new \Viserio\Component\OptionsResolver\Command\OptionReaderCommand();
    }

    /**
     * Returns the public Viserio\Component\View\Engine\FileEngine shared service.
     *
     * @return \Viserio\Component\View\Engine\FileEngine
     */
    protected function get1a6c73ea96b910b7c8cc9eccb8a2efd9908e964162cfa207eeef703514994b04(): \Viserio\Component\View\Engine\FileEngine
    {
        return $this->services[\Viserio\Component\View\Engine\FileEngine::class] = new \Viserio\Component\View\Engine\FileEngine();
    }

    /**
     * Returns the public Viserio\Component\View\Engine\PhpEngine shared service.
     *
     * @return \Viserio\Component\View\Engine\PhpEngine
     */
    protected function getd01d428260fd74997b150257850eb533cb546f9df452b1033a6c2fae22551a64(): \Viserio\Component\View\Engine\PhpEngine
    {
        return $this->services[\Viserio\Component\View\Engine\PhpEngine::class] = new \Viserio\Component\View\Engine\PhpEngine();
    }

    /**
     * Returns the public Viserio\Provider\Twig\Command\CleanCommand shared service.
     *
     * @return \Viserio\Provider\Twig\Command\CleanCommand
     */
    protected function getb27fd541062d323461cd1a120541021d75cbc7d2bbae5eefde976856c43fbebe(): \Viserio\Provider\Twig\Command\CleanCommand
    {
        return $this->services[\Viserio\Provider\Twig\Command\CleanCommand::class] = new \Viserio\Provider\Twig\Command\CleanCommand([
            'viserio' => [
                'view' => [
                    'paths' => [
                        0 => ($this->targetDirs[2] . '/Fixture/'),
                        1 => $this->targetDirs[1],
                    ],
                    'engines' => [
                        'twig' => [
                            'options' => [
                                'debug' => true,
                                'cache' => '',
                            ],
                            'file_extension' => 'html',
                            'templates' => [
                                'test.html' => 'tests',
                            ],
                            'loaders' => [
                                0 => new \Twig\Loader\ArrayLoader(),
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Returns the public Viserio\Provider\Twig\Engine\TwigEngine shared service.
     *
     * @return \Viserio\Provider\Twig\Engine\TwigEngine
     */
    protected function get60c8946331b96312aa7aa623435675475698643b927851fb2234c12cd9374ea5(): \Viserio\Provider\Twig\Engine\TwigEngine
    {
        $this->services[\Viserio\Provider\Twig\Engine\TwigEngine::class] = $instance = new \Viserio\Provider\Twig\Engine\TwigEngine(($this->services[\Twig\Environment::class] ?? $this->get8d9ff7a81f29c15daa148bf17605b436a0f079af45d4fe0663dc18bfb7cc9691()), [
            'viserio' => [
                'view' => [
                    'paths' => [
                        0 => ($this->targetDirs[2] . '/Fixture/'),
                        1 => $this->targetDirs[1],
                    ],
                    'engines' => [
                        'twig' => [
                            'options' => [
                                'debug' => true,
                                'cache' => '',
                            ],
                            'file_extension' => 'html',
                            'templates' => [
                                'test.html' => 'tests',
                            ],
                            'loaders' => [
                                0 => new \Twig\Loader\ArrayLoader(),
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $instance->setContainer($this);

        return $instance;
    }

    /**
     * Returns the public Viserio\Provider\Twig\Loader shared service.
     *
     * @return \Viserio\Provider\Twig\Loader
     */
    protected function get917a35e00325c34c4098b810e960473b05e7514c36627ea7d0456564e774bd56(): \Viserio\Provider\Twig\Loader
    {
        $this->services[\Viserio\Provider\Twig\Loader::class] = $instance = new \Viserio\Provider\Twig\Loader(($this->services[\Viserio\Contract\View\Finder::class] ?? $this->get2bc187b27da90a561b690d6fd54a3000d8276c6e5b8e8167ce59a4b9355b3859()), ($this->services[\Viserio\Contract\Filesystem\Filesystem::class] ?? $this->get787a79c940e7519357cde00935d657a813a0ce7c7b555cf3cb98d8c2263d18c6()));

        $instance->setExtension('html');

        return $instance;
    }

    /**
     * Returns the public config service.
     *
     * @return array
     */
    protected function get34bcaa5afa8745d92e6161e8495be3b939c5c6abb4dc2fd1f5a3cfdaba620256(): array
    {
        return [
            'viserio' => [
                'view' => [
                    'paths' => [
                        0 => ($this->targetDirs[2] . '/Fixture/'),
                        1 => $this->targetDirs[1],
                    ],
                    'engines' => [
                        'twig' => [
                            'options' => [
                                'debug' => true,
                                'cache' => '',
                            ],
                            'file_extension' => 'html',
                            'templates' => [
                                'test.html' => 'tests',
                            ],
                            'loaders' => [
                                0 => new \Twig\Loader\ArrayLoader(),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRemovedIds(): array
    {
        return require $this->containerDir . '/removed-ids.php';
    }
}