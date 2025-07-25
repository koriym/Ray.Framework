<?php

declare(strict_types=1);

namespace Be\Framework;

/**
 * Core metamorphosis engine interface
 *
 * This is the fundamental contract of Be Framework.
 * Objects transform through a chain of metamorphoses until reaching their final form.
 */
interface MetamorphosisInterface
{
    /**
     * Execute metamorphosis chain starting from input object
     *
     * @param object $input The starting object of metamorphosis chain
     *
     * @return object The final transformed object
     */
    public function __invoke(object $input): object;
}
