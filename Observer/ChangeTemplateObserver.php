<?php
/**
 * @category  SixtySeven
 * @package   SixtySeven_ProductVideo
 * @author    67Digital http://67digital.com/
 */
namespace SixtySeven\UploadVideo\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Observer to change template for gallery
 */
class ChangeTemplateObserver implements ObserverInterface
{
    /**
     * @param mixed $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $observer->getBlock()->setTemplate('SixtySeven_UploadVideo::helper/gallery.phtml');
    }
}
