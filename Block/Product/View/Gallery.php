<?php
/**
 * @category  SixtySeven
 * @package   SixtySeven_ProductVideo
 * @author    67Digital http://67digital.com/
 */

/**
 * extended block class of media gallery
 */
namespace SixtySeven\UploadVideo\Block\Product\View;
 
class Gallery extends \Magento\Catalog\Block\Product\View\Gallery
{
    /**
     * @var \SixtySeven\UploadVideo\Helper\Media
     */
    protected $mediaHelper;

    
    /**
     * 
     * @param \Magento\Catalog\Block\Product\Context   $context     [description]
     * @param \Magento\Framework\Stdlib\ArrayUtils     $arrayUtils  [description]
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder [description]
     * @param \SixtySeven\UploadVideo\Helper\Media     $mediaHelper [description]
     * @param array                                    $data        [description]
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \SixtySeven\UploadVideo\Helper\Media $mediaHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $arrayUtils,
            $jsonEncoder,
            $data
        );
        $this->mediaHelper = $mediaHelper;
    }

    /**
     * Retrieve media gallery data in JSON format
     *
     * @return string
     */
    public function getMediaGalleryDataJson()
    {
        $mediaGalleryData = [];
        $catMediaUrl = trim($this->mediaHelper->getCatalogMediaUrl(),'/');

        foreach ($this->getProduct()->getMediaGalleryImages() as $mediaGalleryImage) {
            $videoUrl = $mediaGalleryImage->getVideoUrl();
            $mediatype = $mediaGalleryImage->getMediaType();

            if($mediatype=='external-uploaded-video'){
                $videoUrl = $catMediaUrl.$videoUrl;
            }
            
            $mediaGalleryData[] = [
                'mediaType' => $mediatype,
                'videoUrl' => $videoUrl,
                'isBase' => $this->isMainImage($mediaGalleryImage),
            ];
        }
        return $this->jsonEncoder->encode($mediaGalleryData);
    }

    /**
     * Retrieve video settings data in JSON format
     *
     * @return string
     */
    public function getVideoSettingsJson()
    {
        $videoSettingData[] = [
            'playIfBase' => $this->mediaHelper->getPlayIfBaseAttribute(),
            'showRelated' => $this->mediaHelper->getShowRelatedAttribute(),
            'videoAutoRestart' => $this->mediaHelper->getVideoAutoRestartAttribute(),
        ];
        return $this->jsonEncoder->encode($videoSettingData);
    }

    /**
     * Return media gallery for product options
     * @return string
     * @since 100.1.0
     */
    public function getOptionsMediaGalleryDataJson()
    {
        return  $this->jsonEncoder->encode([]);
    }
}
