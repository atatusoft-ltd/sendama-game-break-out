<?php

namespace SendamaEngine\BreakOut\Events;

use Sendama\Engine\Events\Enumerations\EventType;
use Sendama\Engine\Events\Event;

/**
 * GroundCollisionEvent class.
 * 
 * @package SendamaEngine\BreakOut\Events
 */
readonly class GroundCollisionEvent extends Event
{
  public function __construct()
  {
    parent::__construct(EventType::GAME_PLAY);
  }
}