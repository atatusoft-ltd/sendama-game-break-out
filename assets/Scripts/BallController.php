<?php

namespace SendamaEngine\BreakOut\Scripts;

use Sendama\Engine\Core\Behaviours\Behaviour;
use Sendama\Engine\Core\Time;
use Sendama\Engine\Core\Transform;
use Sendama\Engine\Core\Vector2;
use Sendama\Engine\Debug\Debug;
use Sendama\Engine\Events\Interfaces\ObservableInterface;
use Sendama\Engine\Events\Traits\ObservableTrait;
use SendamaEngine\BreakOut\Events\GroundCollisionEvent;

class BallController extends Behaviour implements ObservableInterface
{
  use ObservableTrait;

  protected ?Vector2 $velocity;
  protected float $nextUpdateTime = 0;
  protected float $updateInterval = 0.07;
  protected int $playerHeight = 28;

  protected ?Transform $paddleTransform = null;
  protected bool $launched = false;

  public function awake(): void
  {
    $this->velocity = new Vector2();
  }

  public function onStart(): void
  {
    // onStart is useful for initializing variables
  }

  public function onFixedUpdate(): void
  {
    $this->move();
  }

  public function onUpdate(): void
  {
    // onUpdate is called once per frame
  }

  protected function move(): void
  {
    if (Time::getTime() > $this->nextUpdateTime) {
      $nextPosition = Vector2::sum($this->getTransform()->getPosition(), $this->velocity);

      if ($this->willHitGround($nextPosition)) {
        $this->reset();
        $this->notify(new GroundCollisionEvent());
      }

      $this->velocity = match (true) {
        $nextPosition->getX() < 2 => Vector2::reflect($this->velocity, new Vector2(1, 0)),
        $nextPosition->getX() > 108 => Vector2::reflect($this->velocity, new Vector2(-1, 0)),
        $nextPosition->getY() < 2 => Vector2::reflect($this->velocity, new Vector2(0, 1)),
        $nextPosition->getY() > 29 => Vector2::reflect($this->velocity, new Vector2(0, -1)),
        $this->willCollideWithPaddle($nextPosition) => Vector2::reflect($this->velocity, new Vector2(0, -1)),
        default => $this->velocity
      };

      $this->getTransform()->translate($this->velocity);

      $this->nextUpdateTime = Time::getTime() + $this->updateInterval;
    }
  }

  /**
   * Set the velocity of the ball.
   *
   * @param Vector2 $velocity The velocity of the ball.
   */
  public function setVelocity(Vector2 $velocity): void
  {
    $this->velocity = $velocity;
    $this->getTransform()->translate($this->velocity);
  }

  /**
   * Get the velocity of the ball.
   *
   * @return Vector2 The velocity of the ball.
   */
  public function getVelocity(): Vector2
  {
    return $this->velocity;
  }

  public function reflect(Vector2 $normal): void
  {
    $reflection = Vector2::reflect($this->velocity, $normal);
    $this->velocity = $reflection;
    $this->getTransform()->translate($this->velocity);
  }

  public function launch(): void
  {
    if ($this->velocity->getMagnitude() > 0) {
      return;
    }

    $x = rand(1, 3);
    $y = -1;
    $this->velocity = new Vector2($x, $y);
    $this->launched = true;
  }

  /**
   * Check if the ball is moving.
   *
   * @return bool Whether the ball is moving or not.
   */
  public function isMoving(): bool
  {
    return $this->velocity->getMagnitude() > 0;
  }

  public function setPaddleTransform(Transform $paddleTransform): void
  {
    $this->paddleTransform = $paddleTransform;
  }

  /**
   * Check if the ball will collide with the player's paddle.
   *
   * @param Vector2 $nextPosition The next position of the ball.
   * @return bool Whether the ball will collide with the player's paddle or not.
   */
  private function willCollideWithPaddle(Vector2 $nextPosition): bool
  {
    if (!$this->paddleTransform) {
      return false;
    }

    $paddlePosition = $this->paddleTransform->getPosition();

    if ($nextPosition->getY() !== $this->playerHeight) {
      return false;
    }

    if ($nextPosition->getX() < $paddlePosition->getX()) {
      return false;
    }

    if ($nextPosition->getX() > $paddlePosition->getX() + PaddleController::PADDLE_WIDTH) {
      return false;
    }

    return true;
  }

  private function willHitGround(Vector2 $nextPosition): bool
  {
    return $nextPosition->getY() > 29;
  }

  public function reset(): void
  {
    $this->velocity = Vector2::zero();
    $this->launched = false;
  }

  public function isLaunched(): bool
  {
    return $this->launched;
  }
}
