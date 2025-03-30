<?php

namespace SendamaEngine\BreakOut\Scenes;

use Amasiye\Figlet\FontName;
use Sendama\Engine\Core\Behaviours\SimpleQuitListener;
use Sendama\Engine\Core\GameObject;
use Sendama\Engine\Core\Rect;
use Sendama\Engine\Core\Scenes\AbstractScene;
use Sendama\Engine\Core\Vector2;
use Sendama\Engine\IO\Enumerations\KeyCode;
use Sendama\Engine\UI\Menus\Menu;
use Sendama\Engine\UI\Menus\MenuItems\MenuItem;
use Sendama\Engine\UI\Text\Text;
use SendamaEngine\BreakOut\Scripts\GameOverMenuController;

/**
 * The game over scene.
 *
 * @package SendamaEngine\BreakOut\Scenes
 */
class GameOverScene extends AbstractScene
{
  /**
   * @var Menu $menu The game over menu
   */
  protected Menu $menu;
  /**
   * @var Text $titleText The title text
   */
  protected Text $titleText;
  /**
   * @var int $menuWidth The width of the menu.
   */
  protected int $menuWidth = 20;
  /**
   * @var int $menuHeight The height of the menu.
   */
  protected int $menuHeight = 8;
  /**
   * @var int $titleTopMargin The top margin of the title.
   */
  protected int $titleTopMargin = 4;

  public function awake(): void
  {
    $gameOverMenuManager = new GameObject('GameOverMenuManager', new Vector2(0, 0));
    $this->titleText = new Text($this, 'Game Over', new Vector2(0, 4), new Vector2(DEFAULT_SCREEN_WIDTH, 5));
    $this->menu = new Menu('', description: 'q:quit', dimensions: new Rect(new Vector2($this->getMenuLeftMargin(), $this->getMenuTopMargin()), new Vector2($this->menuWidth, $this->menuHeight)), cancelKey: [KeyCode::Q, KeyCode::q], onCancel: fn() => quitGame());

    $this
      ->titleText
      ->setText('Game Over');
    $this
      ->titleText
      ->setFontName(FontName::ANSI_SHADOW->value);

    $this->menu->addItem(new MenuItem('Play Again', 'Restart the game', callback: function() {
      loadScene('Level');
    }));
    $this->menu->addItem(new MenuItem('To Title', 'Back to title screen', callback: function() {
      loadScene(0);
    }));
    $this->menu->addItem(new MenuItem('Quit Game', 'Exit the game', callback: function() {
      quitGame();
    }));

    $gameOverMenuController = $gameOverMenuManager->addComponent(GameOverMenuController::class);
    assert($gameOverMenuController instanceof GameOverMenuController);
    $gameOverMenuController->setMenu($this->menu);

    $this->add($this->titleText);
    $this->add($this->menu);
    $this->add($gameOverMenuManager);
  }

  protected function getMenuLeftMargin(): int
  {
    return (get_screen_width() / 2) - ($this->menuWidth / 2);
  }

  protected function getMenuTopMargin(): int
  {
    return ($this->titleTopMargin + $this->titleText->getHeight() + 1);
  }
}
