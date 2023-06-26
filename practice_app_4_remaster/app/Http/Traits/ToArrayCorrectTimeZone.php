<?php

namespace App\Http\Traits;

trait ToArrayCorrectTimeZone
{
    public function toArray(): array
    {
        $array = parent::toArray();
        foreach ($this->getAttributes() as $key => $value) {
            if ($this[$key] instanceof \DateTime && isset($array[$key])) {
                $array[$key] = $this[$key]->format('Y-m-d H:i:s');
            }
        }
        return $array;
    }
}
