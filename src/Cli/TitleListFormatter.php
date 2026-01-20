<?php

namespace DMarynicz\BehatParallelExtension\Cli;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioLikeInterface;
use DMarynicz\BehatParallelExtension\Task\TaskUnit;

/**
 * Formats a list of features or scenarios for display in the progress bar.
 */
final class TitleListFormatter
{
    /** @var int The maximum length of the formatted string */
    private $maxLength;

    /**
     * @param int $maxLength The maximum length of the formatted string
     */
    public function __construct(int $maxLength = 120)
    {
        $this->maxLength = $maxLength;
    }

    /**
     * @param TaskUnit[] $units
     */
    public function formatFeatures(array $units): string
    {
        $features = [];
        foreach ($units as $unit) {
            $feature = $unit->getFeature();
            if (in_array($feature, $features, true)) {
                continue;
            }

            $features[] = $feature;
        }

        return $this->format($features, 'Feature');
    }

    /**
     * @param TaskUnit[] $units
     */
    public function formatScenarios(array $units): string
    {
        $scenarios = [];
        foreach ($units as $unit) {
            if ($unit->getScenario() === null) {
                continue;
            }

            $scenarios[] = $unit->getScenario();
        }

        return $this->format($scenarios, 'Scenario');
    }

    /**
     * Formats the list of items into a string, truncating if necessary.
     *
     * @param array<FeatureNode|ScenarioLikeInterface> $items  List of features or scenarios
     * @param string                                   $prefix Prefix for the first item (e.g., 'Feature' or 'Scenario')
     *
     * @return string Formatted string
     */
    public function format(array $items, string $prefix): string
    {
        $titles = [];
        foreach ($items as $index => $item) {
            $titles[] = $index === 0
                ? sprintf('<info>%s: %s</info>', $prefix, $item->getTitle())
                : sprintf('<info>%s</info>', $item->getTitle());
        }

        $message = implode(', ', $titles);
        if (mb_strlen(strip_tags($message)) <= $this->maxLength) {
            return $message;
        }

        $limitedTitles = [];
        $currentLength = 0;
        foreach ($titles as $index => $title) {
            $titleLength = mb_strlen(strip_tags($title));
            if ($index > 0) {
                $titleLength += 2; // for ", "
            }

            $suffix       = sprintf(' (and %d more)', count($titles) - $index);
            $suffixLength = mb_strlen($suffix);

            if ($currentLength + $titleLength + $suffixLength > $this->maxLength) {
                break;
            }

            $limitedTitles[] = $title;
            $currentLength  += $titleLength;
        }

        if (empty($limitedTitles)) {
            $firstTitle = reset($titles);
            $suffix     = sprintf(' (and %d more)', count($titles) - 1);

            return $firstTitle . $suffix;
        }

        return implode(', ', $limitedTitles) . sprintf(' (and %d more)', count($titles) - count($limitedTitles));
    }
}
