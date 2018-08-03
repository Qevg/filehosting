<?php

namespace Filehosting\Entity;

abstract class BaseEntity
{
    /**
     * Recursive setting a values for variables using setters
     *
     * @param array $value
     */
    public function setValues(array $value): void
    {
        foreach ($value as $k => $v) {
            $functionName = 'set' . ucfirst($k);
            if (method_exists($this, $functionName)) {
                $this->$functionName($v);
            }
        }
    }
}
