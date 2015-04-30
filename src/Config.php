<?php

namespace mangeld;

class Config
{
  const storage_folder = '/vagrant/public/storage';
  const storage_permission = 0700;
  public static $image_sizes = array(
    'small' => 200,
    'medium' => 600,
    //'large' => 1200
  );
}
