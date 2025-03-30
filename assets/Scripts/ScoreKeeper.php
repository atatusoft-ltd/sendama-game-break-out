<?php

namespace SendamaEngine\BreakOut\Scripts;

use Sendama\Engine\Core\Behaviours\Behaviour;
use Sendama\Engine\Debug\Debug;
use Sendama\Engine\Events\Interfaces\EventInterface;
use Sendama\Engine\Events\Interfaces\ObservableInterface;
use Sendama\Engine\Events\Interfaces\ObserverInterface;
use Sendama\Engine\Events\Traits\ObservableTrait;
use SendamaEngine\BreakOut\Events\GroundCollisionEvent;
use SendamaEngine\BreakOut\Events\LifeCountChangeEvent;

class ScoreKeeper extends Behaviour implements ObserverInterface, ObservableInterface
{
  use ObservableTrait;

  protected int $score = 0;
  protected int $lives = 3;

  public function onStart(): void
  {
    // onStart is useful for initializing variables
    $this->reset();
  }

  public function onUpdate(): void
  {
    // onUpdate is called once per frame
  }

  public function reset(): void
  {
    $this->score = 0;
    $this->lives = 3;
  }

  public function onNotify(ObservableInterface $observable, EventInterface $event): void
  {
    if ($event instanceof GroundCollisionEvent) {
      $previousLifeCount = $this->lives;
      $this->lives--;
      $this->notify(new LifeCountChangeEvent($previousLifeCount, $this->lives));
    }
  }
}
