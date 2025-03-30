<?php

namespace SendamaEngine\BreakOut\Scripts;

use Sendama\Engine\Core\Behaviours\Behaviour;
use Sendama\Engine\Core\Scenes\SceneManager;
use Sendama\Engine\Core\Vector2;
use Sendama\Engine\Debug\Debug;
use Sendama\Engine\Events\Interfaces\EventInterface;
use Sendama\Engine\Events\Interfaces\ObservableInterface;
use Sendama\Engine\Events\Interfaces\ObserverInterface;
use Sendama\Engine\UI\Label\Label;
use SendamaEngine\BreakOut\Events\LifeCountChangeEvent;
use SendamaEngine\BreakOut\Events\ScoreChangeEvent;

/**
 * The UI controller.
 *
 * @package SendamaEngine\BreakOut\Scripts
 */
class UiController extends Behaviour implements ObserverInterface
{
  /**
   * @var Label|null The score label.
   */
  protected ?Label $scoreLabel = null;
  /**
   * @var Label|null The life count label.
   */
  protected ?Label $lifeCountLabel = null;
  /**
   * @var int The y coordinate of the UI elements.
   */
  protected int $uiYCoordinate = 2;

  public function onStart(): void
  {
    // onStart is useful for initializing variables
    $this->setScore(0);
    $this->setLifeCount(3);
  }

  public function onUpdate(): void
  {
    // onUpdate is called once per frame
  }

  /**
   * Set the score label.
   *
   * @param Label $scoreLabel The score label.
   */
  public function setScoreLabel(Label $scoreLabel): void
  {
    $this->scoreLabel = $scoreLabel;
    $scoreLabelLength = 10;
    $x = (110 / 2) - ($scoreLabelLength/ 2);
    $y = $this->uiYCoordinate;
    $this->scoreLabel->setPosition(new Vector2($x, $y));
  }

  /**
   * Set the score.
   *
   * @param int $score The score.
   */
  public function setScore(int $score): void
  {
    $text = sprintf("%010d", $score);
    $this->scoreLabel->setText($text);
  }

  /**
   * Set the life count label.
   *
   * @param Label $lifeCountLabel The life count label.
   */
  public function setLifeCountLabel(Label $lifeCountLabel): void
  {
    $this->lifeCountLabel = $lifeCountLabel;
    $x = 2;
    $y = $this->uiYCoordinate;
    $this->lifeCountLabel->setPosition(new Vector2($x, $y));
  }

  /**
   * Set the life count.
   *
   * @param int $lifeCount The life count.
   */
  public function setLifeCount(int $lifeCount): void
  {
    if ($lifeCount > -1) {
      $text = str_repeat('O ', $lifeCount);
      $this->lifeCountLabel->setText("  $text");
    }
  }

  public function onNotify(ObservableInterface $observable, EventInterface $event): void
  {
    if ($event instanceof ScoreChangeEvent) {
      $this->setScore($event->currentScore);
    }

    if ($event instanceof LifeCountChangeEvent) {
      $this->setLifeCount($event->currentLifeCount);
    }
  }
}
