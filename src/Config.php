<?php

namespace mangeld;

class Config
{
  const storage_folder = '/vagrant/public/storage';
  const script_mk_image_versions = 'php /vagrant/scripts/mk_image_versions.php';
  const log_enabled = true;
  //const log_debug = true; //Disable in production
  const log_file = '/vagrant/landing.log';
  const storage_permission = 0700;
  const image_quality = 80;
  public static $image_sizes = array(
    'small' => 250,
    'medium' => 600,
    'large' => 1200
  );

  private $ops = array(
    'storage_folder' => '/vagrant/public/storage',
    'script_mk_image_versions' => 'php /vagrant/scripts/mk_image_versions.php',
    'log_enabled' => true,
    'log_debug' => true, //Disable in production
    'log_file' => '/vagrant/landing.log',
    'storage_permission' => 0700,
    'image_quality' => 80,
    'image_sizes' => array(
      'small' => 250,
      'medium' => 600,
      'large' => 1200
    )
  );

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

  public function imgSizes()
  {
    return $this->ops['image_sizes'];
  }
}
