<?php
namespace Waxedphp\Waxedphp\Php\Setters;

class Article extends AbstractSetter {

  /**
   * @var ?string $pre
   */
  protected ?string $pre = null;

  /**
   * @var ?string $headline
   */
  protected ?string $headline = null;

  /**
   * @var ?string $lead
   */
  protected ?string $lead = null;

  /**
   * @var ?string $html
   */
  protected ?string $html = null;

  /**
   * @var ?string $image
   */
  protected ?string $image = null;
  
  /**
   * @var ?string $alt
   */
  protected ?string $alt = null;

  /**
   * @var ?string $call
   */
  protected ?string $call = null;
  
  /**
   * @var ?string $url
   */
  protected ?string $url = null;
  
  /**
   * allowed options
   *
   * @var array<mixed> $_allowedOptions
   */
  protected array $_allowedOptions = [
  'pre', 'headline', 'lead', 'html', 'image', 'alt', 'call', 'url'
  ];

  /**
  * value
  *
  * @param mixed $value
  * @return array<mixed>
  */
  public function value(mixed $value = null): array {
    return $this->getArrayOfAllowedOptions();
  }

}
