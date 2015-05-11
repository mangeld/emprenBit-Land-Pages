<?php

namespace mangeld\lib;

use mangeld\Config;
use mangeld\exceptions\FileSystemException;
use mangeld\lib\filesystem\File;

class Logger
{
  private static $loggerInstance = null;
  /** @var File */
  private $logFile = null;

  private function __construct()
  {
    if( Config::log_enabled )
    {
      try
      { $this->logFile = File::openFile(Config::log_file); }
      catch( FileSystemException $e )
      { $this->logFile = @File::newFile(Config::log_file); }

      if( $this->logFile != null )
        $this->logFile->open('a');
    }
  }

  function __destruct()
  {
    if( Config::log_enabled && $this->logFile != null )
      $this->logFile->close();
  }

  public static function close()
  {
    self::$loggerInstance = null;
  }

  public static function instance()
  {
    if( self::$loggerInstance == null )
      self::$loggerInstance = new Logger();

    return self::$loggerInstance;
  }

  public function write($message, $level)
  {
    if( !Config::log_enabled || $this->logFile == null ) return;

    $handle = $this->logFile->getHandle();

    if( $message instanceof \Exception )
      $message = $this->formatException( $message );

    fwrite($handle, $this->formatMessage($message, $level) . "\n");
  }

  private function formatException(\Exception $e)
  {
    return vsprintf(
      '%s at line %s in %s. Stack trace: %s',
      array(
        $e->getMessage(),
        $e->getLine(),
        $e->getFile(),
        //Remove new lines from the stack trace
        trim(preg_replace('/\s+/', ' ', $e->getTraceAsString()))
      )
    );
  }

  private function formatMessage($message, $level)
  {
    $date = date( 'd/m/Y H:i:s O' );
    return sprintf(
      '%s | %s: %s',
      $date,
      $level,
      $message
    );
  }

  /**
   * System is unusable.
   *
   * @param string $message
   * @param array $context
   * @return null
   */
  public function emergency($message, array $context = array())
  {
    $this->write($message, 'EMERGENCY');
  }

  /**
   * Action must be taken immediately.
   *
   * Example: Entire website down, database unavailable, etc. This should
   * trigger the SMS alerts and wake you up.
   *
   * @param string $message
   * @param array $context
   * @return null
   */
  public function alert($message, array $context = array())
  {
    $this->write($message, 'ALERT');
  }

  /**
   * Critical conditions.
   *
   * Example: Application component unavailable, unexpected exception.
   *
   * @param string $message
   * @param array $context
   * @return null
   */
  public function critical($message, array $context = array())
  {
    $this->write($message, 'CRITICAL');
  }

  /**
   * Runtime errors that do not require immediate action but should typically
   * be logged and monitored.
   *
   * @param string $message
   * @param array $context
   * @return null
   */
  public function error($message, array $context = array())
  {
    $this->write($message, 'ERROR');
  }

  /**
   * Exceptional occurrences that are not errors.
   *
   * Example: Use of deprecated APIs, poor use of an API, undesirable things
   * that are not necessarily wrong.
   *
   * @param string $message
   * @param array $context
   * @return null
   */
  public function warning($message, array $context = array())
  {
    $this->write($message, 'WARNING');
  }

  /**
   * Normal but significant events.
   *
   * @param string $message
   * @param array $context
   * @return null
   */
  public function notice($message, array $context = array())
  {
    $this->write($message, 'NOTICE');
  }

  /**
   * Interesting events.
   *
   * Example: User logs in, SQL logs.
   *
   * @param string $message
   * @param array $context
   * @return null
   */
  public function info($message, array $context = array())
  {
    $this->write($message, 'INFO');
  }

  /**
   * Detailed debug information.
   *
   * @param string $message
   * @param array $context
   * @return null
   */
  public function debug($message, array $context = array())
  {
    if( Config::log_debug )
      $this->write($message, 'DEBUG');
  }

  /**
   * Logs with an arbitrary level.
   *
   * @param mixed $level
   * @param string $message
   * @param array $context
   * @return null
   */
  public function log($level, $message, array $context = array())
  {
    $this->write($message, 'LOG');
  }

}
