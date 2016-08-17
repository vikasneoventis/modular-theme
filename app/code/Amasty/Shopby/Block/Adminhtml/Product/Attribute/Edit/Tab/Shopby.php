<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Block\Adminhtml\Product\Attribute\Edit\Tab;

use Amasty\Shopby\Model\FilterSetting;
use Amasty\Shopby\Model\FilterSettingFactory;
use Amasty\Shopby\Model\Source\DisplayMode;
use Amasty\Shopby\Model\Source\MeasureUnit;
use Amasty\Shopby\Model\Source\MultipleValuesLogic;
use Amasty\Shopby\Model\Source\ShowProductQuantities;
use Amasty\Shopby\Model\Source\SortOptionsBy;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Catalog\Model\Entity\Attribute;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

class Shopby extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var Yesno
     */
    protected $yesNo;

    /** @var  DisplayMode */
    protected $displayMode;

    /** @var  MeasureUnit */
    protected $measureUnitSource;

    /** @var  MultipleValuesLogic */
    protected $multipleValuesLogic;

    /** @var  FilterSetting */
    protected $setting;

    /** @var Attribute $attributeObject */
    protected $attributeObject;

    /**
     * @var SortOptionsBy
     */
    protected $sortOptionsBy;

    /**
     * @var ShowProductQuantities
     */
    protected $showProductQuantities;

    /**
     * @var \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory
     */
    protected $dependencyFieldFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Yesno $yesNo,
        DisplayMode $displayMode,
        MeasureUnit $measureUnitSource,
        FilterSettingFactory $settingFactory,
        SortOptionsBy $sortOptionsBy,
        ShowProductQuantities $showProductQuantities,
        \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $dependencyFieldFactory,
        MultipleValuesLogic $multipleValuesLogic,
        array $data = []
    ) {
        $this->yesNo = $yesNo;
        $this->displayMode = $displayMode;
        $this->measureUnitSource = $measureUnitSource;
        $this->setting = $settingFactory->create();
        $this->attributeObject = $registry->registry('entity_attribute');
        $this->displayMode->setAttributeType($this->attributeObject->getBackendType());
        $this->sortOptionsBy = $sortOptionsBy;
        $this->showProductQuantities = $showProductQuantities;
        $this->dependencyFieldFactory = $dependencyFieldFactory;
        $this->multipleValuesLogic = $multipleValuesLogic;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $this->prepareFilterSetting();
        $form->setDataObject($this->setting);

        $yesnoSource = $this->yesNo->toOptionArray();
        /** @var  $dependence \Magento\SalesRule\Block\Widget\Form\Element\Dependence */
        $dependence = $this->getLayout()->createBlock(
            'Magento\SalesRule\Block\Widget\Form\Element\Dependence'
        );

        $fieldsetDisplayProperties = $form->addFieldset(
            'shopby_fieldset_display_properties',
            ['legend' => __('Display Properties'), 'collapsable' => $this->getRequest()->has('popup')]
        );

        $displayModeField = $fieldsetDisplayProperties->addField(
            'display_mode',
            'select',
            [
                'name'     => 'display_mode',
                'label'    => __('Display Mode'),
                'title'    => __('Display Mode'),
                'values'   => $this->displayMode->toOptionArray(),
            ]
        );
        $dependence->addFieldMap(
            $displayModeField->getHtmlId(),
            $displayModeField->getName()
        );

        $fieldDisplayModeSliderDependencyNegative = $this->dependencyFieldFactory->create(
            ['fieldData' => ['value' => (string)DisplayMode::MODE_SLIDER, 'negative'=>true], 'fieldPrefix' => '']
        );



        $sortOptionsByField = $fieldsetDisplayProperties->addField(
            'sort_options_by',
            'select',
            [
                'name'     => 'sort_options_by',
                'label'    => __('Sort Options By'),
                'title'    => __('Sort Options By'),
                'values'   => $this->sortOptionsBy->toOptionArray(),
            ]
        );

        $dependence->addFieldMap(
            $sortOptionsByField->getHtmlId(),
            $sortOptionsByField->getName()
        );

        $dependence->addFieldDependence(
            $sortOptionsByField->getName(),
            $displayModeField->getName(),
            $fieldDisplayModeSliderDependencyNegative
        );

        $showProductQuantitiesField = $fieldsetDisplayProperties->addField(
            'show_product_quantities',
            'select',
            [
                'name'     => 'show_product_quantities',
                'label'    => __('Show Product Quantities'),
                'title'    => __('Show Product Quantities'),
                'values'   => $this->showProductQuantities->toOptionArray(),
            ]
        );

        $dependence->addFieldMap(
            $showProductQuantitiesField->getHtmlId(),
            $showProductQuantitiesField->getName()
        );



        $dependence->addFieldDependence(
            $showProductQuantitiesField->getName(),
            $displayModeField->getName(),
            $fieldDisplayModeSliderDependencyNegative
        );

        $showSearchBoxField = $fieldsetDisplayProperties->addField(
            'is_show_search_box',
            'select',
            [
                'name'     => 'is_show_search_box',
                'label'    => __('Show Search Box'),
                'title'    => __('Show Search Box'),
                'values'   => $this->yesNo->toOptionArray(),
            ]
        );

        $dependence->addFieldMap(
            $showSearchBoxField->getHtmlId(),
            $showSearchBoxField->getName()
        );

        $dependence->addFieldDependence(
            $showSearchBoxField->getName(),
            $displayModeField->getName(),
            DisplayMode::MODE_DEFAULT
        );

        $numberUnfoldedOptions = $fieldsetDisplayProperties->addField(
            'number_unfolded_options',
            'text',
            [
                'name'     => 'number_unfolded_options',
                'label'    => __('Number of unfolded options'),
                'title'    => __('Number of unfolded options'),
            ]
        );

        $dependence->addFieldMap(
            $numberUnfoldedOptions->getHtmlId(),
            $numberUnfoldedOptions->getName()
        );

        $dependence->addFieldDependence(
            $numberUnfoldedOptions->getName(),
            $displayModeField->getName(),
            DisplayMode::MODE_DEFAULT
        );



        $fieldsetDisplayProperties->addField(
            'is_expanded',
            'select',
            [
                'name'     => 'is_expanded',
                'label'    => __('Expand'),
                'title'    => __('Expand'),
                'values'   =>  $this->yesNo->toOptionArray(),
            ]
        );


        $fieldsetDisplayProperties->addField(
            'tooltip',
            'textarea',
            [
                'name'     => 'tooltip',
                'label'    => __('Tooltip'),
                'title'    => __('Tooltip'),
            ]
        );



        $fieldsetFiltering = $form->addFieldset(
            'shopby_fieldset_filtering',
            ['legend' => __('Filtering'), 'collapsable' => $this->getRequest()->has('popup')]
        );

        $fieldsetFiltering->addField(
            'filter_code',
            'hidden',
            [
                'name'     => 'filter_code',
                'value'   => $this->setting->getFilterCode(),
            ]
        );



        if($this->attributeObject->getBackendType() != 'decimal') {
            $multiselectField = $fieldsetFiltering->addField(
                'is_multiselect',
                'select',
                [
                    'name'     => 'is_multiselect',
                    'label'    => __('Allow Multiselect'),
                    'title'    => __('Allow Multiselect'),
                    'values'   => $yesnoSource,
                ]
            );
            $dependence->addFieldMap(
                $multiselectField->getHtmlId(),
                $multiselectField->getName()
            )->addFieldDependence(
                $multiselectField->getName(),
                $displayModeField->getName(),
                DisplayMode::MODE_DEFAULT
            );

            $useAndLogicField = $fieldsetFiltering->addField(
                'is_use_and_logic',
                'select',
                [
                    'name'     => 'is_use_and_logic',
                    'label'    => __('Multiple Values Logic'),
                    'title'    => __('Multiple Values Logic'),
                    'values'   => $this->multipleValuesLogic->toOptionArray(),
                ]
            );

            $dependence->addFieldMap(
                $useAndLogicField->getHtmlId(),
                $useAndLogicField->getName()
            )->addFieldDependence(
                $useAndLogicField->getName(),
                $multiselectField->getName(),
                1
            )->addFieldDependence(
                $useAndLogicField->getName(),
                $displayModeField->getName(),
                DisplayMode::MODE_DEFAULT
            );

            $fieldsetDisplayProperties->addField(
                'hide_one_option',
                'select',
                [
                    'name'     => 'hide_one_option',
                    'label'    => __('Hide filter when only one option available'),
                    'title'    => __('Hide filter when only one option available'),
                    'values'   => $yesnoSource,
                ]
            );
        } else {
            $useCurrencySymbolField = $fieldsetDisplayProperties->addField(
                'units_label_use_currency_symbol',
                'select',
                [
                    'name'     => 'units_label_use_currency_symbol',
                    'label'    => __('Measure Units'),
                    'title'    => __('Measure Units'),
                    'values'   => $this->measureUnitSource->toOptionArray(),
                ]
            );
            $dependence->addFieldMap(
                $useCurrencySymbolField->getHtmlId(),
                $useCurrencySymbolField->getName()
            );

            $unitsLabelField = $fieldsetDisplayProperties->addField(
                'units_label',
                'text',
                [
                    'name'     => 'units_label',
                    'label'    => __('Unit label'),
                    'title'    => __('Unit label'),
                ]
            );

            $dependence->addFieldMap(
                $unitsLabelField->getHtmlId(),
                $unitsLabelField->getName()
            );

            $dependence->addFieldDependence(
                $unitsLabelField->getName(),
                $useCurrencySymbolField->getName(),
                MeasureUnit::CUSTOM
            );

            $sliderStepField = $fieldsetDisplayProperties->addField(
                'slider_step',
                'text',
                [
                    'name'     => 'slider_step',
                    'label'    => __('Slider Step'),
                    'title'    => __('Slider Step'),
                ]
            );

            $dependence->addFieldMap(
                $sliderStepField->getHtmlId(),
                $sliderStepField->getName()
            )->addFieldDependence(
                $sliderStepField->getName(),
                $displayModeField->getName(),
                DisplayMode::MODE_SLIDER
            );
        }



        $this->setChild(
            'form_after',
            $dependence
        );


        $this->_eventManager->dispatch('amshopby_attribute_form_tab_build_after', ['form' => $form, 'setting' => $this->setting]);

        $this->setForm($form);
        $data = $this->setting->getData();
        if(isset($data['slider_step'])) {
            $data['slider_step'] = round($data['slider_step'], 4);
        }
        $form->setValues($data);
        return parent::_prepareForm();
    }

    protected function prepareFilterSetting()
    {
        if ($this->attributeObject->getId()) {
            $filterCode = 'attr_' . $this->attributeObject->getAttributeCode();
            $this->setting->load($filterCode, 'filter_code');
            $this->setting->setFilterCode($filterCode);
        }
    }
}
