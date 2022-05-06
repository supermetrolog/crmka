<?php

namespace app\components;

use yii\db\Expression;
use yii\helpers\ArrayHelper;

class ExpressionBuilder
{
    private $expression;
    private $conditions = [];
    private $endText = "";
    private $tablePrefix = "";
    public function addCondition($condition, $true, $false)
    {
        $this->conditions[] = [
            'value' => $condition,
            'true' => $true,
            'false' => $false,
        ];
        return $this;
    }

    public function prepareToEnd($text)
    {
        $this->endText .= " $text";
        return $this;
    }
    /**
     * Add condition for IF in slq
     *  -> IF (true, 1, 0)
     */
    //fuck
    public function getConditionExpression()
    {
        if (!$count = count($this->conditions)) {
            return false;
        }
        $expressionCount = 0;
        $expression = "(";
        foreach ($this->conditions as $key => $condition) {
            $sign = trim($condition['value'][0]);
            $column = trim($condition['value'][1]);
            $value = $condition['value'][2];
            $prefix = $this->tablePrefix;
            $withPrefix = true;
            if (count($condition['value']) > 3) {
                $withPrefix = false;
            }
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $value = trim($value);
            if (!$sign || !$column || !$value) {
                continue;
            }
            if ($sign == 'IN' || $sign == 'in') {
                $value = "($value)";
            }
            $conditionValue = "$prefix.$column $sign $value";
            if (!$withPrefix) {
                $conditionValue = "$column $sign $value";
            }
            $expression .= "
                IF ($conditionValue, {$condition['true']}, {$condition['false']}) 
            ";
            $expression .= "+";
            $expressionCount++;
        }
        $expression = trim($expression, "+");
        $expression .= ")";
        $expression .= $this->endText;
        $this->expression = $expression;
        if (!$expressionCount) {
            return false;
        }
        return $this->getExpression();
    }

    public function getExpression()
    {
        return new Expression($this->expression);
    }

    public function addTablePrefix($prefix)
    {
        $this->tablePrefix = $prefix;
    }
}
