<?php

namespace SendamaEngine\BreakOut\Scripts;

use Sendama\Engine\Core\Behaviours\Behaviour;
use Sendama\Engine\Core\GameObject;
use Sendama\Engine\Debug\Debug;

class BrickController extends Behaviour
{
  protected ?BallController $ballController = null;
  protected ?GameObject $ball = null;

  public function onStart(): void
  {
    // onStart is useful for initializing variables
    $this->setBall(GameObject::find('Ball'));
  }

  public function onUpdate(): void
  {
    // onUpdate is called once per frame
  }

  public function setBall(?GameObject $ball): void
  {
    if ($ball) {
      $this->ballController = $ball->getComponent(BallController::class);
    }
  }
}
