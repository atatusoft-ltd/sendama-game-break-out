<?php

namespace SendamaEngine\BreakOut\Scripts;

use Sendama\Engine\Core\Behaviours\Behaviour;
use Sendama\Engine\Core\Vector2;
use Sendama\Engine\IO\Enumerations\AxisName;
use Sendama\Engine\IO\Enumerations\KeyCode;
use Sendama\Engine\IO\Input;

class PaddleController extends Behaviour
{
  public const int PADDLE_WIDTH = 10;
  public const int PADDLE_HEIGHT = 1;

  protected ?BallController $ballController = null;
  protected int $speed = 3;

  public function onStart(): void
  {
    // onStart is useful for initializing variables
  }

  public function onUpdate(): void
  {
    // onUpdate is called once per frame
    $h = Input::getAxis(AxisName::HORIZONTAL);

    if (abs($h) > 0) {
      $this->move($h);
    }

    if (Input::isAnyKeyPressed([KeyCode::SPACE])) {
      $this->ballController->launch();
    }
  }

  /**
   * @param int $horizontalDirection
   * @return void
   */
  protected function move(int $horizontalDirection): void
  {
    $xMin = 2;
    $xMax = 110;

    $velocity = new Vector2($horizontalDirection * $this->speed, 0);
    $currentPosition = $this->getTransform()->getPosition();
    $newPosition = new Vector2($currentPosition->getX() + $velocity->getX(), $currentPosition->getY());

    if ($newPosition->getX() < $xMin) {
      $deltaX = $xMin - $newPosition->getX();
      $velocity->add(new Vector2($deltaX, 0));
    }

    if ($newPosition->getX() + self::PADDLE_WIDTH > $xMax) {
      $deltaX = ($newPosition->getX() + self::PADDLE_WIDTH) - $xMax;
      $velocity->subtract(new Vector2($deltaX, 0));
    }

    $this->getTransform()->translate($velocity);

    if (!$this->ballController->isLaunched()) {
      $this->ballController->getTransform()->translate($velocity);
    }
  }

  public function setBallController(BallController $ballController): void
  {
    $this->ballController = $ballController;
  }
}
