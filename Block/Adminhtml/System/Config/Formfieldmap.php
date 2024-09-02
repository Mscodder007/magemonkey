<?php
namespace Magemonkey\RMA\Block\Adminhtml\System\Config;

class Formfieldmap extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
	protected $_groupRenderer;
	protected $_selectRenderer;
	protected $_isenableselectRenderer;
	protected $_columns = [];
	protected $_addAfter = true;
/**
*  set block to field
*
* @return block
*/
protected function _getGroupRenderer()
{
	if (!$this->_groupRenderer) {
		$this->_groupRenderer = $this->getLayout()->createBlock(
			\Magemonkey\RequestQuote\Block\Adminhtml\Form\Field\Fieldsgroupp::class,
			'',
			['data' => ['is_render_to_js_template' => true]]
		);
		$this->_groupRenderer->setClass('formfields_group_select');
	}
	return $this->_groupRenderer;
}
/**
*  set block to field
*
* @return block
*/
protected function _getSelectRenderer()
{
	if (!$this->_selectRenderer) {
		$this->_selectRenderer = $this->getLayout()->createBlock(
			\Magemonkey\RequestQuote\Block\Adminhtml\Form\Field\SelectGroupp::class,
			'',
			['data' => ['is_render_to_js_template' => true]]
		);
		$this->_selectRenderer->setClass('formfields_option_select');
	}
	return $this->_selectRenderer;
}
/**
*  set block to field
*
* @return block
*/
protected function _getEnableSelectRenderer()
{
	if (!$this->_isenableselectRenderer) {
		$this->_isenableselectRenderer = $this->getLayout()->createBlock(
			\Magemonkey\RequestQuote\Block\Adminhtml\Form\Field\IsEnableSelect::class,
			'',
			['data' => ['is_render_to_js_template' => true]]
		);
		$this->_isenableselectRenderer->setClass('formfields_isenable_select');
	}
	return $this->_isenableselectRenderer;
}
/**
*  set fields of dynamic rows
*
*
*/
protected function _prepareToRender()
{
	$this->addColumn('label', ['label' => __('Label'), 'class' => 'required-entry', 'style' => 'width:125px;']);

	//$this->addColumn('type', ['label' => __('Type'), 'class' => 'required-entry', 'renderer' => $this->_getGroupRenderer()]);

	//$this->addColumn('order', ['label' => __('Sort Order'), 'class' => 'required-entry', 'style' => 'width:40px;']);
	$this->addColumn('required', ['label' => __('Required'),'class' => 'required-entry', 'renderer' => $this->_getSelectRenderer()]);
    $this->addColumn('enable', ['label' => __('Enable'), 'class' => 'required-entry', 'renderer' => $this->_getEnableSelectRenderer()]);




	$this->_addAfter = false;
	$this->_addButtonLabel = __('Add');
}
/**
*  set fields saved data
*
* @return block
*/
public function renderCellTemplate($columnName)
{
	if ($columnName == "type") {
		$this->_columns[$columnName]['style'] = 'width:150px';
	}
	return parent::renderCellTemplate($columnName);
}
/**
*  set fields saved data
*
*
*/
protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
{



$optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->_getGroupRenderer()->calcOptionHash($row->getData('type'))] =
            'selected="selected"';
        $optionExtraAttr['option_' . $this->_getSelectRenderer()->calcOptionHash($row->getData('required'))] =
            'selected="selected"';
        $optionExtraAttr['option_' . $this->_getEnableSelectRenderer()->calcOptionHash($row->getData('enable'))] =
            'selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
}
}