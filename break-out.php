<?php

require __DIR__ . '/vendor/autoload.php';

use Amasiye\Figlet\FontName;
use Sendama\Engine\Game;
use Sendama\Engine\Core\Scenes\TitleScene;
use SendamaEngine\BreakOut\Scenes\GameOverScene;
use SendamaEngine\BreakOut\Scenes\Level;

/**
 * @return void
 * @throws Exception
 */
function bootstrap(): void
{
  $gameName = 'Blockout'; // This will be overwritten by the .env file if GAME_NAME is set
  $game = new Game($gameName);

  $titleScene = new TitleScene('Title Screen');
  $titleScene
	  ->setTitleFont(FontName::ANSI_SHADOW)
	  ->setTitle($gameName);

  $game->addScenes(
    $titleScene,
    new Level('Level'),
    new GameOverScene('Game Over')
  );

  $game
    ->loadSettings()
    ->run();
}

bootstrap();
