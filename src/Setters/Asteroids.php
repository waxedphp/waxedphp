<?php
namespace Waxedphp\Waxedphp\Setters;

class Asteroids extends AbstractSetter {

  private string $token = "";
  private string $hash = "";

  /**
   * @var array<mixed> $screens
   */
  private array $screens = [
    'game_start' => ['w'=>720,'h'=>480,'bgPos'=>'-260px 0px','img'=>'/img/asteroids/resources.png'],
    'game_over' => ['w'=>720,'h'=>480,'bgPos'=>'-260px -480px','img'=>'/img/asteroids/resources.png'],
    'game_win' => ['w'=>720,'h'=>480,'bgPos'=>'-260px -960px','img'=>'/img/asteroids/resources.png'],
  ];

  /**
   * @var array<mixed> $items
   */
  private array $items = array(
          'background'=>array(
            'type'=>'background',
            'layer'=>2,'w'=>4148,'h'=>480,'y1'=>0,'img'=>'/img/asteroids/background.png',
            'ramps'=>array(
              'x1'=>array(
                array('repeat'=>true),
                array('A'=>0,'B'=>-2074,'steps'=>700),
              ),
            ),
          ),
          'firework1'=>array(
            'type'=>'enemy',
            'layer'=>2,'w'=>260,'h'=>316,'y1'=>80,'img'=>'/img/asteroids/resources.png',
            'ramps'=>array(
              'sprite'=>array(
                array('repeat'=>true),
                array('A'=>'0px -736px','B'=>'0px -736px','steps'=>8),
                array('B'=>'0px -1052px','steps'=>60),

                array('A'=>'0px -736px','B'=>'0px -736px','steps'=>8),
                array('B'=>'0px -1052px','steps'=>100),

                array('A'=>'0px -736px','B'=>'0px -736px','steps'=>8),
                array('B'=>'0px -1052px','steps'=>80),

              ),
              'x1'=>array(
                array('repeat'=>true),
                array('A'=>300,'B'=>560,'steps'=>8,'accel'=>'start'),
                array('B'=>900,'steps'=>10,'accel'=>'end'),
                array('B'=>900,'steps'=>50),

                array('A'=>300,'B'=>560,'steps'=>8,'accel'=>'start'),
                array('B'=>900,'steps'=>10,'accel'=>'end'),
                array('B'=>900,'steps'=>90),

                array('A'=>300,'B'=>560,'steps'=>8,'accel'=>'start'),
                array('B'=>900,'steps'=>10,'accel'=>'end'),
                array('B'=>900,'steps'=>70),

              ),
              'y1'=>array(
                array('repeat'=>true),
                array('A'=>220,'B'=>-120,'steps'=>8,'accel'=>'start'),
                array('B'=>-400,'steps'=>10,'accel'=>'end'),
                array('B'=>-400,'steps'=>50),

                array('A'=>220,'B'=>-120,'steps'=>8,'accel'=>'start'),
                array('B'=>-400,'steps'=>10,'accel'=>'end'),
                array('B'=>-400,'steps'=>90),

                array('A'=>220,'B'=>-120,'steps'=>8,'accel'=>'start'),
                array('B'=>-400,'steps'=>10,'accel'=>'end'),
                array('B'=>-400,'steps'=>70),

              ),
            ),
          ),
          'tree1'=>array(
            'type'=>'enemy',
            'poly'=>array(
              array(119,10),
              array(238,316),
              array(0,316),
              array(119,11),
            ),
            'layer'=>2,'w'=>238,'h'=>316,'y1'=>210,'img'=>'/img/asteroids/resources.png',
            'bgPos'=>'0px -186px',

            //'layer'=>2,'w'=>296,'h'=>357,'y1'=>260,'img'=>'/img/asteroids2/planet1.png',
            'ramps'=>array('x1'=>array(
                array('repeat'=>true),
                array('A'=>800,'B'=>-600,'steps'=>200),
              )
            ),
          ),
          'tree2'=>array(
            'type'=>'enemy',
            'poly'=>array(
              array(119,10),
              array(238,316),
              array(0,316),
              array(119,11),
            ),
            'layer'=>2,'w'=>238,'h'=>316,'y1'=>220,'img'=>'/img/asteroids/resources.png',
            'bgPos'=>'0px -186px',
            //'layer'=>2,'w'=>460,'h'=>350,'y1'=>270,'img'=>'/img/asteroids2/planet2.png',
            'ramps'=>array('x1'=>array(
                array('repeat'=>true),
                array('A'=>800,'B'=>800,'steps'=>100),
                array('B'=>-600,'steps'=>200),
              )
            ),
          ),
          'tree3'=>array(
            'type'=>'enemy',
            'poly'=>array(
              array(119,10),
              array(238,316),
              array(0,316),
              array(119,11),
            ),
            'layer'=>2,'w'=>238,'h'=>316,'y1'=>240,'img'=>'/img/asteroids/resources.png',
            'bgPos'=>'0px -186px',
            //'layer'=>2,'w'=>308,'h'=>381,'y1'=>250,'img'=>'/img/asteroids2/planet3.png',
            'ramps'=>array('x1'=>array(
                array('repeat'=>true),
                array('A'=>800,'B'=>800,'steps'=>200),
                array('B'=>-600,'steps'=>200),
              )
            ),
          ),
          'star1'=>array(
            'type'=>'enemy',
            'layer'=>2,'w'=>37,'h'=>37,'y1'=>50,'img'=>'/img/asteroids/resources.png',
            'bgPos'=>'-193px -37px',
            'ramps'=>array('x1'=>array(
                array('repeat'=>true),
                array('A'=>740,'B'=>740,'steps'=>50),
                array('A'=>740,'B'=>-100,'steps'=>50),
                array('B'=>-600,'steps'=>200),
              )
            ),
          ),
          'star2'=>array(
            'type'=>'enemy',
            'layer'=>2,'w'=>37,'h'=>37,'y1'=>10,'img'=>'/img/asteroids/resources.png',
            'bgPos'=>'-193px -37px',
            'ramps'=>array('x1'=>array(
                array('repeat'=>true),
                array('A'=>740,'B'=>740,'steps'=>250),
                array('B'=>-100,'steps'=>40),
              )
            ),
          ),
          'star3'=>array(
            'type'=>'enemy',
            'layer'=>2,'w'=>37,'h'=>37,'y1'=>80,'img'=>'/img/asteroids/resources.png',
            'bgPos'=>'-193px 0px',
            'ramps'=>array('x1'=>array(
                array('repeat'=>true),
                array('A'=>740,'B'=>740,'steps'=>350),
                array('B'=>-100,'steps'=>30),
              )
            ),
          ),
          'star4'=>array(
            'type'=>'enemy',
            'layer'=>2,'w'=>37,'h'=>37,'y1'=>80,'img'=>'/img/asteroids/resources.png',
            'bgPos'=>'-193px 0px',
            'ramps'=>array(
              'x1'=>array(
                array('repeat'=>true),
                array('A'=>740,'B'=>740,'steps'=>330),
                array('B'=>-100,'steps'=>40),
              ),
              'y1'=>array(
                array('repeat'=>true),
                array('A'=>80,'B'=>80,'steps'=>330),
                array('B'=>120,'steps'=>40),
                array('B'=>20,'steps'=>330),
                array('B'=>60,'steps'=>40),
              ),
            ),
          ),
          'ship'=>array(
            'type'=>'ship',
            'poly'=>array(
              array(110,12),
              array(240,35),
              array(110,62),
              array(110,13),
            ),
            'layer'=>2,'w'=>240,'h'=>74,'y1'=>10,'x1'=>-20,'img'=>'/img/asteroids/resources.png',
            //'layer'=>2,'w'=>200,'h'=>114,'y1'=>0,'x1'=>0,'img'=>'/img/asteroids2/ship.png',
            //'layer'=>2,'w'=>250,'h'=>142,'y1'=>0,'x1'=>0,'img'=>'/img/asteroids2/RocketScrutinizer.png',

            'ramps'=>array(
              'sprite'=>array(
                array('repeat'=>true),
                array('A'=>'0 -502px','B'=>'0 -502px','steps'=>3),
                array('B'=>'0 -582px','steps'=>2),
                array('B'=>'0 -662px','steps'=>2),
                array('B'=>'0 -582px','steps'=>2),
              ),
            ),
            /*
            'ramps'=>array(
              'sprite'=>array(
                array('repeat'=>true),
                array('A'=>'0 0px','B'=>'0 0px','steps'=>3),
                array('B'=>'0 -142px','steps'=>2),
                array('B'=>'0 -284px','steps'=>2),
                array('B'=>'0 -142px','steps'=>2),
              ),
            ),
            */

          ),
        );

  /**
   * @var array<mixed> $dictionary
   */
  private array $dictionary = [];
  private string $language = 'en';
  private int $maxPoints=20;

  private ?string $submitUrl = null;
  private string $winAction = 'game/win';
  private string $bangAction = 'game/bang';

  /**
  * constructor
  *
  * @param \Waxedphp\Waxedphp\php\Base $base
  */
  public function __construct(\Waxedphp\Waxedphp\php\Base $base){
    parent::__construct($base);
    $this->submitUrl = $this->base->getAjaxUrl();
    $this->dictionary['en']=array(
        'YAREADY'=>'Hi! Ready to win some discount?',
        'GAMEOVR'=>'Game over! You won {perc}% discount! ',
        'YAWIN'=>'Great! You won maximum discount {perc}% !',
        'BTNINST'=>'Instructions',
        'BTNSTRT'=>'Play',
        'BTNAGAN'=>'Play Again',
        'BTNREDM'=>'Redeem your discount',
        'OR'=>'or',
        'INSTR'=>'<strong>Goal of the game</strong><br>You should try to evade<br>trees and flying stars<br>with the rocket.<br><br><strong>Instruction navigation</strong><br>Click above/under the<br>rocket or use the arrow<br>keys up/down.',
    );
    $this->dictionary['de']=array(
        'YAREADY'=>'Grüezi. Sind Sie bereit einen Rabatt zu gewinnen?',
        'GAMEOVR'=>'Das Spiel ist fertig! Sie haben {perc}% Rabatt gewonnen!',
        'YAWIN'=>'Grossartig! Sie haben das Maximum von {perc}% Rabatt gewonnen.',
        'BTNINST'=>'Anleitung',
        'BTNSTRT'=>'Play',
        'BTNAGAN'=>'Spiel nochmals',
        'BTNREDM'=>'Jetzt Rabatt einlösen',
        'OR'=>'oder',
        'INSTR'=>'<strong>Ziel des Spiels</strong><br>Sie sollen mit der Rakete<br>den Bäumen und fliegenden<br>Sternen ausweichen.<br><br><strong>Anleitung Navigation</strong><br>Klicken Sie über/unter die<br>Rakete oder benutzen Sie<br>die Pfeiltasten hoch/runter.',
    );
    $this->dictionary['fr']=array(
        'YAREADY'=>'Bonjour. Etes-vous prêt(e) à gagner un rabais ?',
        'GAMEOVR'=>'Le jeu est terminé ! Vous avez gagné un rabais de {perc}% !',
        'YAWIN'=>'Super ! Vous avez gagné le rabais maximum de {perc}% !',
        'BTNINST'=>'Instructions',
        'BTNSTRT'=>'Play',
        'BTNAGAN'=>'Rejouer',
        'BTNREDM'=>'Profitez de votre rabais ',
        'OR'=>'ou',
        'INSTR'=>'<strong>But du jeu</strong><br>Faites avancer la fusée tout<br>en évitant les arbres et les<br>étoiles.<br><br><strong>Navigation</strong><br>Cliquez au-dessus ou en-dessous<br>de la fusée ou utilisez les<br>touches fléchées haut/bas.' ,
    );
    $this->dictionary['sk'] = array(
        'YAREADY'=>'Ahoj, si pripravený(á) vyhrať zľavu?',
        'GAMEOVR'=>'Hra sa skončila! Vyhral(a) si {perc}% zľavu !',
        'YAWIN'=>'Super! Vyhral(a) si maximálnu zľavu {perc}% !',
        'BTNINST'=>'INŠTRUKCIE',
        'BTNSTRT'=>'ŠTARTUJ HRU',
        'BTNAGAN'=>'HRAJ ZNOVA',
        'BTNREDM'=>'BER ZĽAVU',
        'OR'=>'alebo',
        'INSTR'=>'Klikaním myšou alebo tlačítkami so šípkami riadiš raketu medzi stromami a hviezdami.'
    );
  }

  /**
  * set language
  *
  * @param string $lng
  * @return object
  */
  function setLanguage(string $lng): object {
    if (isset($this->dictionary[$lng])) {
      $this->language = $lng;
    } else {
      $this->language = 'en';
    };
    return $this;
  }

  /**
  * set csrf
  *
  * @param string $token
  * @param string $hash
  * @return object
  */
  public function setCSRF(string $token, string $hash): object {
    $this->token = $token;
    $this->hash = $hash;
    return $this;
  }


  /**
  * value
  *
  * @param mixed $idGame
  * @return array<mixed>
  */
  public function value(mixed $idGame): array {
    $a = array(
      'items'=>$this->items,
      'screens'=>$this->screens,
      'dictionary'=>$this->dictionary[$this->language],
      'maxPoints'=>intval($this->maxPoints),
      'ppHack'=>true,
      'submitUrl'=>$this->submitUrl,
      'bangAction'=>$this->bangAction,
      'winAction'=>$this->winAction,
      'play' => true,
      'idGame' => $idGame,
    );
    if ($this->token) {
      $a['token'] = $this->token;
      $a['hash'] = $this->hash;
    }
    return $a;
  }

  /**
  * get items
  *
  * @return array<mixed>
  */
  public function getItems(): array {
    return $this->items;
  }

  /**
  * get screens
  *
  * @return array<mixed>
  */
  public function getScreens(): array {
    $o = $this->screens;
    foreach ($o as $k=>$v) $o[$k]['type']='screen';
    return $o;
  }

  /**
  * set items
  *
  * @param array<mixed> $dd
  * @return object
  */
  public function setItems(array $dd): object {
    $this->items = [];
    foreach ($dd as $o) {
      $oo = [];
      $oo['type']=$o['type'];
      if ($o['poly']!==null) $oo['poly']=json_decode($o['poly'],true);

      $oo['layer']=intval($o['layer']);
      $oo['img']=$o['img'];
      $oo['w']=intval($o['w']);
      $oo['h']=intval($o['h']);

        if ($o['x1']!==null) $oo['x1']=intval($o['x1']);
        if ($o['y1']!==null) $oo['y1']=intval($o['y1']);
        if(isset($o['x2']))
          if ($o['x2']!==null) $oo['x2']=intval($o['x2']);
        if(isset($o['y2']))
          if ($o['y2']!==null) $oo['y2']=intval($o['y2']);
        if ($o['bgpos']!==null) $oo['bgPos']=$o['bgpos'];
        if ($o['ramps']!==null) $oo['ramps']=json_decode($o['ramps'],true);
      if ($oo['type']=='screen') {
        $this->screens[$o['slug']] = $oo;
      } else {
        $this->items[$o['slug']] = $oo;
      }
    }
    return $this;
  }

  /**
  * set dictionary
  *
  * @param array<mixed> $dd
  * @return object
  */
  public function setDictionary(array $dd): object {
    foreach ($dd as $row) {
      $lng = $row['lang'];
      if (!isset($this->dictionary[$lng])) $this->dictionary[$lng] = [];
      $this->dictionary[$lng][$row['slug']] = $row['text'];
    }
    return $this;
  }

}

