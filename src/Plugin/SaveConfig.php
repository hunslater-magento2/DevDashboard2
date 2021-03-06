<?php

namespace Firegento\DevDashboard\Plugin;

class SaveConfig
{

    protected $_whitelist =[
       'user_id',
       'configuration',
       'use_devdashboard'
    ];

    protected $_configRepository;

    protected $_messageManager;

    /**
     * SaveConfig constructor.
     * @param \Firegento\DevDashboard\Model\ConfigRepository $configRepository
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Firegento\DevDashboard\Model\ConfigRepository $configRepository,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_configRepository = $configRepository;
        $this->_messageManager = $messageManager;
    }


    /**
     * @param \Magento\User\Controller\Adminhtml\User\Save $subject
     * @return array
     * @throws \Exception
     */
    public function afterExecute(\Magento\User\Controller\Adminhtml\User\Save $subject)
    {
        $userId = (int)$subject->getRequest()->getParam('user_id');
        $data = $this->_filterData($subject->getRequest()->getParams());

        try {

            /** @var \Firegento\DevDashboard\Model\Config $model */
            $model = $this->_configRepository->getByUserId($userId);
            $model->setData($data);
            $this->_configRepository->save($model);
        } catch (\Exception $e) {
            $this->_messageManager->addErrorMessage($e->getMessage());
        }
        return [];
    }

    /**
     * @param $data
     * @return array
     */
    protected function _filterData($data)
    {
        $filtered = [];

        foreach ($this->_whitelist as $key) {
            $filtered[$key] = $data[$key];
        }
        return $filtered;
    }
}
