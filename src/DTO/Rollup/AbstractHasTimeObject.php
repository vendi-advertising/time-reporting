<?php

namespace App\DTO\Rollup;

abstract class AbstractHasTimeObject implements HasTimeInterface
{
    private ?float $timeCache = null;

    /**
     * @param HasTimeInterface[] $children
     * @return float
     */
    protected function getTimeFromChildren(array $children): float
    {
        if (null === $this->timeCache) {
            $this->timeCache = 0;
            foreach ($children as $child) {
                $this->timeCache += $child->getTime();
            }
        }

        return $this->timeCache;
    }

    final public function hasTime(): bool
    {
        return $this->getTime() > 0;
    }
}