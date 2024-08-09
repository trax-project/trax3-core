<?php

namespace Trax\Framework\Auth;

interface User
{
    /**
     * Get the user capabilities.
     *
     * @return array
     */
    public function capabilities(): array;
}
