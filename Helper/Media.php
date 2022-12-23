<?php
/**
 * @category  SixtySeven
 * @package   SixtySeven_ProductVideo
 * @author    67Digital http://67digital.com/
 */
namespace SixtySeven\UploadVideo\Helper;

use Magento\Framework\App\Helper\Context;
 
/**
 * Helper to get attributes for video
 */
class Media extends \Magento\ProductVideo\Helper\Media
{
    protected $_urlInterface;
    protected $_storeManager;

    /**
     * @param Context                                    $context      [description]
     * @param \Magento\Framework\UrlInterface            $urlInterface [description]
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager [description]
     */
    public function __construct(
        Context $context,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_urlInterface = $urlInterface;
        parent::__construct($context);
        $this->_storeManager = $storeManager;
    }

    /**
     * get media url for catalog/product
     * @return string url for media/catalog/product
     */
    public function getCatalogMediaUrl(){
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'/catalog/product';
    }
    /**
     * get Store manager Interface
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getStoreManager(){
        return $this->_storeManager;
    } 
}
