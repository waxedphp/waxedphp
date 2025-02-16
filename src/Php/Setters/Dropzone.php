<?php
namespace Waxedphp\Waxedphp\Php\Setters;

class Dropzone extends AbstractSetter {

  /**
   * Has to be specified on elements other than form
   * (or when the form doesn't have an action attribute).
   * You can also provide a function that will be called with files and must return the url (since v3.12.0)
   * @var ?string $url
   */
  protected ?string $url = null;

  /**
   * method
   *
   * @var ?string $url
   */
  protected ?string $method = null;//|"post"|Can be changed to "put" if necessary. You can also provide a function that will be called with files and must return the method (since v3.12.0).

  /**
   * withCredentials
   *
   * @var ?string $withCredentials
   */
  protected ?string $withCredentials = null;//|false|Will be set on the XHRequest.

  /**
   * timeout
   *
   * @var ?int $timeout
   */
  protected ?int $timeout = null;//|null|The timeout for the XHR requests in milliseconds (since v4.4.0). If set to null or 0, no timeout is going to be set.

  /**
   * parallelUploads
   *
   * @var ?int $parallelUploads
   */
  protected ?int $parallelUploads = null;//|2|How many file uploads to process in parallel (See the Enqueuing file uploads documentation section for more info)

  /**
   * uploadMultiple
   *
   * @var ?bool $uploadMultiple
   */
  protected ?bool $uploadMultiple = null;//|false|Whether to send multiple files in one request. If this it set to true, then the fallback file input element will have the multiple attribute as well. This option will also trigger additional events (like processingmultiple). See the events documentation section for more information.

  /**
   * chunking
   *
   * @var ?bool $chunking
   */
  protected ?bool $chunking = null;//|false|Whether you want files to be uploaded in chunks to your server. This can't be used in combination with uploadMultiple. See [chunksUploaded](#config-chunksUploaded) for the callback to finalise an upload.

  /**
   * forceChunking
   *
   * @var ?bool $forceChunking
   */
  protected ?bool $forceChunking = null;//|false|If chunking is enabled, this defines whether **every** file should be chunked, even if the file size is below chunkSize. This means, that the additional chunk form data will be submitted and the chunksUploaded callback will be invoked.

  /**
   * chunkSize
   *
   * @var ?int $chunkSize
   */
  protected ?int $chunkSize = null;//|2000000|If chunking is true, then this defines the chunk size in bytes.

  /**
   * parallelChunkUploads
   *
   * @var ?bool $parallelChunkUploads
   */
  protected ?bool $parallelChunkUploads = null;//|false|If true, the individual chunks of a file are being uploaded simultaneously.

  /**
   * retryChunks
   *
   * @var ?bool $retryChunks
   */
  protected ?bool $retryChunks = null;//|false|Whether a chunk should be retried if it fails.

  /**
   * retryChunksLimit
   *
   * @var ?int $retryChunksLimit
   */
  protected ?int $retryChunksLimit = null;//|3|If retryChunks is true, how many times should it be retried.

  /**
   * maxFilesize
   *
   * @var ?int $maxFilesize
   */
  protected ?int $maxFilesize = null;//|256|The maximum filesize (in megabytes) that is allowed to be uploaded.

  /**
   * paramName
   *
   * @var ?string $paramName
   */
  protected ?string $paramName = null;//|"file"|The name of the file param that gets transferred. **NOTE**: If you have the option uploadMultiple set to true, then Dropzone will append \[\] to the name.

  /**
   * createImageThumbnails
   *
   * @var ?bool $createImageThumbnails
   */
  protected ?bool $createImageThumbnails = null;//|true|Whether thumbnails for images should be generated

  /**
   * maxThumbnailFilesize
   *
   * @var ?int $maxThumbnailFilesize
   */
  protected ?int $maxThumbnailFilesize = null;//|10|In MB. When the filename exceeds this limit, the thumbnail will not be generated.

  /**
   * thumbnailWidth
   *
   * @var ?int $thumbnailWidth
   */
  protected ?int $thumbnailWidth = null;//|120|If null, the ratio of the image will be used to calculate it.

  /**
   * thumbnailHeight
   *
   * @var ?int $thumbnailHeight
   */
  protected ?int $thumbnailHeight = null;//|120|The same as thumbnailWidth. If both are null, images will not be resized.

  /**
   * thumbnailMethod
   *
   * @var ?string $thumbnailMethod
   */
  protected ?string $thumbnailMethod = null;//|"crop"|How the images should be scaled down in case both, thumbnailWidth and thumbnailHeight are provided. Can be either contain or crop.

  /**
   * resizeWidth
   *
   * @var ?int $resizeWidth
   */
  protected ?int $resizeWidth = null;//null|If set, images will be resized to these dimensions before being **uploaded**. If only one, resizeWidth **or** resizeHeight is provided, the original aspect ratio of the file will be preserved. The options.transformFile function uses these options, so if the transformFile function is overridden, these options don't do anything.

  /**
   * resizeHeight
   *
   * @var ?int $resizeHeight
   */
  protected ?int $resizeHeight = null;//|See resizeWidth.

  /**
   * resizeMimeType
   *
   * @var ?string $resizeMimeType
   */
  protected ?string $resizeMimeType = null;// = null|The mime type of the resized image (before it gets uploaded to the server). If null the original mime type will be used. To force jpeg, for example, use image/jpeg. See resizeWidth for more information.

  /**
   * resizeQuality
   *
   * @var ?float $resizeQuality
   */
  protected ?float $resizeQuality = null;//|0.8|The quality of the resized images. See resizeWidth.

  /**
   * resizeMethod
   *
   * @var ?string $resizeMethod
   */
  protected ?string $resizeMethod = null;//|"contain"|How the images should be scaled down in case both, resizeWidth and resizeHeight are provided. Can be either contain or crop.

  /**
   * filesizeBase
   *
   * @var ?int $filesizeBase
   */
  protected ?int $filesizeBase = null;//|1000|The base that is used to calculate the **displayed** filesize. You can change this to 1024 if you would rather display kibibytes, mebibytes, etc... 1024 is technically incorrect, because 1024 bytes are 1 kibibyte not 1 kilobyte. You can change this to 1024 if you don't care about validity.

  /**
   * maxFiles
   *
   * @var ?int $maxFiles
   */
  protected ?int $maxFiles = null;//|null|If not null defines how many files this Dropzone handles. If it exceeds, the event maxfilesexceeded will be called. The dropzone element gets the class dz-max-files-reached accordingly so you can provide visual feedback.

  /**
   * headers
   *
   * @var ?array<mixed> $headers
   */
  protected ?array $headers = null;//|null|An optional object to send additional headers to the server. Eg: { "My-Awesome-Header": "header value" }

  /**
   * clickable
   *
   * @var ?bool $clickable
   */
  protected ?bool $clickable = null;//|true|If true, the dropzone element itself will be clickable, if false nothing will be clickable. You can also pass an HTML element, a CSS selector (for multiple elements) or an array of those. In that case, all of those elements will trigger an upload when clicked.

  /**
   * ignoreHiddenFiles
   *
   * @var ?bool $ignoreHiddenFiles
   */
  protected ?bool $ignoreHiddenFiles = null;//|true|Whether hidden files in directories should be ignored.

  /**
   * acceptedFiles
   *
   * @var ?string $acceptedFiles
   */
  protected ?string $acceptedFiles = null;//|null|The default implementation of accept checks the file's mime type or extension against this list. This is a comma separated list of mime types or file extensions. Eg.: image/*,application/pdf,.psd If the Dropzone is clickable this option will also be used as [accept](https://developer.mozilla.org/en-US/docs/HTML/Element/input#attr-accept) parameter on the hidden file input as well.
  //acceptedMimeTypes|null|**Deprecated!** Use acceptedFiles instead.

  /**
   * autoProcessQueue
   *
   * @var ?bool $autoProcessQueue
   */
  protected ?bool $autoProcessQueue = null;//|true|If false, files will be added to the queue but the queue will not be processed automatically. This can be useful if you need some additional user input before sending files (or if you want want all files sent at once). If you're ready to send the file simply call myDropzone.processQueue(). See the [enqueuing file uploads](#enqueuing-file-uploads) documentation section for more information.

  /**
   * autoQueue
   *
   * @var ?bool $autoQueue
   */
  protected ?bool $autoQueue = null;//|true|If false, files added to the dropzone will not be queued by default. You'll have to call enqueueFile(file) manually.

  /**
   * addRemoveLinks
   *
   * @var ?bool $addRemoveLinks
   */
  protected ?bool $addRemoveLinks = null;//|false|If true, this will add a link to every file preview to remove or cancel (if already uploading) the file. The dictCancelUpload, dictCancelUploadConfirmation and dictRemoveFile options are used for the wording.

  /**
   * previewsContainer
   *
   * @var ?string $previewsContainer
   */
  protected ?string $previewsContainer = null;//|null|Defines where to display the file previews – if null the Dropzone element itself is used. Can be a plain HTMLElement or a CSS selector. The element should have the dropzone-previews class so the previews are displayed properly.

  /**
   * disablePreviews
   *
   * @var ?bool $disablePreviews
   */
  protected ?bool $disablePreviews = null;//|false|Set this to true if you don't want previews to be shown.

  /**
   * hiddenInputContainer
   *
   * @var ?string $hiddenInputContainer
   */
  protected ?string $hiddenInputContainer = null;//|"body"|This is the element the hidden input field (which is used when clicking on the dropzone to trigger file selection) will be appended to. This might be important in case you use frameworks to switch the content of your page. Can be a selector string, or an element directly.

  /**
   * capture
   *
   * @var ?string $capture
   */
  protected ?string $capture = null;//|null|If null, no capture type will be specified If camera, mobile devices will skip the file selection and choose camera If microphone, mobile devices will skip the file selection and choose the microphone If camcorder, mobile devices will skip the file selection and choose the camera in video mode On apple devices multiple must be set to false. AcceptedFiles may need to be set to an appropriate mime type (e.g. "image/*", "audio/*", or "video/*").
  //protected string renameFilename|null|**Deprecated**. Use renameFile instead.
  //protected string renameFile|null|A function that is invoked before the file is uploaded to the server and renames the file. This function gets the File as argument and can use the file.name. The actual name of the file that gets used during the upload can be accessed through file.upload.filename.

  /**
   * forceFallback
   *
   * @var ?bool $forceFallback
   */
  protected ?bool $forceFallback = null;//|false|If true the fallback will be forced. This is very useful to test your server implementations first and make sure that everything works as expected without dropzone if you experience problems, and to test how your fallbacks will look.

  /**
   * existingFiles
   *
   * @var ?array<mixed> $existingFiles
   */
  protected ?array $existingFiles = null;

  /**
   * allowed options
   *
   * @var array<mixed> $_allowedOptions
   */
  protected array $_allowedOptions = [
    'url',
    'method',
    'withCredentials',
    'timeout',
    'parallelUploads',
    'uploadMultiple',
    'chunking',
    'forceChunking',
    'chunkSize',
    'parallelChunkUploads',
    'retryChunks',
    'retryChunksLimit',
    'maxFilesize',
    'paramName',
    'createImageThumbnails',
    'maxThumbnailFilesize',
    'thumbnailWidth',
    'thumbnailHeight',
    'thumbnailMethod',
    'resizeWidth',
    'resizeHeight',
    'resizeMimeType',
    'resizeQuality',
    'resizeMethod',
    'filesizeBase',
    'maxFiles',
    'headers',
    'clickable',
    'ignoreHiddenFiles',
    'acceptedFiles',
    'acceptedMimeTypes',
    'autoProcessQueue',
    'autoQueue',
    'addRemoveLinks',
    'previewsContainer',
    'disablePreviews',
    'hiddenInputContainer',
    'capture',
    'renameFilename',
    'renameFile',
    'forceFallback',
    'existingFiles'
  ];

  /**
  * value
  *
  * @param mixed $value
  * @return array<mixed>
  */
  public function value(mixed $value): array {
    $a = $this->getArrayOfAllowedOptions();
    $a['value'] = $value;
    return $a;
  }

}
