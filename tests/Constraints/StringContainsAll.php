<?php

namespace BaclucC5Crud\Test\Constraints;

use function BaclucC5Crud\Lib\collect as collect;
use function mb_stripos;
use function mb_strpos;
use function mb_strtolower;
use PHPUnit\Framework\Constraint\Constraint;

/**
 * Constraint that asserts that the string it is evaluated for contains
 * a given string.
 *
 * Uses mb_strpos() to find the position of the string in the input, if not
 * found the evaluation fails.
 *
 * The sub-string is passed in the constructor.
 */
class StringContainsAll extends Constraint {
    /**
     * @var string
     */
    private $strings;

    /**
     * @var bool
     */
    private $ignoreCase;

    public function __construct(array $strings, bool $ignoreCase = false) {
        parent::__construct();

        $this->strings = $strings;
        $this->ignoreCase = $ignoreCase;
    }

    /**
     * Returns a string representation of the constraint.
     */
    public function toString(): string {
        if ($this->ignoreCase) {
            $strings = collect($this->strings)->map(function ($string) {
                return mb_strtolower($string);
            });
        } else {
            $strings = collect($this->strings);
        }

        return \sprintf(
            'contains all of "%s"',
            $strings->join(', ')
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function matches($other): bool {
        if ([] === $this->strings) {
            return true;
        }

        return 0 === collect($this->strings)->filter(function ($string) use ($other) {
            return $this->ignoreCase ? false === mb_stripos($other, $string) : false === mb_strpos($other, $string);
        })->count();
    }

    protected function failureDescription($other): string {
        $notFound = collect($this->strings)->filter(function ($string) use ($other) {
            return $this->ignoreCase ? false === mb_stripos($other, $string) : false === mb_strpos($other, $string);
        })->join(',');

        return "the following strings [{$notFound}]\n
        of all strings [".collect($this->strings)->join(',')."]\n
        were not found in:\n
        {$other}";
    }
}
