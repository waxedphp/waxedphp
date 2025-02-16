<?php
namespace JasterStary\Waxed\php\Setters\suneditor;

use \JasterStary\Waxed\php\Setters\AbstractSetter;

class Setter extends AbstractSetter {

  /**
  * height: The height size of the editor. default: clientHeight||'auto' {Number|String}
  * @var ?string $height
  */
  protected ?string $height = null;

  /**
  * minHeight
  * @var string $minHeight
  */
  protected ?string $minHeight = null;

  /**
  * maxHeight
  * @var ?string $maxHeight
  */
  protected ?string $maxHeight = null;

  /**
  * width
  * @var ?string $width
  */
  protected ?string $width = null;

  /**
  * minWidth
  * @var int|string $minWidth
  */
  protected ?string $minWidth = null;

  /**
  * maxWidth
  * @var ?string $maxWidth
  */
  protected ?string $maxWidth = null;

  /**
  * buttonList
  * @var ?array<mixed> $buttonList
  */
  protected ?array $buttonList = null;

  /**
  * templates
  * @var ?array<mixed> $templates
  */
  protected ?array $templates = null;

  /**
  * _templates
  * @var array<mixed> $_templates
  */
  protected array $_templates = [];


  /**
  * mode: 'classic', 'inline', 'balloon', 'balloon-always'
  * @var ?string $mode
  */
  protected ?string $mode = null;

  /**
  * position        : The position property of suneditor.               default: null {String}
  * @var ?string $position
  */
  protected ?string $position = null;
  /**
  * The display property of suneditor. default: null {String}
  * @var ?string $display
  */
  protected ?string $display = null;
  /**
  * popupDisplay: Size of background area when activating dialog window ('full'||'local') default: 'full' {String}
  * @var ?string $popupDisplay
  */
  protected ?string $popupDisplay = null;
  /**
  * resizingBar     : Show the bottom resizing bar.
  * If 'height' value is 'auto', it will not be resized. default: true {Boolean}
  * @var ?bool $resizingBar
  */
  protected ?bool $resizingBar = null;
  /**
  * showPathLabel   : Displays the current node structure to resizingBar.  default: true {Boolean}
  * @var ?bool $showPathLabel
  */
  protected ?bool $showPathLabel = null;
  /**
  * resizeEnable  : Enable/disable resize function of bottom resizing bar.   default: true {Boolean}
  * @var ?bool resizeEnable
  */
  protected ?bool $resizeEnable = null;
  /**
  * resizingBarContainer: A custom HTML selector placing the resizing bar inside.
  * The class name of the element must be 'sun-editor'.
  * Element or querySelector argument.     default: null {Element|String}
  * ex) document.querySelector('#id') || '#id'
  * @var ?string $resizingBarContainer
  */
  protected ?string $resizingBarContainer = null;
  /**
  * charCounter     : Shows the number of characters in the editor.
  * If the maxCharCount option has a value, it becomes true. default: false {Boolean}
  * @var ?bool $charCounter
  */
  protected ?bool $charCounter = null;
  /**
  * charCounterType : Defines the calculation method of the "charCounter" option.
  * 'char': Characters length.
  * 'byte': Binary data size of characters.
  * 'byte-html': Binary data size of the full HTML string.   default: 'char' {String}
  * @var ?string $charCounterType
  */
  protected ?string $charCounterType = null;

  /**
  * charCounterLabel: Text to be displayed in the "charCounter" area of the bottom bar.
  * Screen ex) 'charCounterLabel : 20/200'.           default: null {String}
  * @var ?string $charCounterLabel
  */
  protected ?string $charCounterLabel = null;

  /**
  * maxCharCount: The maximum number of characters allowed to be inserted into the editor.
  * default: null {Number}
  *
  * @var ?int $maxCharCount
  */
  protected ?int $maxCharCount = null;

  /**
  * className: Add a "class" to the editing area[.sun-editor-editable].    default: '' {String}
  * @var ?string $className
  */
  protected ?string $className = null;

  /**
  * defaultStyle: You can define the style of the editing area[.sun-editor-editable].
  * It affects the entire editing area.               default: '' {String}
  * ('z-index', 'position' and 'width' properties apply to the top div.)
  * ex) 'font-family: cursive; font-size: 10px;'
  * @var ?string $defaultStyle
  */
  protected ?string $defaultStyle = null;

  /**
  * font: Change default font-family array.                 default: [...] {Array}
  * Default value: ['Arial', 'Comic Sans MS', 'Courier New', 'Impact',
  * 'Georgia','tahoma', 'Trebuchet MS', 'Verdana']
  *
  * @var ?array<mixed> $font = null;
  */
  protected ?array $font = null;

  /**
  * fontSize: Change default font-size array.                   default: [...] {Array}
  * Default value: [8, 9, 10, 11, 12, 14, 16, 18, 20, 22, 24, 26, 28, 36, 48, 72]
  *
  * @var ?array<int> $fontSize = null;
  */
  protected ?array $fontSize = null;

  /**
  * fontSizeUnit: The font size unit.                               default: 'px' {String}
  * @var ?string $fontSizeUnit
  */
  protected ?string $fontSizeUnit = null;

  /**
  * alignItems: A list of drop-down options for the 'align' plugin.
  * default: rtl === true ? ['right', 'center', 'left', 'justify'] : ['left', 'center', 'right', 'justify'] {Array}
  *
  * @var ?array<mixed> $alignItems = null;
  */
  protected ?array $alignItems = null;


  /**
  * formats: Change default formatBlock array.                 default: [...] {Array}
  * Default value: ['p', 'div', 'blockquote', 'pre', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'
  * // "blockquote": range format, "pre": free format, "Other tags": replace format
  * ],
  * Custom: [{
  *  tag: 'div', // Tag name
  *  name: 'Custom div' || null, // default: tag name
  * command: 'replace' || 'range' || 'free', // default: "replace"
  * class: '__se__format__replace_xxx' || '__se__format__range_xxx' || '__se__format__free_xxx' || '__se__format__free__closure_xxx'
  * // Class names must always begin with "__se__format__(replace, range, free)_"
  * }]
  *
  * @var ?array<mixed> $formats = null;
  */
  protected ?array $formats = null;


  /**
  * colorList: Change default color array of color picker.       default: [..[..]..] {Array}
  * Default value: [
  * '#ff0000', '#ff5e00', '#ffe400', '#abf200', '#00d8ff', '#0055ff', '#6600ff', '#ff00dd', '#000000',
  * '#ffd8d8', '#fae0d4', '#faf4c0', '#e4f7ba', '#d4f4fa', '#d9e5ff', '#e8d9ff', '#ffd9fa', '#f1f1f1',
  * '#ffa7a7', '#ffc19e', '#faed7d', '#cef279', '#b2ebf4', '#b2ccff', '#d1b2ff', '#ffb2f5', '#bdbdbd',
  * '#f15f5f', '#f29661', '#e5d85c', '#bce55c', '#5cd1e5', '#6699ff', '#a366ff', '#f261df', '#8c8c8c',
  * '#980000', '#993800', '#998a00', '#6b9900', '#008299', '#003399', '#3d0099', '#990085', '#353535',
  * '#670000', '#662500', '#665c00', '#476600', '#005766', '#002266', '#290066', '#660058', '#222222'
  * ]
  * ex) [
  * ['#ccc', '#dedede', 'OrangeRed', 'Orange', 'RoyalBlue', 'SaddleBrown'], // Line break
  * ['SlateGray', 'BurlyWood', 'DeepPink', 'FireBrick', 'Gold', 'SeaGreen']
  * ]
  *
  * @var ?array<mixed> $colorList = null;
  */
  protected ?array $colorList = null;


  /**
  * lineHeights: Change default line-height array.                 default: [{}..] {Array}
  * Default value: [{text: '1', value: 1}, {text: '1.15', value: 1.15}, {text: '1.5', value: 1.5}, {text: '2', value: 2} ]
  * ex) [{text: 'Single', value: 1},{text: 'Double', value: 2}]
  *
  * @var ?array<mixed> $lineHeights = null;
  */
  protected ?array $lineHeights = null;

  /**
  * paragraphStyles : You can apply custom class to format.
  * ex) '.sun-editor-editable .__se__customClass'
  * '.sun-editor .__se__customClass' // If you want to apply styles to menu items as well
  * Default value: [{
                        name: 'Spaced', // Format style name
                        class: '__se__p-spaced', // Define style for used class (Class names must always begin with "__se__")
                        _class: '' // You can control the style of the tags displayed in the menu by putting a class on the button of the menu.
                    },
                    {
                        name: 'Bordered',
                        class: '__se__p-bordered'
                    },
                    {
                        name: 'Neon',
                        class: '__se__p-neon'
                    }
                  ]
                  ex) [
                      'spaced', 'neon', // The default value is called by name only and the name is called in the language file.
                      {
                          name: 'Custom',
                          class: '__se__customClass'
                      }
                  ]
  *
  * @var ?array<mixed> $paragraphStyles = null;
  */
  protected ?array $paragraphStyles = null;

  /**
  * textStyles: You can apply custom style or class to selected text.
  * ex(using a class)) '.sun-editor-editable .__se__customClass'
  * '.sun-editor .__se__customClass' // If you want to apply styles to menu items as well
                  Default value: [
                    {
                        name: 'Code',
                        class: '__se__t-code',
                        tag: 'code',
                    },
                    {
                        name: 'Translucent', // Text style name
                        style: 'opacity: 0.5;', // Style query
                        tag: 'span', // Style tag name (default: span)
                        _class: '' // You can control the style of the tags displayed in the menu by putting a class on the button of the menu.
                    },
                    {
                        name: 'Shadow',
                        class: '__se__t-shadow', // Class names (Class names must always begin with "__se__")
                        tag: 'span'
                    }
                  ]
                  ex) [
                      'Code', // The default value is called by name only and the name is called in the language file.
                      {
                          name: 'Emphasis',
                          style: '-webkit-text-emphasis: filled;',
                          tag: 'span'
                      }
                  ]
  *
  *
  * @var ?array<mixed> $textStyles = null;
  */
  protected ?array $textStyles = null;


  /**
  * imageResizing   : Can resize the image.                               default: true {Boolean}
  * @var ?bool $imageResizing
  */
  protected ?bool $imageResizing = null;

  /**
  * imageHeightShow : Choose whether the image height input is visible.   default: true {Boolean}
  * @var ?bool $imageHeightShow
  */
  protected ?bool $imageHeightShow = null;

  /**
  * imageAlignShow  : Choose whether the image align radio buttons are visible.       default: true {Boolean}
  * @var ?bool $imageAlignShow
  */
  protected ?bool $imageAlignShow = null;

  /**
  * imageWidth: The default width size of the image frame.          default: 'auto' {String}
  * @var ?string $imageWidth
  */
  protected ?string $imageWidth = null;

  /**
  * imageHeight     : The default height size of the image frame.         default: 'auto' {String}
  * @var ?string $imageHeight
  */
  protected ?string $imageHeight = null;

  /**
  * imageSizeOnlyPercentage : If true, image size can only be scaled by percentage.   default: false {Boolean}
  * @var ?bool $imageSizeOnlyPercentage
  */
  protected ?bool $imageSizeOnlyPercentage = null;

  /**
  * imageRotation   : Choose whether to image rotation buttons display.
  * When "imageSizeOnlyPercentage" is "true" or  or "imageHeightShow" is "false" the default value is false.
  * If you want the button to be visible, put it a true.     default: true {Boolean}
  * @var ?bool $imageRotation
  */
  protected ?bool $imageRotation = null;

  /**
  * imageFileInput  : Choose whether to create a file input tag in the image upload window.  default: true {Boolean}
  * @var ?bool $imageFileInput
  */
  protected ?bool $imageFileInput = null;

  /**
  * imageUrlInput   : Choose whether to create a image url input tag in the image upload window.
  * If the value of imageFileInput is false, it will be unconditionally.   default: true {Boolean}
  * @var ?bool $imageUrlInput
  */
  protected ?bool $imageUrlInput = null;

  /**
  * imageUploadHeader : Http Header when uploading images.              default: null {Object}
  *
  * @var ?array<mixed> $imageUploadHeader = null;
  */
  protected ?array $imageUploadHeader = null;

  /**
  * imageUploadUrl  : The image upload to server mapping address.       default: null {String}
  * (When not used the "imageUploadUrl" option, image is enters base64 data)
  * ex) "/editor/uploadImage"
                  request format: {
                            "file-0": File,
                            "file-1": File
                        }
                  response format: {
                            "errorMessage": "insert error message",
                            "result": [
                                {
                                    "url": "/download/editorImg/test_image.jpg",
                                    "name": "test_image.jpg",
                                    "size": "561276"
                                }
                            ]
                        }
  * @var ?string $imageUploadUrl
  */
  protected ?string $imageUploadUrl = null;

  /**
  * imageUploadSizeLimit: The size of the total uploadable images (in bytes).
  * Invokes the "onImageUploadError" method.  default: null {Number}
  *
  * @var ?int $imageUploadSizeLimit
  */
  protected ?int $imageUploadSizeLimit = null;

  /**
  * imageMultipleFile: If true, multiple images can be selected.    default: false {Boolean}
  * @var ?bool $imageMultipleFile
  */
  protected ?bool $imageMultipleFile = null;

  /**
  * imageAccept      : Define the "accept" attribute of the input.  default: "*" {String}
  * ex) "*" or ".jpg, .png .."
  * @var ?string $imageAccept
  */
  protected ?string $imageAccept = null;

  /**
  * imageGalleryUrl     : The url of the image gallery, if you use the image gallery.
  * When "imageUrlInput" is true, an image gallery button is created in the image modal.
  * You can also use it by adding "imageGallery" to the button list.   default: null {String}
                      ex) "/editor/getGallery"
                      response format: {
                            "result": [
                                {
                                    "src": "/download/editorImg/test_image.jpg", // @Require
                                    "thumbnail": "/download/editorImg/test_thumbnail.jpg", // @Option - Thumbnail image to be displayed in the image gallery.
                                    "name": "Test image", // @Option - default: src.split('/').pop()
                                    "alt": "Alt text", // @Option - default: src.split('/').pop()
                                    "tag": "Tag name" // @Option
                                }
                            ],
                            "nullMessage": "Text string or HTML string", // It is displayed when "result" is empty.
                            "errorMessage": "Insert error message", // It is displayed when an error occurs.
                        }
                      You can redefine the "plugins.imageGallery.drawItems" method.
  * @var ?string $imageGalleryUrl
  */
  protected ?string $imageGalleryUrl = null;


  /**
  * imageGalleryHeader: Http Header when get image gallery.         default: null {Object}
  *
  * @var ?array<mixed> $imageGalleryHeader = null;
  */
  protected ?array $imageGalleryHeader = null;

  /**
  * videoResizing   : Can resize the video (iframe, video).                         default: true {Boolean}
  * @var ?bool $videoResizing
  */
  protected ?bool $videoResizing = null;

  /**
  * videoHeightShow : Choose whether the video height input is visible.    default: true {Boolean}
  * @var ?bool $videoHeightShow
  */
  protected ?bool $videoHeightShow = null;

  /**
  * videoAlignShow  : Choose whether the video align radio buttons are visible.       default: true {Boolean}
  * @var ?bool $videoAlignShow
  */
  protected ?bool $videoAlignShow = null;

  /**
  * videoRatioShow  : Choose whether the video ratio options is visible.   default: true {Boolean}
  * @var ?bool $videoRatioShow
  */
  protected ?bool $videoRatioShow = null;

  /**
  * videoWidth      : The default width size of the video frame.           default: '100%' {String}
  * @var ?string $videoWidth
  */
  protected ?string $videoWidth = null;

  /**
  * videoHeight     : The default height size of the video frame.          default: '56.25%' {String}
  * @var ?string $videoHeight
  */
  protected ?string $videoHeight = null;

  /**
  * videoSizeOnlyPercentage : If true, video size can only be scaled by percentage.   default: false {Boolean}
  * @var ?bool $videoSizeOnlyPercentage
  */
  protected ?bool $videoSizeOnlyPercentage = null;

  /**
  * videoRotation   : Choose whether to video rotation buttons display.
  * When "videoSizeOnlyPercentage" is "true" or "videoHeightShow" is "false" the default value is false.
  * If you want the button to be visible, put it a true.     default: true {Boolean}
  * @var ?bool $videoRotation
  */
  protected ?bool $videoRotation = null;

  /**
  * videoRatio      : The default aspect ratio of the video.
  * Up to four decimal places are allowed.             default: 0.5625 (16:9) {Float}
  *
  * @var ?float $videoRatio
  */
  protected ?float $videoRatio = null;

  /**
  * videoRatioList  : Video ratio selection options.
                  default: [
                    {name: '16:9', value: 0.5625},
                    {name: '4:3', value: 0.75},
                    {name: '21:9', value: 0.4285}
                  ],
                  ex) [
                    {name: 'Classic Film 3:2', value: 0.6666},
                    {name: 'HD', value: 0.5625}
                  ]
  *
  * @var ?array<mixed> $videoRatioList = null;
  */
  protected ?array $videoRatioList = null;


  /**
  * youtubeQuery    : The query string of a YouTube embedded URL.        default: '' {String}
  * It takes precedence over the value user entered.
  * ex) 'autoplay=1&mute=1&enablejsapi=1&controls=0&rel=0&modestbranding=1'
  * // https://developers.google.com/youtube/player_parameters
  * @var ?string $youtubeQuery
  */
  protected ?string $youtubeQuery = null;

  /**
  * videoFileInput  : Choose whether to create a file input tag in the video upload window.  default: false {Boolean}
  * @var ?bool $videoFileInput
  */
  protected ?bool $videoFileInput = null;

  /**
  * videoUrlInput   : Choose whether to create a video url input tag in the video upload window.
  * If the value of videoFileInput is false, it will be unconditionally.   default: true {Boolean}
  * @var ?bool $videoUrlInput
  */
  protected ?bool $videoUrlInput = null;

  /**
  * videoUploadHeader : Http Header when uploading videos.              default: null {Object}
  *
  * @var ?array<mixed> $videoUploadHeader = null;
  */
  protected ?array $videoUploadHeader = null;


  /**
  * videoUploadUrl  : The video upload to server mapping address.       default: null {String}
                  ex) "/editor/uploadVideo"
                  request format: {
                            "file-0": File,
                            "file-1": File
                        }
                  Use video tags. (supported video formats: '.mp4', '.webm', '.ogg')
                  response format: {
                            "errorMessage": "insert error message",
                            "result": [
                                {
                                    "url": "/download/editorVideos/test_video.mp4",
                                    "name": "test_video.mp4",
                                    "size": "561276"
                                }
                            ]
                        }
  * @var ?string $videoUploadUrl
  */
  protected ?string $videoUploadUrl = null;

  /**
  * videoUploadSizeLimit: The size of the total uploadable videos (in bytes).
  * Invokes the "onVideoUploadError" method.  default: null {Number}
  *
  * @var ?int $videoUploadSizeLimit
  */
  protected ?int $videoUploadSizeLimit = null;

  /**
  * videoMultipleFile: If true, multiple videos can be selected.    default: false {Boolean}
  * @var ?bool $videoMultipleFile
  */
  protected ?bool $videoMultipleFile = null;

  /**
  * videoTagAttrs    : Define "Attributes" of the video tag.                      default: null {Object}
  * ex) { poster: "http://suneditor.com/docs/loading.gif", autoplay: true }
  *
  * @var ?array<mixed> $videoTagAttrs = null;
  */
  protected ?array $videoTagAttrs = null;


  /**
  * videoIframeAttrs : Define "Attributes" of the iframe tag. (Youtube, Vimeo).   default: null {Object}
  * ex) { style: "border: 2px solid red;" }
  *
  * @var ?array<mixed> $videoIframeAttrs = null;
  */
  protected ?array $videoIframeAttrs = null;


  /**
  * videoAccept      : Define the "accept" attribute of the input.  default: "*" {String}
  * ex) "*" or ".mp4, .avi .."
  * @var ?string $videoAccept
  */
  protected ?string $videoAccept = null;

  /**
  * audioWidth      : The default width size of the audio frame.        default: '300px' {String}
  * @var ?string $audioWidth
  */
  protected ?string $audioWidth = null;

  /**
  * audioHeight     : The default height size of the audio frame.       default: '54px' {String}
  * @var ?string $audioHeight
  */
  protected ?string $audioHeight = null;

  /**
  * audioFileInput  : Choose whether to create a file input tag in the audio upload window.  default: false {Boolean}
  * @var ?bool $audioFileInput
  */
  protected ?bool $audioFileInput = null;

  /**
  * audioUrlInput   : Choose whether to create a audio url input tag in the audio upload window.
  * If the value of audioFileInput is false, it will be unconditionally.   default: true {Boolean}
  * @var ?bool $audioUrlInput
  */
  protected ?bool $audioUrlInput = null;

  /**
  * audioUploadHeader : Http Header when uploading audios.              default: null {Object}
  *
  * @var ?array<mixed> $audioUploadHeader = null;
  */
  protected ?array $audioUploadHeader = null;

  /**
  * audioUploadUrl  : The audio upload to server mapping address.       default: null {String}
                  ex) "/editor/uploadAudio"
                  request format: {
                            "file-0": File,
                            "file-1": File
                        }
                  Use audio tags. (supported audio formats: '.mp4', '.webm', '.ogg')
                  response format: {
                            "errorMessage": "insert error message",
                            "result": [
                                {
                                    "url": "/download/editorAudios/test_audio.mp3",
                                    "name": "test_audio.mp3",
                                    "size": "561276"
                                }
                            ]
                        }
  * @var ?string $audioUploadUrl
  */
  protected ?string $audioUploadUrl = null;

  /**
  * audioUploadSizeLimit: The size of the total uploadable audios (in bytes).
  * Invokes the "onAudioUploadError" method.  default: null {Number}
  *
  * @var ?int $audioUploadSizeLimit
  */
  protected ?int $audioUploadSizeLimit = null;

  /**
  * audioMultipleFile: If true, multiple audios can be selected.    default: false {Boolean}
  * @var ?bool $audioMultipleFile
  */
  protected ?bool $audioMultipleFile = null;

  /**
  * audioTagAttrs    : Define "Attributes" of the audio tag.        default: null {Object}
  * ex) { controlslist: "nodownload", autoplay: true }
  *
  * @var ?array<mixed> $audioTagAttrs = null;
  */
  protected ?array $audioTagAttrs = null;


  /**
  * audioAccept      : Define the "accept" attribute of the input.  default: "*" {String}
  * ex) "*" or ".mp3, .wav .."
  * @var ?string $audioAccept
  */
  protected ?string $audioAccept = null;

  /**
  * tableCellControllerPosition : Define position to the table cell controller('cell', 'top'). default: 'cell' {String}
  *
  * @var ?string $tableCellControllerPosition
  */
  protected ?string $tableCellControllerPosition = null;

  /**
  * linkTargetNewWindow : Default checked value of the "Open in new window" checkbox.   default: false {Boolean}
  * @var ?bool $linkTargetNewWindow
  */
  protected ?bool $linkTargetNewWindow = null;

  /**
  * linkProtocol    : Default protocol for the links. ('link', 'image', 'video', 'audio')
  * This applies to all plugins that enter the internet url.   default: null {String}
  *
  * @var ?string $linkProtocol
  */
  protected ?string $linkProtocol = null;

  /**
  * linkRel         : Defines "rel" attribute list of anchor tag.   default: [] {Array}
  *               // https://www.w3schools.com/tags/att_a_rel.asp
                  ex) [
                    'author',
                    'external',
                    'help',
                    'license',
                    'next',
                    'follow',
                    'nofollow',
                    'noreferrer',
                    'noopener',
                    'prev',
                    'search',
                    'tag'
                ]
  *
  *
  * @var ?array<string> $linkRel = null;
  */
  protected ?array $linkRel = null;


  /**
  * linkRelDefault  : Defines default "rel" attributes of anchor tag.   default: {} {Object}
                  ex) linkRelDefault: {
                        default: 'nofollow', // Default rel
                        check_new_window: 'noreferrer noopener', // When "open new window" is checked
                        check_bookmark: 'bookmark' // When "bookmark" is checked
                    },
                    // If properties other than "default" start with "only:", the existing "rel" is cleared and applied.
                    linkRelDefault: {
                        check_new_window: 'only:noreferrer noopener'
                    }
  *
  * @var ?array<mixed> $linkRelDefault = null;
  */
  protected ?array $linkRelDefault = null;

  /**
  * linkNoPrefix   : If true, disables the automatic prefixing of the host URL to the value of the link. default: false {Boolean}
  * @var ?bool $linkNoPrefix
  */
  protected ?bool $linkNoPrefix = null;

  /**
  * hrItems         : Defines the hr items.
                  "class" or "style" must be specified.
                  default: [
                      {name: lang.toolbar.hr_solid, class: '__se__solid'},
                      {name: lang.toolbar.hr_dashed, class: '__se__dashed'},
                      {name: lang.toolbar.hr_dotted, class: '__se__dotted'}
                  ]
                  ex) [ {name: "Outset", style: "border-style: outset;"} ]
  *
  *
  * @var ?array<mixed> $hrItems = null;
  */
  protected ?array $hrItems = null;


  /**
  * tabDisable      : If true, disables the interaction of the editor and tab key.  default: false {Boolean}
  * @var ?bool $tabDisable
  */
  protected ?bool $tabDisable = null;

  /**
  * shortcutsDisable: You can disable shortcuts.    default: [] {Array}
  * ex) ['bold', 'strike', 'underline', 'italic', 'undo', 'indent', 'save']
  *
  * @var ?array<string> $shortcutsDisable = null;
  */
  protected ?array $shortcutsDisable = null;


  /**
  * shortcutsHint   : If false, hide the shortcuts hint.    default: true {Boolean}
  * @var ?bool $shortcutsHint
  */
  protected ?bool $shortcutsHint = null;

// Defining save button-------------------------------------------------------------------------------------------
//callBackSave    : Callback functions that is called when the Save button is clicked.
// Arguments - (contents, isChanged).                            default: functions.save {Function}

  /**
   * allowed options
   *
   * @var array<mixed> $_allowedOptions
   */
  protected array $_allowedOptions = [
    'buttonList','templates',
    'height','width','minHeight','minWidth','maxHeight','maxWidth',
    'mode',
    'position', 'display', 'popupDisplay', 'resizingBar', 'showPathLabel',
    'resizeEnable', 'resizingBarContainer', 'charCounter', 'charCounterType',
    'charCounterLabel', 'maxCharCount', 'className', 'defaultStyle',
    'font', 'fontSize', 'fontSizeUnit', 'alignItems', 'formats',
    'colorList', 'lineHeights', 'paragraphStyles', 'textStyles',
    'imageResizing', 'imageHeightShow', 'imageAlignShow', 'imageWidth', 'imageHeight',
    'imageSizeOnlyPercentage', 'imageRotation', 'imageFileInput', 'imageUrlInput',
    'imageUploadHeader', 'imageUploadUrl', 'imageUploadSizeLimit', 'imageMultipleFile',
    'imageAccept', 'imageGalleryUrl', 'imageGalleryHeader',
    'videoResizing', 'videoHeightShow', 'videoAlignShow', 'videoRatioShow',
    'videoWidth', 'videoHeight', 'videoSizeOnlyPercentage', 'videoRotation',
    'videoRatio', 'videoRatioList', 'youtubeQuery', 'videoFileInput', 'videoUrlInput',
    'videoUploadHeader', 'videoUploadUrl', 'videoUploadSizeLimit', 'videoMultipleFile',
    '$videoTagAttrs', 'videoIframeAttrs', 'videoAccept',
    'audioWidth', 'audioHeight', 'audioFileInput', 'audioUrlInput',
    'audioUploadHeader', 'audioUploadUrl', 'audioUploadSizeLimit',
    'audioMultipleFile', 'audioTagAttrs', 'audioAccept',
    'tableCellControllerPosition', 'linkTargetNewWindow', 'linkProtocol',
    'linkRel', 'linkRelDefault', 'linkNoPrefix', 'hrItems',
    'tabDisable', 'shortcutsDisable', 'shortcutsHint'
  ];

  /**
  * set buttons
  *
  * @param array<mixed> $buttons
  * @return Setter
  */
  public function setButtons(array $buttons): Setter {
    $this->buttonList = $buttons;
    return $this;
  }

  /**
  * add template
  *
  * @param string $name
  * @param string $html
  * @return Setter
  */
  public function addTemplate(string $name, string $html): Setter {
    $this->_templates[$name] = [
      'name' => $name,
      'html' => $html,
    ];
    $this->templates = array_values($this->_templates);
    return $this;
  }

  /**
  * value
  *
  * @param mixed $value
  * @return array<mixed>
  */
  public function value(mixed $value = null): array {
    $a = [];
    $b = $this->getArrayOfAllowedOptions();
    if (!empty($b)) {
      $a['config'] = $b;
    }
    if ($value) $a['value'] = $value;
    return $a;
  }

}
