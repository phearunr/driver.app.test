<?php

namespace App\Helper;

class Constant
{
    public const ORDER_STATUS = [
        0 => 'Cancel',
        10 => 'in-reviews',
        // 10 => [
        //     'in-reviews',
        //     ['confirm_cash_pay_time' => 0, 'payment_code' => 'cash']
        // ],
        20 => 'To Ship',
        30 => 'To Receive',
        35 => 'Delivered',
        40 => 'Completed'
    ];

    public const PAYMENT_CODE = [
        'cards'   => 'Credit/Debit Card',
        'bakong' => 'kHQRs',
        'abapay'   => 'ABA Pay',
        'predepost' => 'Wallet',
        'pipay'   => 'Pipay',
        'cash' => 'Cash On Delivery',
        'aeon'   => 'AEON Pay',
        'aeonCredit' => 'AEON Credit Card',
        'payway' => 'Payway',
        'paypal' => 'Paypal',
    ];

    public const BILL_CYCLE = [
        1 => 'CoD',
        2 => 'T+1',
        11 => 'Standard',
        16 => 'Standard'
    ];

    public const ORDER_TYPE = [
        1 => 'Normal',
        2 => 'Group buy',
        4 => 'Prize',
        5 => 'Charity',
        6 => 'Get it free',
    ];


    public const USER_STATUS = [
        1 => 'Activated',
        2 => 'Disabled',
        4 => 'Banned',
        5 => 'Trashed',
        7 => 'Online',
        6 => 'Offline',

    ];

    public const DROP_OFF_STATUS = [
        0 => 'scan out',
        1 => 'drop-off',
    ];
}

