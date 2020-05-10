<?php

namespace DMarynicz\Tests;

use Iterator;
use PHPUnit\Framework\MockObject\MockObject;

trait MockIterator
{
    /**
     * @param MockObject|Iterator<mixed> $iterator
     * @param array<mixed>               $items
     * @param bool                       $includeCallsToKey
     */
    public function mockIteratorItems(Iterator $iterator, array $items, $includeCallsToKey = false)
    {
        $iterator->expects($this->at(0))->method('rewind');
        $counter = 1;
        foreach ($items as $k => $v) {
            $iterator->expects($this->at($counter++))->method('valid')->will($this->returnValue(true));
            $iterator->expects($this->at($counter++))->method('current')->will($this->returnValue($v));
            if ($includeCallsToKey) {
                $iterator->expects($this->at($counter++))->method('key')->will($this->returnValue($k));
            }

            $iterator->expects($this->at($counter++))->method('next');
        }

        $iterator->expects($this->at($counter))->method('valid')->will($this->returnValue(false));
    }
}
