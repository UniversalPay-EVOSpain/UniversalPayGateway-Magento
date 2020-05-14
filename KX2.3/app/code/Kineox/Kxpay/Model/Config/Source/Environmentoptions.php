<?php
namespace Kineox\Kxpay\Model\Config\Source;

class Environmentoptions  implements \Magento\Framework\Option\ArrayInterface
{
	
    public function toOptionArray()
    {
    	$arr = $this->toArray();
        $ret = [];
        foreach ($arr as $key => $value) {
            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }
        return $ret;
    }

	public function toArray()
	{
		  $array = [
            0 => __('Test - Entorno de pruebas'),
            1 => __('Producción - Entorno real'),
        ];
        return $array;
	}
	
}