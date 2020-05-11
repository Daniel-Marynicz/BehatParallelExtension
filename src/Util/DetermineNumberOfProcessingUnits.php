<?php

namespace DMarynicz\BehatParallelExtension\Util;

final class DetermineNumberOfProcessingUnits implements CanDetermineNumberOfProcessingUnits
{
    /**
     * {@inheritdoc}
     */
    public function getNumberOfProcessingUnits()
    {
        $result = $this->tryGetFromNumberOfProcessorsEnvVar();
        if ($result) {
            return $result;
        }

        $result = $this->tryGetFromCpuInfo();

        return $result ?: 1;
    }

    /**
     * @return int|null
     */
    private function tryGetFromCpuInfo()
    {
        if (! is_file('/proc/cpuinfo')) {
            return null;
        }

        $cpuInfo = file_get_contents('/proc/cpuinfo');
        if (! is_string($cpuInfo)) {
            return null;
        }

        preg_match_all('/^processor\s+:\s+\d+/m', $cpuInfo, $matches);

        $result = count($matches[0]);

        return $this->isIntAndGreaterThanZero($result) ? $result : null;
    }

    /**
     * @return int|null
     */
    private function tryGetFromNumberOfProcessorsEnvVar()
    {
        $result = getenv('NUMBER_OF_PROCESSORS');
        if (empty($result)) {
            return null;
        }

        $result = (int) $result;

        return $this->isIntAndGreaterThanZero($result) ? $result : null;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    private function isIntAndGreaterThanZero($value)
    {
        return is_int($value) && $value > 0;
    }
}
