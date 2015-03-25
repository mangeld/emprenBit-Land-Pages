<?php

namespace mangeld\lib;

class StringValidator
{
    private $uuid4Regex = '/[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[98ab][0-9a-f]{3}-[0-9a-f]{12}/';

    public function __construct()
    {
        //code...
    }

    public function validateUuid4($uuid)
    {
      return 1 == preg_match($this->uuid4Regex, $uuid);
    }
}