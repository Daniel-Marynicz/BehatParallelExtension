<?php

namespace DMarynicz\BehatParallelExtension\Task;

use Behat\Testwork\Specification\GroupedSpecificationIterator;

interface TestworkSpecificationsFinder
{
    /**
     * @param string|null $path
     *
     * @return array<string, GroupedSpecificationIterator<mixed>>
     */
    public function findGroupedSpecifications($path);
}
