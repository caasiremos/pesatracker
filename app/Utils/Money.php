<?php

namespace App\Utils;

use Money\Currency;
use NumberFormatter;
use Money\Money as BaseMoney;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;

class Money
{
    public $amount;
    protected $money;

    function __construct($value)
    {
        $this->money = new BaseMoney($value, new Currency('UGX'));
        $this->amount = $value;
    }

    /**
     * Format any string to currency format
     * @param $amount
     * @return String
     */
    public static function formatAmount($amount): string
    {
        $money = new Money($amount);
        return $money->formatted();
    }

    public function formatted()
    {
        $formatter = new IntlMoneyFormatter(
            new NumberFormatter('en', NumberFormatter::CURRENCY),
            new ISOCurrencies()
        );
        return $formatter->format($this->money);
    }

    public function amount()
    {
        return $this->money->getAmount();
    }

    public function add(Money $money)
    {
        $this->money = $this->money->add($money->instance());
        return $this;
    }

    public function instance()
    {
        return $this->money;
    }
}
