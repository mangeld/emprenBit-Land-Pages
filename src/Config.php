<?php

namespace mangeld;

class Config
{
  private $base_folder;
  private $ops = array();

  public function __construct()
  {
    $this->base_folder = dirname( dirname(__FILE__) );

    $this->ops = array(
      'mysql_user' => 'root',
      'mysql_passwd' => 'toor',
      'mysql_host' => 'localhost',
      'mysql_db_name' => 'landingPages',
      'storage_folder' => $this->base_folder . '/public/storage',
      'script_mk_image_versions' => 'php ' . $this->base_folder .  '/scripts/mk_image_versions.php',
      'log_enabled' => true,
      'log_debug' => true, //Disable in production
      'log_file' => $this->base_folder . '/landing.log',
      'storage_permission' => 0700,
      'image_quality' => 80,
      'date_format' => 'd/m/Y H:i:s O',
      'save_original_media' => false,
      'image_sizes' => array(
        'small' => 250,
        'medium' => 600,
        'large' => 1200
      )
    );
  }

  public function varOrEnv($varName)
  {
    $env = getenv($varName);
    return $env !== false ? $env : $this->ops[$varName];
  }

  public function storageFolder()
  {
    return $this->varOrEnv('storage_folder');
  }

  public function scriptMkImageV()
  {
    return $this->varOrEnv('script_mk_image_versions');
  }

  public function logEnabled()
  {
    return $this->varOrEnv('log_enabled') === true;
  }

  public function mysqlUser()
  {
    return $this->varOrEnv('mysql_user');
  }

  public function mysqlPasswd()
  {
    return $this->varOrEnv('mysql_passwd');
  }

  public function mysqlHost()
  {
    return $this->varOrEnv('mysql_host');
  }

  public function mysqlDbName()
  {
    return $this->varOrEnv('mysql_db_name');
  }

  public function logDebug()
  {
    return $this->varOrEnv('log_debug') === true;
  }

  public function logFile()
  {
    return $this->varOrEnv('log_file');
  }

  public function storagePerms()
  {
    return $this->varOrEnv('storage_permission');
  }

  public function imgQ()
  {
    return $this->varOrEnv('image_quality');
  }

  public function saveOriginalMedia()
  {
    return $this->varOrEnv('save_original_media');
  }

  public function dateFormat()
  {
    return $this->varOrEnv('date_format');
  }

  public function imgSizes()
  {
    return $this->ops['image_sizes'];
  }
}
