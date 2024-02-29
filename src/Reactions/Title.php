<?php
namespace Waxedphp\Waxedphp\Reactions;

class Title extends AbstractReaction {

  /**
   * @var string $title
   */
  private string $title = '';

  /**
  * to array
  *
  * @return array<mixed>
  */
  function toArray(): array {
    $a = array(
      'action' => 'title',
      'title' => $this->title,
    );
    return $a;
  }

  /**
  * configure
  *
  * @param string $title
  * @return object
  */
  function configure(string $title):object {
    $this->title=$title;
    return $this;
  }


}

