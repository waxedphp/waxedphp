<?php

$setter = $waxed->setter('suneditor');
$setter->setButtons([
      ['formatBlock'],['table', 'link', 'image', 'video', 'audio'],
      ['font', 'fontSize'],
      ['bold', 'underline', 'italic', 'strike', 'subscript', 'superscript'],
      ['fullScreen', 'showBlocks', 'codeView'],
      ['preview', 'print'],
      ['save', 'template'],
     ]);
$setter->addTemplate('Template-1','<p>HTML source1</p>')
->addTemplate('Template-2','<h1>NADPIS</h1><p>HTML source2</p>')
->addTemplate('Template-3','<h1>Lárom fárom</h1><p>HTML source2</p>')
->setHeight('400px')
->setMode('classic');

return [
  'payload1' => $setter->value('<h1>Lárom fárom</h1>'),
];

return [
'payload1' =>
  [
    'value' => '
    <h2>Lorem Ipsum!</h2>
    <p>
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean dictum tincidunt elementum. Suspendisse dapibus, sem vel aliquet commodo, magna purus rhoncus sem, id porttitor nunc mauris eget lacus. Cras ac quam ut elit mollis placerat eget nec ante. Suspendisse feugiat sem at tortor varius bibendum. Donec ac orci ac nunc porta gravida. Sed in faucibus enim, vitae ultrices mauris. Morbi bibendum magna ac est pulvinar, non feugiat neque maximus. Morbi venenatis purus id metus lacinia fringilla. Cras et elit eget nisl sollicitudin pulvinar.
</p><p>
Maecenas eu lorem ultricies, molestie libero vel, vulputate neque. Nam ut odio vestibulum enim commodo faucibus eu non risus. Nulla sollicitudin diam eget leo tempus, sit amet consequat nisl sollicitudin. Vivamus nisi massa, convallis ac nisi sit amet, iaculis ullamcorper nunc. Sed eget nisi nisl. Maecenas eget nunc accumsan, tristique quam in, tempus massa. Praesent semper magna eu sapien efficitur, quis bibendum augue gravida. Integer maximus metus vel cursus varius. Donec bibendum malesuada lacinia.
</p><p>
Duis non mollis purus. Sed fermentum sollicitudin ipsum sed fermentum. Vivamus laoreet consequat posuere. Nunc risus libero, fermentum id urna quis, gravida ultrices nisi. Mauris tempor elementum augue et lobortis. Duis posuere dui pellentesque ipsum dignissim, sit amet efficitur lacus finibus. Nunc maximus in massa in laoreet. Sed sit amet eros vel mi cursus blandit. Duis sed hendrerit nunc. Maecenas consectetur nisl a sapien faucibus, auctor tempus elit facilisis. Vivamus fringilla urna turpis, eu lobortis sapien placerat at. Nulla a lacinia erat. Duis vulputate erat vel varius interdum. Curabitur a mi ultricies, ultricies turpis at, mattis eros.
</p><p>
Sed vitae accumsan arcu. Ut arcu dolor, mattis et lacus nec, porttitor malesuada sapien. In blandit ac est elementum bibendum. Maecenas ex dui, maximus vitae libero sit amet, tincidunt dignissim lectus. Aenean eu gravida magna. Fusce placerat, enim aliquam pretium fermentum, neque ante varius tellus, in efficitur odio dolor eget tellus. Aenean iaculis libero ac aliquam vulputate. Nulla tempus vel lacus a scelerisque. Fusce velit tellus, suscipit a tincidunt id, gravida ut nulla. Sed gravida pharetra dignissim. Pellentesque id interdum leo. Suspendisse placerat ullamcorper sollicitudin. In hac habitasse platea dictumst. Proin sed mi et felis scelerisque cursus non quis tortor. Nulla rhoncus nulla est, ut eleifend arcu commodo nec.
</p><p>
Sed mollis imperdiet mauris at malesuada. Aliquam at posuere sapien. Curabitur leo est, molestie sed consequat in, maximus efficitur quam. Nullam mollis, ligula a tristique dignissim, velit sem mollis diam, in consequat neque lectus a turpis. Maecenas urna mi, finibus nec pellentesque vel, ornare a tortor. Etiam purus purus, laoreet a felis in, eleifend dapibus mi. Donec blandit velit felis, euismod vulputate massa condimentum nec. In efficitur nulla et quam tristique, eget hendrerit diam pulvinar. Phasellus nec elit ut orci vestibulum aliquet. Duis ac tortor a purus blandit tincidunt. Donec pulvinar leo ex, sed scelerisque sem bibendum ut. Etiam in mi purus.
    </p>

    ',
    'config' => [
     //'plugins' => ['paragraphStyle','formatBlock'],
    'templates' => [
        [
            'name' => 'Template-1',
            'html' => '<p>HTML source1</p>'
        ],
        [
            'name' => 'Template-2',
            'html' => '<h1>NADPIS</h1><p>HTML source2</p>'
        ],
    ],
     'buttonList' => [
      ['formatBlock'],['table', 'link', 'image', 'video', 'audio'],
      ['font', 'fontSize'],
      ['bold', 'underline', 'italic', 'strike', 'subscript', 'superscript'],
      ['fullScreen', 'showBlocks', 'codeView'],
      ['preview', 'print'],
      ['save', 'template'],
     ],


    ],
  ],

];

