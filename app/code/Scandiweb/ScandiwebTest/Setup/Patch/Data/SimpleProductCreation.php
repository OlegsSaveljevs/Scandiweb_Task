<?php
/**
 * @category Scandiweb
 * @package Scandiweb\ScandiwebTest
 * @author Olegs Saveljevs <olegs.saveljevs@scandiweb.com>
 * @copyright Copyright (c) 2025 Scandiweb, Ltd (https://scandiweb.com)
 * @license   http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Scandiweb\ScandiwebTest\Setup\Patch\Data;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;

class SimpleProductCreation implements DataPatchInterface
{
    protected const SAMPLE_SKU = 'scandiweb-sample-product';
    protected const SAMPLE_CATEGORY_IDS = [3, 4];

    /**
     * @param ProductInterfaceFactory $productInterfaceFactory
     * @param ProductRepositoryInterface $productRepository
     * @param CategoryLinkManagementInterface $categoryLinkManagement
     * @param State $state
     * @param EavSetup $eavSetup
     * @param SourceItemInterfaceFactory $sourceItemFactory
     * @param array $sourceItems
     * @param SourceItemsSaveInterface $sourceItemsSaveInterface
     * @param CategoryLinkManagementInterface $categoryLink
     */
    public function __construct(
        protected ProductInterfaceFactory $productInterfaceFactory,
        protected ProductRepositoryInterface $productRepository,
        protected CategoryLinkManagementInterface $categoryLinkManagement,
        protected State $state,
        protected EavSetup $eavSetup,
        protected SourceItemInterfaceFactory $sourceItemFactory,
        protected SourceItemsSaveInterface $sourceItemsSaveInterface,
        protected categoryLinkManagementInterface $categoryLink,
        protected array $sourceItems = [],

    ) {
    }
    /**
     * @return void
     * @throws Exception
     */
    public function apply(): void
    {
        try {
            $this->state->emulateAreaCode('adminhtml', [$this, 'execute']);
        } catch (Exception $e) {
        }
    }

    /**
     * @return void
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws StateException
     * @throws LocalizedException
     */
    public function execute(): void
    {
        $product = $this->productInterfaceFactory->create();

        if ($product->getIdBySku(self::SAMPLE_SKU)) {
            return;
        }

        $attributeSetId = $this->eavSetup->getAttributeSetId(Product::ENTITY, 'Default');
        $product->setSku(self::SAMPLE_SKU)
            ->setAttributeSetId($attributeSetId)
            ->setName('Scandiweb Sample Product')
            ->setPrice(99.99)
            ->setTypeId(Type::TYPE_SIMPLE)
            ->setVisibility(Visibility::VISIBILITY_BOTH)
            ->setStatus(Status::STATUS_ENABLED);
        $product = $this->productRepository->save($product);

        $sourceItem = $this->sourceItemFactory->create();
        $sourceItem->setSourceCode('default');
        $sourceItem->setQuantity(100);
        $sourceItem->setSku($product->getSku());
        $sourceItem->setStatus(SourceItemInterface::STATUS_IN_STOCK);
        $this->sourceItems[] = $sourceItem;
        $this->sourceItemsSaveInterface->execute($this->sourceItems);
        $this->categoryLink->assignProductToCategories($product->getSku(), self::SAMPLE_CATEGORY_IDS);
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }
}