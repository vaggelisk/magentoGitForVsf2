<?php

namespace Netsteps\Base\Plugin\Attribute\Source;

class TablePlugin
{
    /**
     * Fixes issue with resolving a numeric option label
     * @param \Magento\Eav\Model\Entity\Attribute\Source\Table $subject
     * @param callable $proceed
     * @param string $label
     * @return int|null
     */
    public function aroundGetOptionId(
        \Magento\Eav\Model\Entity\Attribute\Source\Table $subject,
        callable $proceed,
        string $label
    ): ?int
    {
        $optionId = $proceed($label);

        $valueFromAttribute = $subject->getOptionText($optionId);

        if($label == $valueFromAttribute) {
            return $optionId;
        }

        // option is numeric and returned
        $options = $subject->getAllOptions(false);

        foreach ($options as $option){
            if ($this->mbStrcasecmp($option['label'], $label) === 0) {
                return $option['value'];
            }
        }

        return null;
    }

    /**
     * @param string $str1
     * @param string $str2
     * @return int
     */
    private function mbStrcasecmp(string $str1, string $str2): int
    {
        $encoding = mb_internal_encoding();
        return strcmp(
            mb_strtoupper($str1, $encoding),
            mb_strtoupper($str2, $encoding)
        );
    }

}
