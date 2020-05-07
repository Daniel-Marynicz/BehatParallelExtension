<?php

namespace DMarynicz\Tests\Behat\Context;

class ResolveDirectorySeparatorContext extends ResolveTextContext
{

    public function resolveText($text)
    {
        return str_replace(
            [
                '<DIRECTORY_SEPARATOR>',
            ],
            [
                DIRECTORY_SEPARATOR
            ],
            $text
        );
    }
}
