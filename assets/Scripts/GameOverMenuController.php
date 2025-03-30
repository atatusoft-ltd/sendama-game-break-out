<?php

namespace SendamaEngine\BreakOut\Scripts;

use Sendama\Engine\Core\Behaviours\Behaviour;
use Sendama\Engine\UI\Menus\Menu;

class GameOverMenuController extends Behaviour
{
  protected ?Menu $menu = null;

  public function onStart(): void
  {
    // onStart is useful for initializing variables
    if ($this->menu) {
      $this->menu->setActiveItemByIndex(0);
    }
  }

  public function onUpdate(): void
  {
    // onUpdate is called once per frame
  }

  public function setMenu(Menu $menu)
  {
    $this->menu = $menu;
  }
}
