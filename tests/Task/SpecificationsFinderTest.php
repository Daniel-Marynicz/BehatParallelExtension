<?php

namespace DMarynicz\Tests\Task;

use Behat\Testwork\Specification\SpecificationFinder as TestworkSpecificationFinder;
use Behat\Testwork\Specification\SpecificationIterator;
use Behat\Testwork\Suite\Suite;
use Behat\Testwork\Suite\SuiteRepository;
use DG\BypassFinals;
use DMarynicz\BehatParallelExtension\Task\SpecificationsFinder;
use PHPUnit\Framework\TestCase;

class SpecificationsFinderTest extends TestCase
{
    protected function setUp(): void
    {
        BypassFinals::enable();
    }

    public function testFindGroupedSpecifications(): void
    {
        $suiteRepository = $this->createMock(SuiteRepository::class);
        /** @phpstan-ignore-next-line */
        $testworkFinder = $this->createMock(TestworkSpecificationFinder::class);
        $suite          = $this->createMock(Suite::class);
        $suiteIterator  = $this->createMock(SpecificationIterator::class);

        $suite->method('getName')
            ->willReturn('some-name');

        $suiteRepository
            ->method('getSuites')
            ->willReturn([$suite]);
        $suiteIterator->method('getSuite')
            ->willReturn($suite);
        $testworkFinder
            ->method('findSuitesSpecifications')
            ->willReturn([$suiteIterator]);

        $finder = new SpecificationsFinder($suiteRepository, $testworkFinder);
        $result = $finder->findGroupedSpecifications('path');

        $this->assertArrayHasKey('some-name', $result);
        $this->assertCount(1, $result);
        $this->assertEquals($suite, $result['some-name']->getSuite());
    }
}
