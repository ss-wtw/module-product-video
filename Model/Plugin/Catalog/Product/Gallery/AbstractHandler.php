<?php
/**
 * @category  SixtySeven
 * @package   SixtySeven_ProductVideo
 * @author    67Digital http://67digital.com/
 */
namespace SixtySeven\UploadVideo\Model\Plugin\Catalog\Product\Gallery;

/**
 * Abstract class for catalog product gallery handlers plugins.
 */
abstract class AbstractHandler
{
    /**
     * @var array
     */
    protected $videoPropertiesDbMapping = [
        'value_id' => 'value_id',
        'store_id' => 'store_id',
        'video_provider' => 'provider',
        'video_url' => 'url',
        'video_title' => 'title',
        'video_description' => 'description',
        'video_metadata' => 'metadata'
    ];
    /**
     * Image uploader
     *
     * @var \Magento\Catalog\Model\ImageUploader
     */
    protected $imageUploader;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Gallery
     */
    protected $resourceModel;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Gallery $resourceModel
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Gallery $resourceModel,
        \Magento\Catalog\Model\ImageUploader $imageUploader
    ) {
        $this->resourceModel = $resourceModel;
        $this->imageUploader = $imageUploader;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     * @return array
     */
    protected function getMediaEntriesDataCollection(
        \Magento\Catalog\Model\Product $product,
        \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
    ) {
        $attributeCode = $attribute->getAttributeCode();
        $mediaData = $product->getData($attributeCode);
        if (!empty($mediaData['images']) && is_array($mediaData['images'])) {
            return $mediaData['images'];
        }
        return [];
    }
}
