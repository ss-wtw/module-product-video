<?php
/**
 * @category  SixtySeven
 * @package   SixtySeven_ProductVideo
 * @author    67Digital http://67digital.com/
 */

namespace SixtySeven\UploadVideo\Controller\Adminhtml\Upload;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Newpost extends \Magento\Backend\App\Action
{
    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var array
     */
    protected $allowedExtensions = ['mp4','ogg','ogv','webm'];

    /**
     * @var string
     */
    protected $fileId = 'uploaded_video_url_tmp';
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * 
     * @param Action\Context                                  $context          [description]
     * @param Filesystem                                      $fileSystem       [description]
     * @param UploaderFactory                                 $uploaderFactory  [description]
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory [description]
     */
    public function __construct(
        Action\Context $context,
        Filesystem $fileSystem,
        UploaderFactory $uploaderFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        $this->fileSystem = $fileSystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->resultRawFactory = $resultRawFactory;
        parent::__construct($context);
    }

 	
    /**
     * 
     * @return \Magento\Framework\Controller\Result\Raw $response 
     */
    public function execute()
    {
    	 
        $destinationPath = $this->getDestinationPath();

        try {
            $uploader = $this->uploaderFactory->create(['fileId' => $this->fileId])
                ->setAllowCreateFolders(true)
                ->setAllowedExtensions($this->allowedExtensions)
                ->setAllowRenameFiles(true)
                ->setFilesDispersion(true)
                ->addValidateCallback('validate', $this, 'validateFile');
           	$result = $uploader->save($destinationPath);
            if (!$result) {
                throw new LocalizedException(
                    __('File cannot be saved to path: $1', $destinationPath)
                );
            } 
            unset($result['tmp_name']);
            unset($result['path']);

            $result['url'] = $this->_objectManager->get(\Magento\Catalog\Model\Product\Media\Config::class)
                ->getTmpMediaUrl($result['file']);

            $result['uid'] = md5(uniqid(rand(), true));

            $result['file'] = $result['file'] . '.tmp';
            $result['uploaded_path'] = $result['file'];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));
        return $response;
    }
     
    /**
     * get destination path for temp media to upload file
     * @return string  absolute path
     */
    public function getDestinationPath()
    {
        return $this->fileSystem
            ->getDirectoryWrite(DirectoryList::MEDIA)
            ->getAbsolutePath('tmp/catalog/product');
    }
}