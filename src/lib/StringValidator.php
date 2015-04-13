<?php

namespace mangeld\lib;

class StringValidator
{
    private $uuid4Regex = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[98ab][0-9a-f]{3}-[0-9a-f]{12}$/i';
    private $emailRegex = '/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i';

    public function __construct()
    {
        //code...
    }

    /**
     * Checks if the given string is a valid uuid v.4
     * @param  string $uuid string to check
     * @return boolean true if the string given is uuid4 compilant
     * false if is not.
     */
    public function validateUuid4($uuid)
    {
      return 1 == preg_match($this->uuid4Regex, $uuid);
    }

    public function validateEmail($email)
    {
      return 1 == preg_match($this->emailRegex, $email);
    }
}