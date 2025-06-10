<?php

namespace SendamaEngine\BreakOut\Scripts;

use Sendama\Engine\Core\Behaviours\Behaviour;
use Sendama\Engine\Core\GameObject;
use Sendama\Engine\Core\Vector2;
use Sendama\Engine\Debug\Debug;
use Sendama\Engine\Events\Interfaces\ObservableInterface;
use Sendama\Engine\Events\Traits\ObservableTrait;
use SendamaEngine\BreakOut\Events\BrickCollisionEvent;

class BrickController extends Behaviour implements ObservableInterface
{
  use ObservableTrait;

  protected ?BallController $ballController = null;
  protected ?GameObject $ball = null;
  protected int $tier = 1;

  protected int $leftBound = 0;
  protected int $rightBound = 0;

  public function onStart(): void
  {
    // onStart is useful for initializing variables
    $this->setBall(GameObject::find('Ball'));
    $this->leftBound = $this->getTransform()->getPosition()->getX();
    $this->rightBound = $this->getTransform()->getPosition()->getX() + 6;
  }

  public function onFixedUpdate(): void
  {
  }

  public function onUpdate(): void
  {
    if ($this->ballController) {
      if ($this->ballIsColliding()) {
        $newVelocity = Vector2::reflect($this->ballController->getVelocity(), new Vector2(0, 1));
        $this->ballController->setVelocity($newVelocity);
        $this->notify(new BrickCollisionEvent($this->tier * 200));
        $this->getGameObject()->deactivate();
      }
    }
  }

  public function setBall(?GameObject $ball): void
  {
    if ($ball) {
      $this->ball = $ball;
      $this->ballController = $ball->getComponent(BallController::class);
    }
  }

  private function ballIsColliding(): bool
  {
    if ($this->ball->getTransform()->getPosition()->getY() !== $this->getTransform()->getPosition()->getY()) {
      return false;
    }

    if ($this->ball->getTransform()->getPosition()->getX() < $this->leftBound ||
        $this->ball->getTransform()->getPosition()->getX() + 1 > $this->rightBound) {
      return false;
    }

    return true;
  }
}
