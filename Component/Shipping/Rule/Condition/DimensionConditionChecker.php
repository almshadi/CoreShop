<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Component\Shipping\Rule\Condition;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;

class DimensionConditionChecker extends AbstractConditionChecker
{
    /**
     * {@inheritdoc}
     */
    public function isShippingRuleValid(CarrierInterface $carrier, ShippableInterface $cart, AddressInterface $address, array $configuration)
    {
        $height = $configuration['height'];
        $width = $configuration['width'];
        $depth = $configuration['depth'];

        foreach ($cart->getItems() as $item) {
            if ($height > 0) {
                if ($item->getHeight() > $height) {
                    return false;
                }
            }

            if ($depth > 0) {
                if ($item->getDepth() > $depth) {
                    return false;
                }
            }

            if ($width > 0) {
                if ($item->getWidth() > $width) {
                    return false;
                }
            }
        }

        return true;
    }
}
