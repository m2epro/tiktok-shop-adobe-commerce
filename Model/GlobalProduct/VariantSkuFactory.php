<?php

declare(strict_types=1);

namespace M2E\TikTokShop\Model\GlobalProduct;

class VariantSkuFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(): VariantSku
    {
        return $this->objectManager->create(VariantSku::class);
    }
}
