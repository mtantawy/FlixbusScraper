<?php

namespace FlixbusScraper;

class ViewHelper
{
    private const HEX_COLOR_LOWEST_PRICE = '#00FF00';
    private const HEX_COLOR_HIGHEST_PRICE = '#FF0000';
    private const HEX_COLOR_MOST_COMMON_PRICE = '#FFFF00';
    private const HEX_COLOR_DEFAULT = '#FFFFFF';

    public function getTripBackgroundColor(
        float $price,
        float $lowestPrice,
        float $highestPrice,
        float $mostCommonPrice
    ): string {
        switch ($price) {
            case $lowestPrice:
                return self::HEX_COLOR_LOWEST_PRICE;
                break;

            case $highestPrice:
                return self::HEX_COLOR_HIGHEST_PRICE;
                break;

            case $mostCommonPrice:
                return self::HEX_COLOR_MOST_COMMON_PRICE;
                break;
            
            default:
                return self::HEX_COLOR_DEFAULT;
                break;
        }
    }
}
