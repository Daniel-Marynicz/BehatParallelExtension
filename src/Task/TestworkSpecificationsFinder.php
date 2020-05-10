<?php

namespace DMarynicz\BehatParallelExtension\Task;

use Behat\Testwork\Specification\GroupedSpecificationIterator;

interface TestworkSpecificationsFinder
{
    /**
     * @param string|null $path
     *
     * @return GroupedSpecificationIterator[]
     */
    public function findGroupedSpecifications($path);
}
