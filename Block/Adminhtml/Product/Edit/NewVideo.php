<?php
/**
 * @category  SixtySeven
 * @package   SixtySeven_ProductVideo
 * @author    67Digital http://67digital.com/
 */
namespace SixtySeven\UploadVideo\Block\Adminhtml\Product\Edit;

use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\View\Asset\Repository as AssetRepository;

/**
 * Block for add new video form
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class NewVideo extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Anchor is product video
     */
    const PATH_ANCHOR_PRODUCT_VIDEO = 'catalog_product_video-link';

    /**
     * @var \SixtySeven\UploadVideo\Helper\Media
     */
    protected $mediaHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var string
     */
    protected $videoSelector = '#media_gallery_content';

    /**
     * @var AssetRepository
     */
    protected $assetRepo;
    
    /**
     * 
     * @param \Magento\Backend\Block\Template\Context  $context     [description]
     * @param \Magento\Framework\Registry              $registry    [description]
     * @param \Magento\Framework\Data\FormFactory      $formFactory [description]
     * @param \SixtySeven\UploadVideo\Helper\Media     $mediaHelper [description]
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder [description]
     * @param AssetRepository                          $assetRepo   [description]
     * @param array                                    $data        [description]
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \SixtySeven\UploadVideo\Helper\Media $mediaHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        AssetRepository $assetRepo,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->mediaHelper = $mediaHelper;
        $this->urlBuilder = $context->getUrlBuilder();
        $this->jsonEncoder = $jsonEncoder;
        $this->setUseContainer(true);
        $this->assetRepo  = $assetRepo;
    }

    /**
     * Form preparation
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create([
            'data' => [
                'id' => 'new_uploaded_video_form',
                'class' => 'admin__scope-old',
                'enctype' => 'multipart/form-data',
            ]
        ]);
        $form->setUseContainer($this->getUseContainer());
        $form->addField('new_video_messages', 'note', []);
        $fieldset = $form->addFieldset('new_video_form_fieldset', []);
        $fieldset->addField(
            '',
            'hidden',
            [
                'name' => 'form_key',
                'value' => $this->getFormKey(),
            ]
        );
        $fieldset->addField(
            'uploaded_item_id',
            'hidden',
            []
        );
        $fieldset->addField(
            'uploaded_file_name',
            'hidden',
            []
        );
        $fieldset->addField(
            'uploaded_file_path',
            'hidden',
            [
                'name' => 'uploaded_file_path',
            ]
        );
         
        $fieldset->addField(
            'uploaded_video_provider',
            'hidden',
            [
                'name' => 'uploaded_video_provider',
            ]
        );
        
        $fieldset->addField(
            'uploaded_video_url',
            'file',
            [
                'class' => 'edited-data',
                'label' => __('Upload Video'),
                'title' => __('Upload Video'),
                'required' => true,
                'name' => 'uploaded_video_url',
                'note' => $this->getNoteVideoUrl(),
            ]
        ); 
        $fieldset->addField(
            'uploaded_video_title',
            'text',
            [
                'class' => 'edited-data',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
                'name' => 'uploaded_video_title',
            ]
        );
        $fieldset->addField(
            'uploaded_video_description',
            'textarea',
            [
                'class' => 'edited-data',
                'label' => __('Description'),
                'title' => __('Description'),
                'name' => 'uploaded_video_description',
            ]
        );
        $fieldset->addField(
            'new_uploaded_video_screenshot',
            'file',
            [
                'label' => __('Preview Image'),
                'title' => __('Preview Image'),
                'name' => 'uploaded_image',
            ]
        );
        $fieldset->addField(
            'new_uploaded_video_screenshot_preview',
            'button',
            [
                'class' => 'preview-image-hidden-input',
                'label' => '',
                'name' => 'uploaded_preview',
            ]
        );
         
        $this->addMediaRoleAttributes($fieldset);
        $fieldset->addField(
            'new_uploaded_video_disabled',
            'checkbox',
            [
                'class' => 'edited-data',
                'label' => __('Hide from Product Page'),
                'title' => __('Hide from Product Page'),
                'name' => 'disabled',
            ]
        );
        $this->setForm($form);
    }

    /**
     * Get html id
     *
     * @return mixed
     */
    public function getHtmlId()
    {
        if (null === $this->getData('id')) {
            $this->setData('id', $this->mathRandom->getUniqueHash('id_'));
        }
        return $this->getData('id');
    }

    /**
     * Get widget options
     *
     * @return string
     */
    public function getWidgetOptions()
    {
        return $this->jsonEncoder->encode(
            [
                'saveVideoUrl' => $this->getUrl('productvideo/product_gallery/upload'),
                'uploadVideoUrl' => $this->getUrl('productvideo/upload/newpost'),
                'saveRemoteVideoUrl' => $this->getUrl('product_video/product_gallery/retrieveImage'),
                'htmlId' => $this->getHtmlId(),
                'videoSelector' => $this->videoSelector,
                'placeholderDefault' => $this->assetRepo->getUrlWithParams('SixtySeven_UploadVideo::images/placeholder/video_thumbnail.png', ['_secure'=>true])
            ]
        );
    }

    /**
     * Retrieve currently viewed product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    protected function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', $this->_coreRegistry->registry('product'));
        }
        return $this->getData('product');
    }

    /**
     * Add media role attributes to fieldset
     *
     * @param Fieldset $fieldset
     * @return $this
     */
    protected function addMediaRoleAttributes(Fieldset $fieldset)
    {
        $fieldset->addField('role-label', 'note', ['text' => __('Role')]);
        $mediaRoles = $this->getProduct()->getMediaAttributes();
        ksort($mediaRoles);
        foreach ($mediaRoles as $mediaRole) {
            $fieldset->addField(
                'video_' . $mediaRole->getAttributeCode(),
                'checkbox',
                [
                    'class' => 'video_image_role',
                    'label' => __($mediaRole->getFrontendLabel()),
                    'title' => __($mediaRole->getFrontendLabel()),
                    'data-role' => 'role-type-selector',
                    'value' => $mediaRole->getAttributeCode(),
                ]
            );
        }
        return $this;
    }

    /**
     * Get note for video url
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getNoteVideoUrl()
    {
        $result = __('Only Mp4, Ogg, Ogv, WebM supported.');
         
        return $result;
    }

    /**
     * Get url for config params
     *
     * @return string
     */
    protected function getConfigApiKeyUrl()
    {
        return $this->urlBuilder->getUrl(
            'adminhtml/system_config/edit',
            [
                'section' => 'catalog',
                '_fragment' => self::PATH_ANCHOR_PRODUCT_VIDEO
            ]
        );
    }
}
