<?php

namespace SendamaEngine\BreakOut\Events;

use Sendama\Engine\Events\Enumerations\EventType;
use Sendama\Engine\Events\Event;

/**
 * LifeCountChangeEvent class.
 * 
 * @package SendamaEngine\BreakOut\Events
 */
readonly class LifeCountChangeEvent extends Event
{
  /**
   * LifeCountChangeEvent constructor.
   *
   * @param int $previousLifeCount The previous life count.
   * @param int $currentLifeCount The current life count.
   */
  public function __construct(
    public int $previousLifeCount,
    public int $currentLifeCount,
  )
  {
    parent::__construct(EventType::GAME_PLAY);
  }
}