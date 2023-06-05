<?php

declare(strict_types=1);

namespace MadeByMouses\StorePickup\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class IsActive implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Enabled'),
                'value' => 1,
            ],
            [
                'label' => __('Disabled'),
                'value' => 0,
            ],
        ];
    }
}
