<?php

namespace DMarynicz\BehatParallelExtension\Util;

interface CanDetermineNumberOfProcessingUnits
{
    /**
     * @return int
     */
    public function getNumberOfProcessingUnits();
}
