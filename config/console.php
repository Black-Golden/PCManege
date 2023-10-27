<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'app\common\cli\Server',
        'app\common\cli\TrcServer',
        'app\common\cli\Hello',
        'app\common\cli\Test',
        'app\common\cli\GoodImg',
        'app\common\cli\UpdateDataUser',
        'app\common\cli\CreateQuantTest',
        'app\common\cli\CreateQuantPro',
        'app\common\cli\QuantTest',
        'app\common\cli\QuantPrice',
        'app\common\cli\MenuSc',

        //初始化行情价以及开盘价
        'app\common\cli\setting\InitDayPrice',
        //初始化金额充值
        'app\common\cli\setting\InitMinuteRechargePrice',
        //奖励以及扣除佣金计算
        'app\common\cli\setting\InitSecondProfit',
    ],
];
