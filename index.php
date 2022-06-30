<?php

function eval_expr(string $expr)
{
    $result = array();
    $output = [];
    $operators = [];
    $precedence = [
        "*" => 2,
        "/" => 2,
        "%" => 2,
        "+" => 1,
        "-" => 1,
    ];
    $operations = [
        "+" => function ($a, $b) {
            return $a + $b;
        },
        "-" => function ($a, $b) {
            return $a - $b;
        },
        "/" => function ($a, $b) {
            return $a / $b;
        },
        "*" => function ($a, $b) {
            return $a * $b;
        },
        "%" => function ($a, $b) {
            return $a % $b;
        },
    ];

    $matches = [];
    preg_match_all("/(?<!\d)[-]?\d*\.?\d+|[\\%\\+\\-\\/\\*\\(\\)]/", $expr, $matches);
   
    foreach ($matches[0] as $key => $match) {
        if (is_numeric($match)) {
            $output[] = floatval($match);
        } elseif (empty($operators) || (isset($precedence[$match]) && $precedence[$match] > $precedence[end($operators)]) || $match === "(") {
            $operators[] = $match;
        } elseif ($match === ")") {
            while ($operators[array_key_last($operators)] !== '(') {
                $op = array_pop($operators);
                array_push($output, $op);
            }
            array_pop($operators);
        } else {
            while (!empty($operators) && $precedence[$match] <= $precedence[$operators[array_key_last($operators)]]) {
                if ($operators[array_key_last($operators)] == "(" || $operators[array_key_last($operators)] == ")") {
                    break;
                } else {
                    $op = array_pop($operators);
                    array_push($output, $op);
                }
            }
            array_push($operators, $match);
        }

    }

    while (!empty($operators)) {
        $op = array_pop($operators);
        array_push($output, $op);
    }

    var_dump($output);

    while (count($output) > 1) {
        $operator_index = 0;
        while (!isset($operations[$output[$operator_index]]))        
            $operator_index++;
        $operator = $output[$operator_index];
        $a = $output[$operator_index - 2];
        $b = $output[$operator_index - 1];
        $result = $operations[$operator]($a, $b);
        array_splice($output, $operator_index - 2, 3, [$result]);
    }
    return $output[0];
}

// echo eval_expr("10+3-(5+5)");