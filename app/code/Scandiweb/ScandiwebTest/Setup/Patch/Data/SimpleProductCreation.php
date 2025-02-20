<?php

namespace Scandiweb\ScandiwebTest\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;

class SimpleProductCreation implements DataPatchInterface
{
    public function __construct(
        protected ProductInterfaceFactory $productInterfaceFactory,
        protected ProductRepositoryInterface $productRepository,
        protected CategoryLinkManagementInterface $categoryLinkManagement,
        protected CategoryRepositoryInterface $categoryRepository,
        protected State $state
    ) {}

    public function apply()
    {

        try {

            $this->state->setAreaCode('adminhtml');
        } catch (LocalizedException $e) {
        }

        $product = $this->productInterfaceFactory->create();
        $product->setSku('scandiweb_sample_product')
            ->setName('Scandiweb Sample Product')
            ->setPrice(99.99)
            ->setTypeId('simple')
            ->setVisibility(4)
            ->setStatus(1)
            ->setAttributeSetId(4);

        $savedProduct = $this->productRepository->save($product);

        $this->categoryLinkManagement->assignProductToCategories(
            $savedProduct->getSku(),
            [3, 4]
        );
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
