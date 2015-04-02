<?php

namespace mangeld\db;

interface DBInterface
{
  public function savePage(\mangeld\obj\Page $page);

  /**
   * @return array Array of Page objects
   */
  public function fetchPages();

  
  public function fetchPage($pageId);
}