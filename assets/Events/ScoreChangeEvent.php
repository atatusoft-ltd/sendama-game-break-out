<?php

namespace SendamaEngine\BreakOut\Events;

use Sendama\Engine\Events\Enumerations\EventType;
use Sendama\Engine\Events\Event;

/**
 * ScoreChangeEvent class.
 * 
 * @package SendamaEngine\BreakOut\Events
 */
readonly class ScoreChangeEvent extends Event
{
  /**
   * ScoreChangeEvent constructor.
   *
   * @param int $previousScore The previous score.
   * @param int $currentScore The current score.
   */
  public function __construct(
    public int $previousScore,
    public int $currentScore,
  )
  {
    parent::__construct(EventType::GAME_PLAY);
  }
}