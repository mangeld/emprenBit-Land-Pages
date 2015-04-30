<?php

namespace mangeld;

class Config
{
  const storage_folder = '/vagrant/public/storage';
  const script_mk_image_versions = 'php /vagrant/scripts/mk_image_versions.php';
  const storage_permission = 0700;
  const image_quality = 80;
  public static $image_sizes = array(
    'small' => 250,
    'medium' => 600,
    'large' => 1200
  );
}
