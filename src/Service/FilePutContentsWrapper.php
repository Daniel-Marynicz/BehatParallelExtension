<?php

namespace DMarynicz\BehatParallelExtension\Service;

use DMarynicz\BehatParallelExtension\Exception\UnexpectedValue;

class FilePutContentsWrapper
{
    const SERVICE_ID = 'parallel_extension.file_put_contents_wrapper';

    /**
     * @param string $filename
     * @param string $data
     */
    public function filePutContents($filename, $data)
    {
        if (! is_string($filename)) {
            throw new UnexpectedValue('Expected string');
        }

        if (! is_string($data)) {
            throw new UnexpectedValue('Expected string');
        }

        file_put_contents($filename, $data);
    }
}
