<?php

namespace DMarynicz\Tests\Cli;

use Behat\Gherkin\Node\FeatureNode;
use DMarynicz\BehatParallelExtension\Cli\TitleListFormatter;
use PHPUnit\Framework\TestCase;

class TitleListFormatterTest extends TestCase
{
    public function testFormatWithLargeMaxLength(): void
    {
        $titles = [
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.',
            'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum.',
            'Excepteur sint occaecat cupidatat non proident, sunt in culpa.',
            'Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit.',
            'Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet.',
            'Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse.',
            'At vero eos et accusamus et iusto odio dignissimos ducimus qui.',
            'Et harum quidem rerum facilis est et expedita distinctio.',
        ];

        $items = [];
        foreach ($titles as $title) {
            $item = $this->createMock(FeatureNode::class);
            $item->method('getTitle')->willReturn($title);
            $items[] = $item;
        }

        $formatter = new TitleListFormatter(120);
        $result    = $formatter->format($items, 'Scenario');

        $plainResult = strip_tags($result);
        $this->assertLessThanOrEqual(120, mb_strlen($plainResult), 'The formatted string should not exceed 120 chars');
    }

    public function testFormatTruncation(): void
    {
        $items = [];
        for ($i = 1; $i <= 5; $i++) {
            $item = $this->createMock(FeatureNode::class);
            $item->method('getTitle')->willReturn('Title ' . $i);
            $items[] = $item;
        }

        // "Scenario: Title 1" (17 chars)
        // ", Title 2" (9 chars)
        // ", Title 3" (9 chars)
        // " (and 2 more)" (13 chars)

        $formatter = new TitleListFormatter(30);
        $result    = $formatter->format($items, 'Scenario');

        $plainResult = strip_tags($result);
        $this->assertLessThanOrEqual(30, mb_strlen($plainResult));
        $this->assertStringContainsString('(and', $plainResult);
    }
}
