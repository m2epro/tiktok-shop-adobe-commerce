<?php

namespace M2E\TikTokShop\Controller\Adminhtml\TikTokShop\Listing\Unmanaged;

class Index extends \M2E\TikTokShop\Controller\Adminhtml\TikTokShop\AbstractListing
{
    use \M2E\TikTokShop\Controller\Adminhtml\Listing\Wizard\WizardTrait;

    private \M2E\TikTokShop\Model\Listing\Wizard\Repository $wizardRepository;
    private \M2E\TikTokShop\Model\Account\Ui\RuntimeStorage $uiAccountRuntimeStorage;
    private \M2E\TikTokShop\Model\Account\Repository $accountRepository;

    public function __construct(
        \M2E\TikTokShop\Model\Listing\Wizard\Repository $wizardRepository,
        \M2E\TikTokShop\Model\Account\Ui\RuntimeStorage $uiAccountRuntimeStorage,
        \M2E\TikTokShop\Model\Account\Repository $accountRepository
    ) {
        parent::__construct();

        $this->uiAccountRuntimeStorage = $uiAccountRuntimeStorage;
        $this->accountRepository = $accountRepository;
        $this->wizardRepository = $wizardRepository;
    }

    public function execute()
    {
        $wizard = $this->wizardRepository->findNotCompletedWizardByType(\M2E\TikTokShop\Model\Listing\Wizard::TYPE_UNMANAGED);

        if (null !== $wizard) {
            $this->getMessageManager()->addNoticeMessage(
                __(
                    'Please make sure you finish adding new Products before moving to the next step.',
                ),
            );

            return $this->redirectToIndex($wizard->getId());
        }

        try {
            $this->loadAccount();
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $this->_redirect('*/tiktokshop_listing/index');
        }

        if ($this->getRequest()->getQuery('ajax')) {
            $this->setAjaxContent(
                $this
                    ->getLayout()
                    ->createBlock(\M2E\TikTokShop\Block\Adminhtml\TikTokShop\Listing\Unmanaged\Grid::class)
            );

            return $this->getResult();
        }

        $this->addContent(
            $this
                ->getLayout()
                ->createBlock(\M2E\TikTokShop\Block\Adminhtml\TikTokShop\Listing\Unmanaged::class)
        );
        $this->getResultPage()->getConfig()->getTitle()->prepend(__('All Unmanaged Items'));
        $this->setPageHelpLink('https://docs-m2.m2epro.com/unmanaged-listings-on-m2e-tiktok-shop');

        return $this->getResult();
    }

    private function loadAccount(): void
    {
        $accountId = $this->getRequest()->getParam('account');
        if (empty($accountId)) {
            $account = $this->accountRepository->getFirst();
        } else {
            $account = $this->accountRepository->get((int)$accountId);
        }

        $this->uiAccountRuntimeStorage->setAccount($account);
    }
}
