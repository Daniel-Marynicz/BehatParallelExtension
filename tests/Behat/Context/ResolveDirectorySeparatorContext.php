<?php

namespace DMarynicz\Tests\Behat\Context;

class ResolveDirectorySeparatorContext extends ResolveTextContext
{
    /**
     * {@inheritdoc}
     */
    public function resolveText($text): string
    {
        return str_replace(
            ['<DIRECTORY_SEPARATOR>'],
            [DIRECTORY_SEPARATOR],
            $text
        );
    }
}
