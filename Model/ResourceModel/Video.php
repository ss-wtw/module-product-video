<?php
/**
 * @category  SixtySeven
 * @package   SixtySeven_ProductVideo
 * @author    67Digital http://67digital.com/
 */
namespace SixtySeven\UploadVideo\Model\ResourceModel;

class Video extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        /*using same table to store video data which is using by product-video-module*/
        $this->_init(\SixtySeven\UploadVideo\Setup\InstallSchema::GALLERY_VALUE_VIDEO_TABLE, 'value_id');
    }

    /**
     * @param array $data
     * @param array $fields
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function insertOnDuplicate(array $data, array $fields = [])
    {
        return $this->getConnection()->insertOnDuplicate($this->getMainTable(), $data, $fields);
    }

    /**
     * @param array $ids
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByIds(array $ids)
    {
        $select = $this->getConnection()->select()->from(
            $this->getMainTable()
        )->where(
            'value_id IN(?)',
            $ids
        );

        return $this->getConnection()->fetchAll($select);
    }
}
