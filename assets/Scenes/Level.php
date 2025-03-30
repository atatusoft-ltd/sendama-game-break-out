<?php

namespace SendamaEngine\BreakOut\Scenes;

use Sendama\Engine\Core\Behaviours\SimpleQuitListener;
use Sendama\Engine\Core\Scenes\AbstractScene;
use Sendama\Engine\Core\GameObject;
use Sendama\Engine\Core\Texture2D;
use Sendama\Engine\Core\Vector2;
use Sendama\Engine\Debug\Debug;
use Sendama\Engine\Events\Interfaces\EventInterface;
use Sendama\Engine\Events\Interfaces\ObservableInterface;
use Sendama\Engine\Events\Interfaces\ObserverInterface;
use Sendama\Engine\UI\Label\Label;
use SendamaEngine\BreakOut\Events\GroundCollisionEvent;
use SendamaEngine\BreakOut\Scripts\BallController;
use SendamaEngine\BreakOut\Scripts\BrickController;
use SendamaEngine\BreakOut\Scripts\LevelController;
use SendamaEngine\BreakOut\Scripts\PaddleController;
use SendamaEngine\BreakOut\Scripts\ScoreKeeper;
use SendamaEngine\BreakOut\Scripts\UiController;

class Level extends AbstractScene implements ObserverInterface
{
  protected const int TOTAL_ROWS_OF_BRICKS = 2;

  protected ?BallController $ballController = null;
  protected ?PaddleController $paddleController = null;

  public function awake(): void
  {
    // awake is called when the scene is loaded
    $this->environmentTileMapPath = 'Maps/level';

    // create your game objects here
    $levelManager = new GameObject('Level Manager');
    $paddle = new GameObject('Paddle', 'paddle', position: Vector2::one());
    $ball = new GameObject('Ball', 'ball', position: Vector2::one());
    $ball->addComponent(BallController::class);
    $bricks = $this->generateBricks($ball);

    // create gui elements
    $scoreLabel = new Label($this, '', new Vector2(3, 2), new Vector2(10, 1));
    $lifeCountLabel = new Label($this, '', new Vector2(3, 3), new Vector2(10, 1));

    $this->preparePaddle($paddle, $ball);
    $ballController = $ball->getComponent(BallController::class);
    assert($ballController instanceof BallController);
    $this->prepareLevelManager($levelManager, $ballController);
    $this->prepareGUI($levelManager, $scoreLabel, $lifeCountLabel);

    // add the game objects to the scene
    $this->add($levelManager);
    $this->add($paddle);
    $this->add($ball);
    $this->add($lifeCountLabel);

    foreach ($bricks as $brick) {
      $this->add($brick);
    }

    $this->add($scoreLabel);
  }

  protected function prepareLevelManager(GameObject $levelManager, BallController $ballController): void
  {
    $levelManager->addComponent(SimpleQuitListener::class);
    $levelController = $levelManager->addComponent(LevelController::class);
    $scoreKeeper = $levelManager->addComponent(ScoreKeeper::class);
    $levelManager->addComponent(UiController::class);

    assert($levelController instanceof LevelController);
    assert($scoreKeeper instanceof ScoreKeeper);

    $ballController->addObservers($levelController, $scoreKeeper);
    $scoreKeeper->addObservers($levelController);
  }

  /**
   * Generate bricks for the level
   *
   * @return GameObject[]
   */
  public function generateBricks(GameObject $ball): array
  {
    $startingBrickRow = 4;
    $startingBrickColumn = 4;

    $bricks = [];
    $brickCount = 0;
    $brickLength = strlen('======');
    $columnWidth = $brickLength + 1;
    $brickHeight = 1;

    for ($row = 0; $row < self::TOTAL_ROWS_OF_BRICKS; $row++) {
      $y = $startingBrickRow + $row;
      for ($column = 0; $column < 15; $column++) {
        $x = $startingBrickColumn + ($columnWidth * $column);

        $brickCount++;
        $brick = new GameObject("Brick #$brickCount", 'brick', position: new Vector2($x, $y));
        $brickTexture = new Texture2D('Textures/brick.texture');
        $brick->setSpriteFromTexture($brickTexture, Vector2::zero(), new Vector2($brickLength, $brickHeight));
        $brickController = $brick->addComponent(BrickController::class);
        $brickController->setBall($ball);

        $bricks[] = $brick;
      }
    }

    return $bricks;
  }

  /**
   * @param GameObject $paddle
   * @param GameObject $ball
   * @return void
   */
  protected function preparePaddle(GameObject $paddle, GameObject $ball): void
  {
    $paddleWidth = PaddleController::PADDLE_WIDTH;
    $paddleHeight = 1;

    $this->paddleController = $paddle->addComponent(PaddleController::class);
    $paddleTexture = new Texture2D('Textures/paddle.texture');
    $paddle->setSpriteFromTexture($paddleTexture, Vector2::zero(), new Vector2($paddleWidth, $paddleHeight));

    $this->resetPaddle();
    $this->prepareBall($ball, $this->paddleController);
  }

  /**
   * @param GameObject $ball
   * @param PaddleController $paddleController
   * @return void
   */
  protected function prepareBall(GameObject $ball, PaddleController $paddleController): void
  {
    $ballTexture = new Texture2D('Textures/ball.texture');
    $ball->setSpriteFromTexture($ballTexture, Vector2::zero(), Vector2::one());

    if (! ($this->ballController = $ball->getComponent(BallController::class)) ) {
      $this->ballController = $ball->addComponent(BallController::class);
    }
    assert($this->ballController instanceof BallController);

    $this->ballController->addObservers($this);
    $paddleController->setBallController($this->ballController);
    $this->ballController->setPaddleTransform($paddleController->getTransform());

    $this->resetBall();
  }


  protected function prepareGUI(GameObject $levelManager, Label $scoreLabel, Label $lifeCountLabel): void
  {
    $uiController = $levelManager->getComponent(UiController::class);
    assert($uiController instanceof UiController);

    $uiController->setScoreLabel($scoreLabel);
    $uiController->setLifeCountLabel($lifeCountLabel);

    $scoreKeeper = $levelManager->getComponent(ScoreKeeper::class);
    assert($scoreKeeper instanceof ScoreKeeper);
    $scoreKeeper->addObservers($uiController);
  }

  /**
   * @inheritDoc
   */
  public function onNotify(ObservableInterface $observable, EventInterface $event): void
  {
    if ($event instanceof GroundCollisionEvent) {
      $this->resetPaddle();
      $this->resetBall();
    }
  }

  protected function resetPaddle(): void
  {
    if ($this->paddleController) {
      $this->paddleController->getRenderer()->erase();
      $mapWidth = 110;

      $xPosition = ($mapWidth / 2) - (PaddleController::PADDLE_WIDTH / 2);
      $yPosition = 28;
      $this->paddleController->getTransform()->setPosition(new Vector2($xPosition, $yPosition));
    }
  }

  protected function resetBall(): void
  {
    if ($this->paddleController && $this->ballController) {
      $paddlePosition = $this->paddleController->getTransform()->getPosition();
      $xPosition = $paddlePosition->getX() + (PaddleController::PADDLE_WIDTH - 1);
      $yPosition = $paddlePosition->getY() - PaddleController::PADDLE_HEIGHT;

      $this->ballController->getRenderer()->erase();
      $this->ballController->getTransform()->setPosition(new Vector2($xPosition, $yPosition));
    }
  }
}
