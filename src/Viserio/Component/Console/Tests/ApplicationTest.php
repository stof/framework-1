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

namespace Viserio\Component\Console\Tests;

use Error;
use Exception;
use LogicException;
use Mockery;
use Narrowspark\TestingHelper\ArrayContainer;
use Narrowspark\TestingHelper\Phpunit\MockeryTestCase;
use PHPUnit\Framework\Assert;
use RuntimeException;
use stdClass;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Process\PhpExecutableFinder;
use Viserio\Component\Console\Application;
use Viserio\Component\Console\ConsoleEvents;
use Viserio\Component\Console\Event\ConsoleCommandEvent;
use Viserio\Component\Console\Event\ConsoleErrorEvent;
use Viserio\Component\Console\Event\ConsoleTerminateEvent;
use Viserio\Component\Console\Output\SpyOutput;
use Viserio\Component\Console\Tests\Fixture\FooCommand;
use Viserio\Component\Console\Tests\Fixture\HyperlinkCommand;
use Viserio\Component\Console\Tests\Fixture\ViserioCommand;
use Viserio\Component\Events\EventManager;
use Viserio\Contract\Console\Exception\InvalidArgumentException;
use Viserio\Contract\Console\Exception\InvocationException;
use Viserio\Contract\Events\EventManager as EventManagerContract;

/**
 * Some code in this class it taken from silly.
 *
 * @author Matthieu Napoli https://github.com/mnapoli
 * @copyright Copyright (c) Matthieu Napoli
 *
 * @internal
 *
 * @small
 */
final class ApplicationTest extends MockeryTestCase
{
    /** @var \Viserio\Component\Console\Application */
    private $application;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->application = new Application();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($_ENV['SHELL_VERBOSITY'], $_GET['SHELL_VERBOSITY'], $_SERVER['SHELL_VERBOSITY']);
    }

    public function testBootstrappers(): void
    {
        $_SERVER['ConsoleStarting'] = 0;

        Application::starting(function (): void {
            $_SERVER['ConsoleStarting'] = 1;
        });

        new Application('1.0.0');

        self::assertSame(1, $_SERVER['ConsoleStarting']);

        Application::starting(function (): void {
            $_SERVER['ConsoleStarting'] = 2;
        });

        Application::clearBootstrappers();

        new Application('1.0.0');

        self::assertSame(1, $_SERVER['ConsoleStarting']);

        unset($_SERVER['ConsoleStarting']);
    }

    public function testAllowsToDefineViserioCommand(): void
    {
        $command = $this->application->add(new ViserioCommand());

        self::assertSame($command, $this->application->get('demo:hallo'));
    }

    public function testAllowsToDefineCommands(): void
    {
        $command = $this->application->command('foo', function () {
            return 1;
        });

        self::assertSame($command, $this->application->get('foo'));
    }

    public function testAllowsToDefineDefaultValues(): void
    {
        $this->application->command('greet [firstname] [lastname]', function ($firstname, $lastname, Outputinterface $output): void {
        });
        $this->application->defaults('greet', [
            'firstname' => 'John',
            'lastname' => 'Doe',
        ]);

        $definition = $this->application->get('greet')->getDefinition();

        self::assertEquals('John', $definition->getArgument('firstname')->getDefault());
        self::assertEquals('Doe', $definition->getArgument('lastname')->getDefault());
    }

    public function testItShouldRunSimpleCommand(): void
    {
        $this->application->command('greet', function (OutputInterface $output): void {
            $output->write('hello');
        });

        $this->assertOutputIs('greet', 'hello');
    }

    public function testItShouldRunSimpleCommandWithEvents(): void
    {
        $event = Mockery::mock(EventManagerContract::class);
        $event->shouldReceive('trigger')
            ->twice();

        $this->application->setEventManager($event);

        $this->application->command('greet', function (OutputInterface $output): void {
            $output->write('hello');
        });

        $this->assertOutputIs('greet', 'hello');
    }

    public function testItShouldRunACommandWithAnArgument(): void
    {
        $this->application->command('greet name', function ($name, OutputInterface $output): void {
            $output->write('hello ' . $name);
        });

        $this->assertOutputIs('greet john', 'hello john');
    }

    public function testItShouldRunACommandWithAnOptionalArgument(): void
    {
        $this->application->command('greet [name]', function ($name, OutputInterface $output): void {
            $output->write('hello ' . $name);
        });

        $this->assertOutputIs('greet', 'hello ');
        $this->assertOutputIs('greet john', 'hello john');
    }

    public function testItShouldRunACommandWithAFlag(): void
    {
        $this->application->command('greet [-y|--yell]', function ($yell, OutputInterface $output): void {
            $output->write(\var_export($yell, true));
        });

        $this->assertOutputIs('greet', 'false');
        $this->assertOutputIs('greet -y', 'true');
        $this->assertOutputIs('greet --yell', 'true');
    }

    public function testItShouldRunACommandWithAnOption(): void
    {
        $this->application->command('greet [-i|--iterations=]', function ($iterations, OutputInterface $output): void {
            $output->write($iterations ?? 'null');
        });

        $this->assertOutputIs('greet', 'null');
        $this->assertOutputIs('greet -i 123', '123');
        $this->assertOutputIs('greet --iterations=123', '123');
    }

    public function testItShouldRunACommandWitMultipleOptions(): void
    {
        $this->application->command('greet [-d|--dir=*]', function ($dir, OutputInterface $output): void {
            $output->write('[' . \implode(', ', $dir) . ']');
        });

        $this->assertOutputIs('greet', '[]');
        $this->assertOutputIs('greet -d foo', '[foo]');
        $this->assertOutputIs('greet -d foo -d bar', '[foo, bar]');
        $this->assertOutputIs('greet --dir=foo --dir=bar', '[foo, bar]');
    }

    public function testItShouldInjectTypeHintInPriority(): void
    {
        $stdClass = new stdClass();
        $stdClass->foo = 'hello';
        $stdClass2 = new stdClass();
        $stdClass2->foo = 'nope!';

        $container = new ArrayContainer([
            stdClass::class => $stdClass,
            'param' => $stdClass2,
        ]);

        $this->application->setContainer($container);
        $this->application->command('greet', function (OutputInterface $output, stdClass $param): void {
            $output->write($param->foo);
        });

        $this->assertOutputIs('greet', 'hello');
    }

    public function testItCanResolveCallableStringFromContainer(): void
    {
        $container = new ArrayContainer([
            'command.greet' => function (OutputInterface $output): void {
                $output->write('hello');
            },
        ]);

        $this->application->setContainer($container);
        $this->application->command('greet', 'command.greet');

        $this->assertOutputIs('greet', 'hello');
    }

    public function testItCanResolveCallableArrayFromContainer(): void
    {
        $container = new ArrayContainer([
            'command.arr.greet' => [$this, 'foo'],
        ]);

        $this->application->setContainer($container);
        $this->application->command('greet', 'command.arr.greet');

        $this->assertOutputIs('greet', 'hello');
    }

    public function testItCanInjectUsingTypeHints(): void
    {
        $stdClass = new stdClass();
        $stdClass->foo = 'hello';

        $container = new ArrayContainer([
            'stdClass' => $stdClass,
        ]);

        $this->application->setContainer($container);
        $this->application->command('greet', function (OutputInterface $output, stdClass $stdClass): void {
            $output->write($stdClass->foo);
        });

        $this->assertOutputIs('greet', 'hello');
    }

    public function testItCanInjectUsingParameterNames(): void
    {
        $stdClass = new stdClass();
        $stdClass->foo = 'hello';

        $container = new ArrayContainer([
            'stdClass' => $stdClass,
        ]);

        $this->application->setContainer($container);
        $this->application->command('greet', function (OutputInterface $output, $stdClass): void {
            $output->write($stdClass->foo);
        });

        $this->assertOutputIs('greet', 'hello');
    }

    public function testItShouldMatchHyphenatedArgumentsToLowercaseParameters(): void
    {
        $this->application->command('greet first-name', function ($firstname, OutputInterface $output): void {
            $output->write('hello ' . $firstname);
        });

        $this->assertOutputIs('greet john', 'hello john');
    }

    public function testItShouldMatchHyphenatedArgumentsToMixedCaseParameters(): void
    {
        $this->application->command('greet first-name', function ($firstName, OutputInterface $output): void {
            $output->write('hello ' . $firstName);
        });

        $this->assertOutputIs('greet john', 'hello john');
    }

    public function testItShouldMatchHyphenatedOptionToLowercaseParameters(): void
    {
        $this->application->command('greet [--yell-louder]', function ($yelllouder, OutputInterface $output): void {
            $output->write(\var_export($yelllouder, true));
        });

        $this->assertOutputIs('greet', 'false');
        $this->assertOutputIs('greet --yell-louder', 'true');
    }

    public function testItShouldMatchHyphenatedOptionToMixedCaseParameters(): void
    {
        $this->application->command('greet [--yell-louder]', function ($yellLouder, OutputInterface $output): void {
            $output->write(\var_export($yellLouder, true));
        });

        $this->assertOutputIs('greet', 'false');
        $this->assertOutputIs('greet --yell-louder', 'true');
    }

    public function testItShouldThrowIfAParameterCannotBeResolved(): void
    {
        $this->expectException(InvocationException::class);
        $this->expectExceptionMessage('Impossible to call the \'greet\' command: Unable to invoke the callable because no value was given for parameter 1 ($fbo)');

        $this->application->command('greet', function ($fbo): void {
        });

        $this->assertOutputIs('greet', '');
    }

    public function testRunsACommandViaItsAliasAndReturnsExitCode(): void
    {
        $this->application->command('foo', function ($output): void {
            $output->write(1);
        }, ['bar']);

        $this->assertOutputIs('bar', 1);
    }

    public function testitShouldRunACommandInTheScopeOfTheApplication(): void
    {
        $whatIsThis = null;

        $this->application->command('foo', function () use (&$whatIsThis): void {
            $whatIsThis = $this;
        });

        $this->assertOutputIs('foo', '');
        self::assertSame($this->application, $whatIsThis);
    }

    public function testItCanRunASingleCommandApplication(): void
    {
        $this->application->command('run', function (OutputInterface $output): void {
            $output->write('hello');
        });

        $this->application->setDefaultCommand('run');

        $this->assertOutputIs('run', 'hello');
    }

    public function testItShouldThrowIfTheCommandIsNotACallable(): void
    {
        $this->expectException(InvocationException::class);
        $this->expectExceptionMessage('Impossible to call the \'greet\' command: \'foo\' is not a callable');

        $this->application->command('greet', 'foo');

        $this->assertOutputIs('greet', '');
    }

    public function testItCanRunAsASingleCommandApplication(): void
    {
        $this->application->command('run', function (OutputInterface $output): void {
            $output->write('hello');
        });
        $this->application->setDefaultCommand('run');

        $this->assertOutputIs('', 'hello');
    }

    public function testConsoleErrorEventIsTriggeredOnCommandNotFound(): void
    {
        $eventManager = new EventManager();
        $eventManager->attach(ConsoleEvents::ERROR, function (ConsoleErrorEvent $event): void {
            Assert::assertNull($event->getCommand());
            Assert::assertInstanceOf(CommandNotFoundException::class, $event->getError());

            $event->getOutput()->write('silenced command not found');
        });

        $this->application->setEventManager($eventManager);
        $this->application->setCatchExceptions(true);

        $tester = new ApplicationTester($this->application);
        $tester->run(['command' => 'unknown']);

        self::assertStringContainsString('silenced command not found', $tester->getDisplay());
        self::assertSame(1, $tester->getStatusCode());
    }

    public function testRunWithDispatcher(): void
    {
        $this->application->setEventManager($this->getDispatcher());
        $this->application->register('foo')->setCode(function (InputInterface $input, OutputInterface $output): void {
            $output->write('foo.');
        });

        $tester = new ApplicationTester($this->application);
        $tester->run(['command' => 'foo']);

        self::assertEquals('before.foo.after.' . \PHP_EOL, $tester->getDisplay());
    }

    public function testRunDispatchesAllEventsWithError(): void
    {
        $this->application->setEventManager($this->getDispatcher());
        $this->application->setCatchExceptions(true);

        $this->application->register('dym')->setCode(function (InputInterface $input, OutputInterface $output): void {
            $output->write('dym.');

            throw new Error('dymerr.');
        });

        $tester = new ApplicationTester($this->application);
        $tester->run(['command' => 'dym']);

        self::assertStringContainsString('before.dym.error.after.', $tester->getDisplay(), 'The PHP Error did not dispached events');
    }

    public function testRunWithErrorFailingStatusCode(): void
    {
        $this->application->setEventManager($this->getDispatcher());
        $this->application->setCatchExceptions(true);

        self::assertTrue($this->application->areExceptionsCaught());

        $this->application->register('dus')->setCode(function (InputInterface $input, OutputInterface $output): void {
            $output->write('dus.');

            throw new Error('duserr.');
        });

        $tester = new ApplicationTester($this->application);
        $tester->run(['command' => 'dus']);

        self::assertSame(1, $tester->getStatusCode(), 'Status code should be 1');
    }

    public function testRunWithError(): void
    {
        $this->application->setAutoExit(false);
        $this->application->setCatchExceptions(false);

        $this->application->register('dym')->setCode(function (InputInterface $input, OutputInterface $output): void {
            $output->write('dym.');

            throw new Error('dymerr.');
        });

        $tester = new ApplicationTester($this->application);

        try {
            $tester->run(['command' => 'dym']);
            self::fail('Error expected.');
        } catch (Error $e) {
            self::assertSame('dymerr.', $e->getMessage());
        }
    }

    public function testErrorIsRethrownIfNotHandledByConsoleErrorEvent(): void
    {
        $this->application->setEventManager(new EventManager());

        $this->application->register('dym')->setCode(function (InputInterface $input, OutputInterface $output): void {
            new \Viserio\Component\Console\Tests\UnknownClass();
        });

        $tester = new ApplicationTester($this->application);

        try {
            $tester->run(['command' => 'dym']);
            self::fail('->run() should rethrow PHP errors if not handled via ConsoleErrorEvent.');
        } catch (Error $e) {
            self::assertSame($e->getMessage(), 'Class \'Viserio\\Component\\Console\\Tests\\UnknownClass\' not found');
        }
    }

    public function testErrorIsRethrownIfNotHandledByConsoleErrorEventWithCatchingEnabled(): void
    {
        $this->application->setEventManager(new EventManager());
        $this->application->setCatchExceptions(true);

        $this->application->register('dym')->setCode(function (InputInterface $input, OutputInterface $output): void {
            new \Viserio\Component\Console\Tests\UnknownClass();
        });

        $tester = new ApplicationTester($this->application);

        try {
            $tester->run(['command' => 'dym']);
            self::fail('->run() should rethrow PHP errors if not handled via ConsoleErrorEvent.');
        } catch (Error $e) {
            self::assertSame($e->getMessage(), 'Class \'Viserio\\Component\\Console\\Tests\\UnknownClass\' not found');
        }
    }

    public function testRunWithDispatcherSkippingCommand(): void
    {
        $this->application->setEventManager($this->getDispatcher(true));
        $this->application->setCatchExceptions(true);

        $this->application->register('foo')->setCode(function (InputInterface $input, OutputInterface $output): void {
            $output->write('foo.');
        });

        $tester = new ApplicationTester($this->application);
        $exitCode = $tester->run(['command' => 'foo']);

        self::assertStringContainsString('before.after.', $tester->getDisplay());
        self::assertEquals(ConsoleCommandEvent::RETURN_CODE_DISABLED, $exitCode);
    }

    public function testRunWithDispatcherAccessingInputOptions(): void
    {
        $noInteractionValue = false;
        $quietValue = true;
        $dispatcher = $this->getDispatcher();
        $dispatcher->attach(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $event) use (&$noInteractionValue, &$quietValue): void {
            $input = $event->getInput();
            $noInteractionValue = $input->getOption('no-interaction');
            $quietValue = $input->getOption('quiet');
        });

        $this->application->setEventManager($dispatcher);
        $this->application->setCatchExceptions(true);

        $this->application->register('foo')->setCode(function (InputInterface $input, OutputInterface $output): void {
            $output->write('foo.');
        });

        $tester = new ApplicationTester($this->application);
        $tester->run(['command' => 'foo', '--no-interaction' => true]);

        self::assertTrue($noInteractionValue);
        self::assertFalse($quietValue);
    }

    public function testRunWithExceptionAndDispatcher(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('error');

        $application = new Application();
        $application->setEventManager($this->getDispatcher());

        $application->register('foo')->setCode(function (InputInterface $input, OutputInterface $output): void {
            throw new RuntimeException('foo.');
        });

        $tester = new ApplicationTester($application);
        $tester->run(['command' => 'foo']);
    }

    public function testRunDispatchesAllEventsWithException(): void
    {
        $this->application->setEventManager($this->getDispatcher());
        $this->application->setCatchExceptions(true);

        $this->application->register('foo')->setCode(function (InputInterface $input, OutputInterface $output): void {
            $output->write('foo.');

            throw new RuntimeException('foo.');
        });

        $tester = new ApplicationTester($this->application);
        $tester->run(['command' => 'foo']);

        self::assertStringContainsString('before.foo.error.after.', $tester->getDisplay());
    }

    public function testRunWithDispatcherAddingInputOptions(): void
    {
        $extraValue = null;
        $dispatcher = $this->getDispatcher();

        $dispatcher->attach(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $event) use (&$extraValue): void {
            $definition = $event->getCommand()->getDefinition();
            $input = $event->getInput();

            $definition->addOption(new InputOption('extra', null, InputOption::VALUE_REQUIRED));
            $input->bind($definition);

            $extraValue = $input->getOption('extra');
        });

        $this->application->setEventManager($dispatcher);
        $this->application->register('foo')->setCode(function (InputInterface $input, OutputInterface $output): void {
            $output->write('foo.');
        });

        $tester = new ApplicationTester($this->application);
        $tester->run(['command' => 'foo', '--extra' => 'some test value']);

        self::assertEquals('some test value', $extraValue);
    }

    public function testRunDispatchesAllEventsWithExceptionInListener(): void
    {
        $dispatcher = $this->getDispatcher();
        $dispatcher->attach(ConsoleEvents::COMMAND, function (): void {
            throw new RuntimeException('foo.');
        });

        $this->application->setEventManager($dispatcher);
        $this->application->setCatchExceptions(true);

        $this->application->register('foo')->setCode(function (InputInterface $input, OutputInterface $output): void {
            $output->write('foo.');
        });

        $tester = new ApplicationTester($this->application);
        $tester->run(['command' => 'foo']);

        self::assertStringContainsString('before.error.after.', $tester->getDisplay());
    }

    public function testRunAllowsErrorListenersToSilenceTheException(): void
    {
        $dispatcher = $this->getDispatcher();
        $dispatcher->attach(ConsoleEvents::ERROR, function (ConsoleErrorEvent $event): void {
            $event->getOutput()->write('silenced.');
            $event->setExitCode(0);
        });
        $dispatcher->attach(ConsoleEvents::COMMAND, function (): void {
            throw new RuntimeException('foo.');
        });

        $this->application->setEventManager($dispatcher);
        $this->application->register('foo')->setCode(function (InputInterface $input, OutputInterface $output): void {
            $output->write('foo.');
        });

        $tester = new ApplicationTester($this->application);
        $tester->run(['command' => 'foo']);

        self::assertStringContainsString('before.error.silenced.after.', $tester->getDisplay());
        self::assertEquals(ConsoleCommandEvent::RETURN_CODE_DISABLED, $tester->getStatusCode());
    }

    public function testRunReturnsIntegerExitCode(): void
    {
        /** @var Application|\Mockery\MockInterface $application */
        $application = Mockery::mock(Application::class . '[doRun]');
        $application->setCatchExceptions(true);
        $application->shouldReceive('doRun')
            ->andThrow(new RuntimeException('', 4));

        $exitCode = $application->run(new ArrayInput([]), new NullOutput());

        self::assertSame(4, $exitCode, '->run() returns integer exit code extracted from raised exception');
    }

    public function testCerebroBinary(): void
    {
        self::assertSame(\defined('CEREBRO_BINARY') ? '\'cerebro\'' : 'cerebro', Application::cerebroBinary());
    }

    public function testPhpBinary(): void
    {
        $finder = (new PhpExecutableFinder())->find(false);
        $php = \escapeshellarg($finder === false ? '' : $finder);

        self::assertSame($php, Application::phpBinary());
    }

    public function testFormatCommandString(): void
    {
        $finder = (new PhpExecutableFinder())->find(false);
        $php = \escapeshellarg($finder === false ? '' : $finder);

        self::assertSame($php . ' ' . (\defined('CEREBRO_BINARY') ? '\'cerebro\'' : 'cerebro') . ' command.greet', Application::formatCommandString('command.greet'));
    }

    public function testShouldInjectTheSymfonyStyleObject(): void
    {
        $this->application->command('greet', function (SymfonyStyle $io): void {
            $io->write('hello');
        });

        $this->assertOutputIs('greet', 'hello');
    }

    public function testItShouldInjectTheOutputAndInputByName(): void
    {
        $this->application->command('greet name', function ($output, $input): void {
            $output->write('hello ' . $input->getArgument('name'));
        });
        $this->assertOutputIs('greet john', 'hello john');
    }

    public function testShouldInjectTheOutputAndInputByNameEvenIfAServiceHasTheSameName(): void
    {
        $container = new ArrayContainer([
            'input' => 'foo',
            'output' => 'bar',
        ]);

        $this->application->setContainer($container);
        $this->application->command('greet name', function ($output, $input): void {
            $output->write('hello ' . $input->getArgument('name'));
        });

        $this->assertOutputIs('greet john', 'hello john');
    }

    public function testShouldInjectTheOutputAndInputByTypeHintOnInterfaces(): void
    {
        $this->application->command('greet name', function (OutputInterface $out, InputInterface $in): void {
            $out->write('hello ' . $in->getArgument('name'));
        });

        $this->assertOutputIs('greet john', 'hello john');
    }

    public function testShouldInjectTheOutputAndInputByTypeHintOnClasses(): void
    {
        $this->application->command('greet name', function (Output $out, Input $in): void {
            $out->write('hello ' . $in->getArgument('name'));
        });

        $this->assertOutputIs('greet john', 'hello john');
    }

    public function testShouldInjectTheOutputAndInputByTypeHintEvenIfAServiceHasTheSameName(): void
    {
        $container = new ArrayContainer([
            'in' => 'foo',
            'out' => 'bar',
        ]);

        $this->application->setContainer($container);
        $this->application->command('greet name', function (OutputInterface $out, InputInterface $in): void {
            $out->write('hello ' . $in->getArgument('name'));
        });

        $this->assertOutputIs('greet john', 'hello john');
    }

    public function testItShouldRunASubcommand(): void
    {
        $this->application->command('foo', function (OutputInterface $output): void {
            $output->write('hello');
        });

        $this->application->command('bar', function (OutputInterface $output): void {
            $this->call('foo', [], $output);

            $output->write(' world');
        });

        $this->assertOutputIs('bar', 'hello world');
    }

    public function testGetLastOutput(): void
    {
        $this->application->command('foo', function (OutputInterface $output): void {
            $output->write('hello');
        });

        self::assertSame('', $this->application->getLastOutput());

        $this->application->call('foo');

        self::assertSame('hello', $this->application->getLastOutput());
    }

    public function testCallUsingCommandName(): void
    {
        $this->application->add(new FooCommand());

        $exitCode = $this->application->call('foo:bar', ['id' => 1]);

        self::assertEquals($exitCode, 0);
    }

    public function testCallUsingCommandClass(): void
    {
        $this->application->add(new FooCommand());

        $exitCode = $this->application->call(FooCommand::class, ['id' => 1]);

        self::assertEquals($exitCode, 0);
    }

    public function testCallInvalidCommandName(): void
    {
        $this->expectException(CommandNotFoundException::class);

        $this->application->call('foo:bars');
    }

    public function testAllowsDefaultValuesToBeInferredFromCamelCaseParameters(): void
    {
        $command = $this->application->command('greet [name] [--yell] [--number-of-times=]', function ($numberOfTimes = 15): void {
        });

        $definition = $command->getDefinition();

        self::assertEquals(15, $definition->getOption('number-of-times')->getDefault());
    }

    public function testThrowingErrorListener(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('foo');

        $dispatcher = $this->getDispatcher();
        $dispatcher->attach(ConsoleEvents::ERROR, function (ConsoleErrorEvent $event): void {
            throw new RuntimeException('foo.');
        });

        $dispatcher->attach(ConsoleEvents::COMMAND, function (): void {
            throw new RuntimeException('bar.');
        });

        $application = new Application();
        $application->setEventManager($dispatcher);

        $application->register('foo')->setCode(function (InputInterface $input, OutputInterface $output): void {
            $output->write('foo.');
        });

        $tester = new ApplicationTester($application);
        $tester->run(['command' => 'foo']);
    }

    public function testItShouldThrowIfTheCommandIsAMethodCallToAStaticMethod(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('[\'Viserio\\Component\\Console\\Tests\\ApplicationTest\', \'foo\'] is not a callable because \'foo\' is a static method. Either use [new Viserio\\Component\\Console\\Tests\\ApplicationTest(), \'foo\'] or configure a dependency injection container that supports autowiring.');

        $this->application->command('greet', [__CLASS__, 'foo']);
        $this->assertOutputIs('greet', '');
    }

    public function testArtisanWithHyperlink(): void
    {
        $this->application->add(new HyperlinkCommand());

        $this->assertOutputIs('foo:hyperlink', "Narrowspark\n");

        $this->assertOutputIs('foo:hyperlink --ansi', "\033]8;;https://narrowspark.com\033\\Narrowspark\033]8;;\033\\\n");

        $this->assertOutputIs('foo:hyperlink --no-ansi', "Narrowspark\n");
    }

    /**
     * Fixture method.
     *
     * @param OutputInterface $output
     */
    public function foo(OutputInterface $output): void
    {
        $output->write('hello');
    }

    /**
     * {@inheritdoc}
     */
    protected function allowMockingNonExistentMethods(bool $allow = false): void
    {
        parent::allowMockingNonExistentMethods(true);
    }

    /**
     * @param bool $skipCommand
     *
     * @return \Viserio\Contract\Events\EventManager
     */
    private function getDispatcher(bool $skipCommand = false): EventManagerContract
    {
        $dispatcher = new EventManager();

        $dispatcher->attach(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $event) use ($skipCommand): void {
            $event->getOutput()->write('before.');

            if ($skipCommand) {
                $event->disableCommand();
            }
        });

        $dispatcher->attach(ConsoleEvents::TERMINATE, function (ConsoleTerminateEvent $event) use ($skipCommand): void {
            $event->getOutput()->writeln('after.');

            if (! $skipCommand) {
                $event->setExitCode(ConsoleCommandEvent::RETURN_CODE_DISABLED);
            }
        });

        $dispatcher->attach(ConsoleEvents::ERROR, function (ConsoleErrorEvent $event): void {
            $event->getOutput()->write('error.');
            $event->setError(new LogicException('error.', $event->getExitCode(), $event->getError()));
        });

        return $dispatcher;
    }

    /**
     * @param string     $command
     * @param int|string $expected
     *
     * @throws Exception
     *
     * @return void
     */
    private function assertOutputIs($command, $expected): void
    {
        $output = new SpyOutput();

        $this->application->run(new StringInput($command), $output);

        self::assertEquals($expected, $output->output);
    }
}
