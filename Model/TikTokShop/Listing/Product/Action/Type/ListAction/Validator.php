<?php

declare(strict_types=1);

namespace M2E\TikTokShop\Model\TikTokShop\Listing\Product\Action\Type\ListAction;

use M2E\TikTokShop\Model\TikTokShop\Listing\Product\Action;

class Validator implements \M2E\TikTokShop\Model\TikTokShop\Listing\Product\Action\Type\ValidatorInterface
{
    use Action\Type\ValidatorTrait;

    private Action\Validator\VariantValidator $variantValidator;
    /** @var \M2E\TikTokShop\Model\TikTokShop\Listing\Product\Action\Validator\ValidatorInterface[] */
    private array $validators;

    public function __construct(
        Action\Validator\VariantValidator $variantValidator,
        array $validators = []
    ) {
        $this->variantValidator = $variantValidator;
        $this->validators = $validators;
    }

    public function validate(
        \M2E\TikTokShop\Model\Product $product,
        \M2E\TikTokShop\Model\TikTokShop\Listing\Product\Action\Configurator $actionConfigurator,
        \M2E\TikTokShop\Model\TikTokShop\Listing\Product\Action\VariantSettings $variantSettings
    ): bool {
        if (!$product->isListable()) {
            $this->addErrorMessage((string)__('Item is Listed or not available'));

            return false;
        }

        if (!$actionConfigurator->isVariantsAllowed()) {
            $this->addErrorMessage((string)__('The product was not listed because it has no associated products.'));

            return false;
        }

        if (!$product->isGlobalProduct()) {
            $this->validateProductBy(
                $product,
                $actionConfigurator,
                $this->validators
            );
        }

        $variantErrors = $this->variantValidator->validate($product, $variantSettings);
        foreach ($variantErrors as $variantError) {
            $this->addErrorMessage($variantError);
        }

        return !$this->hasErrorMessages();
    }
}
