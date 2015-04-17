<?php

namespace mangeld\db;

interface DBInterface
{
  public function savePage(\mangeld\obj\Page $page);

  /**
   * @return array(\mangeld\obj\Page) Array of Page objects
   */
  public function fetchPages();

  /**
   * @return \mangeld\obj\Page the page requested
   */
  public function fetchPage($pageId);

  /**
   * @return boolean True on success, false on failure
   */
  public function deletePage($pageId);

  /**
   * @param \mangeld\obj\User $user
   * @return mixed
   */
  public function saveUser(\mangeld\obj\User $user);

  /**
   * @param $userId
   * @return mixed
   */
  public function fetchUser($userId);
}