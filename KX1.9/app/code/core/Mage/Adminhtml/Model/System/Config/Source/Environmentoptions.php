<?php
class Mage_Adminhtml_Model_System_Config_Source_Environmentoptions
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Test - Entorno de pruebas')),
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Producción - Entorno real'))
        );
    }

	public function toArray()
	{
		return array(
			0 => Mage::helper('adminhtml')->__('Test - Entorno de pruebas'),
			1 => Mage::helper('adminhtml')->__('Producción - Entorno real')
		);
	}
}