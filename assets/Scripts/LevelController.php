<?php

namespace SendamaEngine\BreakOut\Scripts;

use Sendama\Engine\Core\Behaviours\Behaviour;
use Sendama\Engine\Debug\Debug;
use Sendama\Engine\Events\Interfaces\EventInterface;
use Sendama\Engine\Events\Interfaces\ObservableInterface;
use Sendama\Engine\Events\Interfaces\ObserverInterface;
use Sendama\Engine\Exceptions\Scenes\SceneNotFoundException;
use SendamaEngine\BreakOut\Events\LifeCountChangeEvent;

class LevelController extends Behaviour implements ObserverInterface
{
  public function onStart(): void
  {
    // onStart is useful for initializing variables
  }

  public function onUpdate(): void
  {
    // onUpdate is called once per frame
  }

  /**
   * @param ObservableInterface $observable
   * @param EventInterface $event
   * @return void
   * @throws SceneNotFoundException
   */
  public function onNotify(ObservableInterface $observable, EventInterface $event): void
  {
    if ($event instanceof LifeCountChangeEvent) {
      Debug::log('Life count changed');

      if ($event->currentLifeCount < 1) {
        // Game over
        Debug::log('Game Over');
        loadScene('Game Over');
      }
    }
  }
}
