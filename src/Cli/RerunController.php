<?php

namespace DMarynicz\BehatParallelExtension\Cli;

use Behat\Gherkin\Node\ScenarioLikeInterface;
use Behat\Testwork\Cli\Controller;
use DMarynicz\BehatParallelExtension\Event\AfterTaskTested;
use DMarynicz\BehatParallelExtension\Event\ParallelTestCompleted;
use DMarynicz\BehatParallelExtension\Exception\UnexpectedValue;
use DMarynicz\BehatParallelExtension\Service\FilePutContentsWrapper;
use DMarynicz\BehatParallelExtension\Util\Assert;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;

class RerunController implements Controller
{
    const SERVICE_ID = 'cli.controller.parallel_extension.re_run_controller';

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var string|null */
    private $cachePath;

    /** @var string */
    private $key;

    /** @var array<string, array<string>> */
    private $lines = [];

    /** @var string */
    private $basepath;

    /** @var JsonEncode */
    private $jsonEncode;

    /** @var FilePutContentsWrapper */
    private $filePutContents;

    /**
     * @param string|null $cachePath
     * @param string      $basepath
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        JsonEncode $jsonEncode,
        FilePutContentsWrapper $filePutContents,
        $cachePath,
        $basepath
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->jsonEncode      = $jsonEncode;
        $this->filePutContents = $filePutContents;
        $this->cachePath       = $cachePath !== null ? rtrim($cachePath, DIRECTORY_SEPARATOR) : null;
        $this->basepath        = $basepath;
    }

    /**
     * {@inheritDoc}
     */
    public function configure(Command $command)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->eventDispatcher->addListener(
            AfterTaskTested::AFTER,
            [$this, 'collectFailedTask']
        );
        $this->eventDispatcher->addListener(ParallelTestCompleted::COMPLETED, [$this, 'writeCache']);
        $this->key = $this->generateKey($input);

        return null;
    }

    /**
     * Records task if it is failed.
     */
    public function collectFailedTask(AfterTaskTested $taskTested)
    {
        $process = $taskTested->getProcess();
        if ($process->isSuccessful()) {
            return;
        }

        if (! $this->getFileName()) {
            return;
        }

        $suiteName    = $taskTested->getTask()->getSuite()->getName();
        $featureFile  = $taskTested->getTask()->getFeature()->getFile();
        $scenarioLine = null;
        $scenario     = $taskTested->getTask()->getScenario();
        if ($scenario instanceof ScenarioLikeInterface) {
            $scenarioLine = $scenario->getLine();
        }

        $line = $featureFile;
        if ($scenarioLine) {
            $line .= ':' . $scenarioLine;
        }

        $this->lines[$suiteName][] = $line;
    }

    /**
     * Writes failed tests cache.
     */
    public function writeCache()
    {
        $fileName = $this->getFileName();
        if (! $fileName) {
            return;
        }

        if (file_exists($fileName)) {
            unlink($fileName);
        }

        if (count($this->lines) === 0) {
            return;
        }

        $encoded = $this->jsonEncode->encode($this->lines, 'json');
        if (! is_string($encoded)) {
            throw new UnexpectedValue('Expected string');
        }

        $this->filePutContents->filePutContents(
            $fileName,
            $encoded
        );
    }

    /**
     * Generates cache key.
     *
     * @return string
     */
    private function generateKey(InputInterface $input)
    {
        return md5(
            Assert::assertString($input->getParameterOption(['--profile', '-p'])) .
            Assert::assertString($input->getOption('suite')) .
            implode(' ', Assert::assertArray($input->getOption('name'))) .
            implode(' ', Assert::assertArray($input->getOption('tags'))) .
            Assert::assertString($input->getOption('role')) .
            Assert::assertString($input->getArgument('paths')) .
            $this->basepath
        );
    }

    /**
     * Returns cache filename (if exists).
     *
     * @return string|null
     */
    private function getFileName()
    {
        if ($this->cachePath === null || $this->key === null) {
            return null;
        }

        if (! is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777);
        }

        return $this->cachePath . DIRECTORY_SEPARATOR . $this->key . '.rerun';
    }
}
