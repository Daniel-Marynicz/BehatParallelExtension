<?php

namespace DMarynicz\BehatParallelExtension\Task;

use Behat\Testwork\Specification\GroupedSpecificationIterator;
use Behat\Testwork\Specification\SpecificationFinder as TestworkSpecificationFinder;
use Behat\Testwork\Specification\SpecificationIterator;
use Behat\Testwork\Suite\Suite;
use Behat\Testwork\Suite\SuiteRepository;

class SpecificationsFinder implements TestworkSpecificationsFinder
{
    /** @var SuiteRepository */
    private $suiteRepository;

    /** @var TestworkSpecificationFinder */
    private $specificationFinder;

    public function __construct(
        SuiteRepository $suiteRepository,
        TestworkSpecificationFinder $specificationFinder
    ) {
        $this->suiteRepository     = $suiteRepository;
        $this->specificationFinder = $specificationFinder;
    }

    /**
     * @param string|null $path
     *
     * @return GroupedSpecificationIterator[]
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function findGroupedSpecifications($path)
    {
        $specs = $this->findSpecifications($path);

        return GroupedSpecificationIterator::group($specs);
    }

    /**
     * Finds exercise specifications.
     *
     * @param string|null $path
     *
     * @return SpecificationIterator[]
     */
    private function findSpecifications($path)
    {
        return $this->findSuitesSpecifications($this->getAvailableSuites(), $path);
    }

    /**
     * Finds specification iterators for all provided suites using locator.
     *
     * @param Suite[]     $suites
     * @param string|null $locator
     *
     * @return SpecificationIterator[]
     */
    private function findSuitesSpecifications($suites, $locator)
    {
        return $this->specificationFinder->findSuitesSpecifications($suites, $locator);
    }

    /**
     * Returns all currently available suites.
     *
     * @return Suite[]
     */
    private function getAvailableSuites()
    {
        return $this->suiteRepository->getSuites();
    }
}
