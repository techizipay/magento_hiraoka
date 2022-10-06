<?php
/**
 * Copyright © Lyra Network.
 * This file is part of Mi Cuenta Web plugin for Magento 2. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Lyranetwork\Micuentaweb\Model\System\Config\Backend;

class ShipOptions extends \Lyranetwork\Micuentaweb\Model\System\Config\Backend\Serialized\ArraySerialized\ConfigArraySerialized
{
    public function beforeSave()
    {
        $data = $this->getGroups('micuentaweb'); // Get data of general config group.
        $oneyContract = isset($data['fields']['oney_contract']['value']) && $data['fields']['oney_contract']['value'];

        if ($oneyContract) {
            $values = $this->getValue();

            if (! is_array($values) || empty($values)) {
                $this->setValue([]);
            } else {
                $i = 0;
                foreach ($values as $id => $value) {
                    $i ++;

                    if (empty($value)) {
                        continue;
                    }

                    if (! isset($value['type']) || empty($value['type'])
                        || ! isset($value['speed']) || empty($value['speed'])) {
                        unset($values[$id]);
                    }
                }

                $this->setValue($values);
            }
        } else {
            $this->setValue([]);
        }

        return parent::beforeSave();
    }
}
