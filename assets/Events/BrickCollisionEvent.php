<?php

namespace SendamaEngine\BreakOut\Events;

use Sendama\Engine\Events\Enumerations\EventType;
use Sendama\Engine\Events\Event;

/**
 * BrickCollisionEvent class.
 * 
 * @package SendamaEngine\BreakOut\Events
 */
readonly class BrickCollisionEvent extends Event
{
  public function __construct(
    public int $brickValue
  )
  {
    parent::__construct(EventType::GAME_PLAY);
  }
}