<?php

namespace mangeld\lib;

/**
 * Simple wrapper class to the 'crypt()' function to provide a easy
 * interface for creating bcrypt hashes and comparing them.
 *
 * @todo Create tests for this class
 * @todo  Throw exception if openssl is not found
 * @todo  Maybe use a less secure mode for generating random salts
 * if open ssl is not found. (with a trigger in the constructor)
 * @author Miguel Ángel Durán González <contact@mangel.me>
 */
class Hasher 
{
    /** @var integer The cost for the blowfish function*/
    private $_cost = 11;

    public function __construct()
    {
        if( CRYPT_BLOWFISH != 1 ) print 'BLOWFISH NOT FOUND';
    }

    /**
     * Creates a blowfish hash from the password given.
     * 
     * NOTE: Passwords greater than 72 characters will be
     * trimmed due to the inner workings of bcrypt()
     *
     * @todo Throw exception if cost is less than 4 or greather than 31.
     * 
     * @param  string $password The password to be hashed
     * @param integer $cost The cost for the bcrypt algorithm
     * @return string The password hashed
     */
    public function create_hash_blowfish($password, $cost = false)
    {
        if($cost !== false) $this->_cost = $cost;

        $prefix = $this->getHashPrefix();
        $salt = $prefix.$this->_cost.'$'.$this->generate_unique_salt(22).'$';
        print $salt . " (".strlen($salt).")"."\n";
        $hash = crypt($password, $salt);
        print $hash . " (".strlen($hash).")"."\n";
        return $hash;
    }

    /**
     * Depending on the version of php some versions of the blowfish
     * algo don't work, so first we have to detect the current php 
     * version.
     * 
     * @return [string] The valid and most secure prefix based on the
     * platform
     */
    private function getHashPrefix()
    {
        $before537 = '$2a$';
        $after537 = '$2y$';

        $isOlder = version_compare( phpversion(), '5.3.7', '<' );

        if( $isOlder )
            return $before537;
        else
            return $after537;
    }

    /**
     * 
     * TODO: Make constant time comparision. (Timming attack protection)
     * aka known as 'compare all the chars anyway' (do not return
     * immediately if there is a char in x pos that doesn't match)
     * @param string $hash Hash to check.
     * @param string $test Given password to check against the hash.
     * @return boolean True if the passwords matches the hash.
     */
    public function is_equal_blowfish($hash, $test)
    {
        $given_password = crypt($test, $hash);

        return $hash === $given_password;
    }

    /**
     * Calculates the optimal cost parameter for the hardware executing this class.
     *
     * Attention!
     * 
     * Never run this method in production code, it is only meant for configuring
     * the performance of the class and no more.
     * 
     * @param integer Minimum target time in milliseconds
     * that the hash function should delay (Default 350 ms).
     * 
     * @return integer The cost factor given the target time.
     */
    public function get_optimal_cost($targetTime = 350)
    {
        return $this->generate_cost((float)$targetTime/(float)1000);
    }

    public function set_bcrypy_cost($cost)
    {
        $this->_cost = $cost;
    }

    /**
     * Generates a salt composed of numbers ranging from 0 to 9, 
     * and letters upper and lower case from 'a' to 'z'.
     * 
     * @param integer $length The lenght of the word to generate.
     * @return string The random salt.
     */
    private function generate_unique_salt($length)
    {
        $salt = '';
        $words = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $wordCount = strlen($words);

        //We have to provide a salt anyway
        //If openssl is not present, then use a less
        //entropy source.
        if( /*!function_exists('openssl_random_pseudo_bytes')*/ true )
        {
            for( $c = 0; $c < $length; $c++ )
                $salt .= $words[ mt_rand(0, $wordCount - 1) ];

            return $salt;
        }

        for($i = 0; $i < $length; $i++)
          $salt .= $words[ $this->cryptoRandSecure(0, $wordCount) ];
        
        return $salt;
    }

    private function cryptoRandSecure($min, $max)
    {
        $range = $max - $min;
        if ($range == 0) return $min; // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 11

        do
        {
          $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes, $s)));
          $rnd = $rnd & $filter; // discard irrelevant bits
        }
        while ($rnd >= $range);

        return $min + $rnd;
   }

   /**
    * @see $this->get_optimal_cost()
    * @param  float $targetTime
    * @return integer The cost factor given the time
    */
   private function generate_cost($targetTime = 0.350)
   {
        $result = 0;
        $result_cost = 3;

        do
        {
            $result_cost++;
            $result = $this->average_execution_time($result_cost);
        }
        while ($result < $targetTime);

        return (integer)$result_cost;
   }

   /**
    * Calculates the execution time that the bcrypt takes given a cost.
    *
    * @param  integer $cost The cost (between 4 and 31)
    * @param  integer $executionTimes Nº of times to execute the bcrypt function
    * @return float The average execution time of bcrypt.
    */
   private function average_execution_time($cost = 11, $executionTimes = 10)
   {    
        /** @var float */
        $totalExecution = .0;

        for ($i=0; $i < $executionTimes; $i++)
        {
            $password = $this->generate_unique_salt(mt_rand(0,32));
            $before = microtime(true);
            $this->create_hash_blowfish($password, $cost);
            $after = microtime(true);

            $totalExecution += ($after - $before);
        }

        return $totalExecution / (integer)$executionTimes;
   }
}