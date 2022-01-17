<?php

namespace App\Validate;

use EasySwoole\Validate\Functions\AbstractValidateFunction;
use EasySwoole\Validate\Validate;

class CustomValidator extends AbstractValidateFunction
{
    /**
     * 返回当前校验规则的名字
     */
    public function name(): string
    {
        return 'mobile';
    }

    /**
     * 验证失败返回 false，或者用户可以抛出异常，验证成功返回 true
     * @param $itemData
     * @param $arg
     * @param $column
     * @param Validate $validate
     * @return bool
     */
    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        $regular = '/^((13[0-9])|(14[5,7,9])|(15[^4])|(18[0-9])|(17[0,1,3,5,6,7,8]))\\d{8}$/';
        if (!preg_match($regular, $itemData)) {
            return false;
        }

        return true;
    }
}