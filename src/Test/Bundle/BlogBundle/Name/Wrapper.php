<?php

namespace Test\Bundle\BlogBundle\Name;

/**
 * Wrapper.
 */
class Wrapper
{
    private $pattern;

    public function __construct($pattern)
    {
        $this->pattern = $pattern ?: '<strong>%s</strong>';
    }

    public function wrap($name)
    {
        return sprintf($this->pattern, $name);
    }
}
