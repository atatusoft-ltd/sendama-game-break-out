## Creating and Using Scripts

### Creating Scripts

Scripts are PHP classes that extend the `Sendama\Engine\Core\Behaviours\Behaviour` class. A script defines the behavior of a game object by implementing methods that are called at specific points in the game loop. The following methods can be implemented in a script:

| Method | Description                           |
|--------|---------------------------------------|
|`onDisable`| Called when the script is disabled. |
|`onStop`| Called when the script is stopped.   |
|`onStart`| Called when the script is initialized. |
|`onUpdate`| Called every frame.                   |
|`onResume`| Called when the script is resumed.   |
|`onSuspend`| Called when the script is suspended. |
|`onDestroy`| Called when the script is destroyed. |
|`onCollisionEnter` | Called when the collider enters another collider. |
|`onCollisionExit` | Called when the collider exits another collider. |
|`onCollisionStay` | Called when the collider stays in contact with another collider. |
|`onTriggerEnter` | Called when the collider enters a trigger. |
|`onTriggerExit` | Called when the collider exits a trigger. |
|`onTriggerStay` | Called when the collider stays in contact with a trigger. |


### Anatomy of a Script file

```php
<?php

namespace My\Namespace\Here;

use Sendama\Engine\Core\Behaviours\Behaviour;

class MyBehaviour extends Behaviour
{
    public function onStart(): void
    {
        // Initialization logic here
    }

    public function onUpdate(): void
    {
        // Update logic here
    }
}
```

### Controlling a Game Object with a Script

A script defines a blueprint for a Component, and its code won't run until the script is attached to a GameObject. In the Sendama game engine, you can attach a Component to a GameObject using code. Here's an example:

```php
class Level01 extends AbstractScene
{
  public function awake(): void
  {
    // Set up the player
    $player = new GameObject('Player Ship');
    $playerMovementController = $player->addComponent(CharacterMovement::class);

    // Add the game objects to the scene
    $this->add($player);
  }
}
```

In this example, the `CharacterMovement` component is attached to the **Player Ship** `GameObject` using the `addComponent` method. The `GameObject` is then added to the scene with the `add` method. This is how you attach components and set up GameObjects in the scene.

### Event Lifecycle

When a script is attached to a GameObject, the following events are triggered in the script's lifecycle:

#### Initialization
1. `onStart`: Called when the script is initialized.
    ```php
    use Sendama\Engine\Physics\CharacterController;

    ...

    private ?CharacterController $characterController = null;
    
    public function onStart(): void
    {
        $this->characterController = $this->getComponent(CharacterController::class);
    }
    ```

#### Regular Updates

1. `onUpdate`: Called every frame.
    ```php
    use Sendama\Engine\Core\Vector2;
    use Sendama\Engine\IO\Enumerations\AxisName;
    use Sendama\Engine\Physics\CharacterController;

    ...
  
    public function onUpdate(): void
    {
      $h = Input::getAxis(AxisName::HORIZONTAL);
      $v = Input::getAxis(AxisName::VERTICAL);

      if (abs($h) > 0 || abs($v) > 0) {
        $velocity = new Vector2($h, $v);
        $velocity->scale($this->speed);

        if ($this->characterController) {
          $this->characterController->move($velocity);
        }
      }
    }
    ```