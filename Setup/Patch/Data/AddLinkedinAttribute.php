<?php
namespace MagentoTest\CustomerLinkedin\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Config;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Customer\Model\ResourceModel\Attribute as CustomerAttributeResourceModel;

class AddLinkedinAttribute implements DataPatchInterface
{
    /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;

    /** @var EavSetupFactory */
    private $eavSetupFactory;
    /**
     * @var Config
     */
    private Config $eavConfig;
    /**
     * @var CustomerAttributeResourceModel
     */
    private CustomerAttributeResourceModel $customerAttributeResourceModel;
    /**
     * @var CustomerSetupFactory
     */
    private CustomerSetupFactory $customerSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param Config $eavConfig
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig,
        CustomerAttributeResourceModel $customerAttributeResourceModel,
        CustomerSetupFactory $customerSetupFactory,
        SetFactory $attributeSetFactory

    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->eavConfig       = $eavConfig;
        $this->customerAttributeResourceModel = $customerAttributeResourceModel;
        $this->attributeSetFactory = $attributeSetFactory;

    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply()
    {
        $setup = $this->moduleDataSetup;
        $setup->startSetup();
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerSetup->removeAttribute(Customer::ENTITY, 'linkedin_url');
        $customerSetup->addAttribute(
            Customer::ENTITY,
            'linkedin_url',
            [

                'label'                 => 'Profile Linkedin',
                'input'                 => 'text',
                'required'              => false,
                'sort_order'            => 1000,
                'position'              => 1000,
                'visible'               => true,
                'system'                => false,
                'is_used_in_grid'       => false,
                'is_visible_in_grid'    => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'user_defined' => true,
            ]
        );
        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        /** @var $attributeSet Set */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY,'linkedin_url');

        $attribute->addData(['used_in_forms' => [
            'adminhtml_customer',
            'customer_account_create',
            'customer_account_edit'
        ]])->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId
        ]);

        $this->customerAttributeResourceModel->save($attribute);

        $setup->endSetup();

    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
