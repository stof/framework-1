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

//
// use Narrowspark\TestingHelper\Phpunit\MockeryTestCase;
// use Psr\Http\Message\ServerRequestInterface;
// use Viserio\Component\Container\Container;
// use Viserio\Contract\Profiler\Profiler as ProfilerContract;
// use Viserio\Contract\Translation\Translator as TranslatorContract;
// use Viserio\Component\HttpFactory\Provider\HttpFactoryServiceProvider;
// use Viserio\Component\Profiler\Provider\ProfilerServiceProvider;
// use Viserio\Component\Translation\Formatter\IntlMessageFormatter;
// use Viserio\Component\Translation\MessageCatalogue;
// use Viserio\Component\Translation\Provider\TranslationDataCollectorServiceProvider;
// use Viserio\Component\Translation\Translator;
//
///**
// * @internal
// */
// final class TranslationDataCollectorServiceProviderTest extends MockeryTestCase
// {
//    public function testProvider(): void
//    {
//        $catalogue = new MessageCatalogue('en', [
//            'messages' => [
//                'foo' => 'bar',
//            ],
//        ]);
//
//        $catalogue->addFallbackCatalogue(new MessageCatalogue('fr', [
//            'messages' => [
//                'test' => 'bar',
//            ],
//        ]));
//
//        $container = new Container();
//        $container->bind(ServerRequestInterface::class, $this->getRequest());
//        $container->bind(TranslatorContract::class, new Translator(
//            $catalogue,
//            new IntlMessageFormatter()
//        ));
//        $container->register(new HttpFactoryServiceProvider());
//        $container->register(new ProfilerServiceProvider());
//        $container->register(new TranslationDataCollectorServiceProvider());
//
//        $container->bind('config', [
//            'viserio' => [
//                'profiler' => [
//                    'enable'    => true,
//                    'collector' => [
//                        'translation' => true,
//                    ],
//                ],
//            ],
//        ]);
//
//        $this->assertInstanceOf(ProfilerContract::class, $container->get(ProfilerContract::class));
//    }
//
//    public function testProviderProfilerIsNull(): void
//    {
//        $container = new Container();
//        $container->register(new TranslationDataCollectorServiceProvider());
//
//        $container->bind('config', [
//            'viserio' => [
//                'profiler' => [
//                    'enable'    => true,
//                    'collector' => [
//                        'translation' => true,
//                    ],
//                ],
//            ],
//        ]);
//
//        $this->assertNull($container->get(ProfilerContract::class));
//    }
//
//    /**
//     * @return \Mockery\MockInterface|\Psr\Http\Message\ServerRequestInterface
//     */
//    private function getRequest()
//    {
//        $request = \Mockery::mock(ServerRequestInterface::class);
//        $request->shouldReceive('getHeaderLine')
//            ->with('request_time_float')
//            ->andReturn(false);
//        $request->shouldReceive('getHeaderLine')
//            ->with('request_time')
//            ->andReturn(false);
//
//        return $request;
//    }
// }
