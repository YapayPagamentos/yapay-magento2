<?php

namespace Yapay\Magento2\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;



class InstallData implements InstallDataInterface
{
    /**
     * Customer setup factory
     *
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;
    /**
     * Init
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(\Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory)
    {
        \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Psr\Log\LoggerInterface')
            ->debug(json_encode('oi to no setup de construtor'));
        $this->customerSetupFactory = $customerSetupFactory;
    }
    /**
     * Installs DB schema for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Psr\Log\LoggerInterface')
            ->debug(json_encode('oi to no setup de novo'));
        $installer = $setup;
        $installer->startSetup();
        $this->createCPF($setup);
        $this->createCNPJ($setup);
        $this->CreatePhone($setup);
        $installer->endSetup();

    }

    /**
     * Método utilizado para cadastrar e editar cpf
     * @param $setup
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createCPF($setup)
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $entityTypeId = $customerSetup->getEntityTypeId(\Magento\Customer\Model\Customer::ENTITY);
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, "cpf");
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, "cnpj");

        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, "cpf",  array(
            "type"     => "varchar",
            "backend"  => "",
            "label"    => "CPF",
            "input"    => "text",
            "source"   => "",
            "visible"  => true,
            "required" => true,
            "default" => "",
            "frontend" => "",
            "unique"     => false,
            "note"       => ""

        ));

        $customerSetup->getAttribute(\Magento\Customer\Model\Customer::ENTITY, "cpf");

        $cpf = $customerSetup->getEavConfig()->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'cpf');
        $used_in_forms[]="adminhtml_customer";
        $used_in_forms[]="checkout_register";
        $used_in_forms[]="customer_account_create";
        $used_in_forms[]="customer_account_edit";
        $used_in_forms[]="adminhtml_checkout";
        $cpf->setData("used_in_forms", $used_in_forms)
            ->setData("is_used_for_customer_segment", true)
            ->setData("is_system", 0)
            ->setData("is_user_defined", 1)
            ->setData("is_visible", 1)
            ->setData("sort_order", 100);

        $cpf->save();

    }

    /**
     * Método utilizado para cadastrar e editar cnpj
     * @param $setup
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createCNPJ($setup)
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $entityTypeId = $customerSetup->getEntityTypeId(\Magento\Customer\Model\Customer::ENTITY);
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, "cnpj");
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, "cnpj");

        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, "cnpj",  array(
            "type"     => "varchar",
            "backend"  => "",
            "label"    => "CNPJ",
            "input"    => "text",
            "source"   => "",
            "visible"  => true,
            "required" => false,
            "default" => "",
            "frontend" => "",
            "unique"     => false,
            "note"       => ""

        ));

        $customerSetup->getAttribute(\Magento\Customer\Model\Customer::ENTITY, "cnpj");

        $cnpj = $customerSetup->getEavConfig()->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'cnpj');
        $used_in_forms[]="adminhtml_customer";
        $used_in_forms[]="checkout_register";
        $used_in_forms[]="customer_account_create";
        $used_in_forms[]="customer_account_edit";
        $used_in_forms[]="adminhtml_checkout";
        $cnpj->setData("used_in_forms", $used_in_forms)
            ->setData("is_used_for_customer_segment", true)
            ->setData("is_system", 0)
            ->setData("is_user_defined", 1)
            ->setData("is_visible", 1)
            ->setData("sort_order", 100);

        $cnpj->save();
    }

    /**
     * Método utilizado para cadastrar e editar telefone
     * @param $setup
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function CreatePhone($setup)
    {
        {
            \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Psr\Log\LoggerInterface')
                ->debug(json_encode('oi to no setup de  telefone'));
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $entityTypeId = $customerSetup->getEntityTypeId(\Magento\Customer\Model\Customer::ENTITY);
            $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, "phone");

            $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, "phone",  array(
                "type"     => "varchar",
                "backend"  => "",
                "label"    => "Telefone/Celular",
                "input"    => "text",
                "source"   => "",
                "visible"  => true,
                "required" => true,
                "default" => "",
                "frontend" => "",
                "unique"     => false,
                "note"       => ""

            ));

            $customerSetup->getAttribute(\Magento\Customer\Model\Customer::ENTITY, "phone");

            $phone = $customerSetup->getEavConfig()->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'phone');
            $used_in_forms[]="adminhtml_customer";
            $used_in_forms[]="checkout_register";
            $used_in_forms[]="customer_account_create";
            $used_in_forms[]="customer_account_edit";
            $used_in_forms[]="adminhtml_checkout";
            $phone->setData("used_in_forms", $used_in_forms)
                ->setData("is_used_for_customer_segment", true)
                ->setData("is_system", 0)
                ->setData("is_user_defined", 1)
                ->setData("is_visible", 1)
                ->setData("sort_order", 100);

            $phone->save();
        }
    }
}