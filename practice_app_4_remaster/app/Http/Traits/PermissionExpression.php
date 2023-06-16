<?php

namespace App\Http\Traits;

use App\Models\User;

trait PermissionExpression
{
    private function isNotOperator($char)
    {
        return !str_contains('|&()', $char);
    }

    private function hasRole(User &$user, $roleName): bool
    {
        return $user->hasRoleNames([$roleName]);
    }

    private function hasPermission(User &$user, $permissionName): bool
    {
        return $user->hasPermissionNames([$permissionName]);
    }

    private function performOp($item1, $op, $item2)
    {
        switch ($op) {
            case '|':
                return $item1 or $item2;
                break;
            case '&':
                return $item1 and $item2;
                break;
            default:
                abort(500);
        }
    }

    
    public function calculate(User &$user, string $expression)
    {
        $stack = array();
        $top = -1;
        $expressionLength = strlen($expression);
        for ($i = 0; $i < $expressionLength;) {
            $char = $expression[$i];
            if ($char === ')') {
                while ($top >= 0) {
                    $item1 = $stack[$top--];
                    if ($item1 === '(') { // () case
                        break;
                    }
                    $op = $stack[$top--];
                    if ($op === '(') {
                        $stack[++$top] = $item1;
                        break;
                    }
                    if ($top < 0) {
                        return null;
                    }
                    if ($top < 0) {
                        return null;
                    }
                    $item2 = $stack[$top--];
                    if ($item2 === '(') {
                        return null;
                    }
                    $stack[++$top] = $this->performOp($item1, $op, $item2);
                }
                $i += 1;
            } elseif ($char === 'r' || $char === 'p') {
                $item = "";
                $i += 2;
                if ($i >= $expressionLength) {
                    abort(500);
                }
                while ($i < $expressionLength && $this->isNotOperator($expression[$i])) {
                    $item .= $expression[$i];
                    $i += 1;
                }
                $char === 'r' ? $stack[++$top] = $this->hasRole($user, $item) :
                    $stack[++$top] = $this->hasPermission($user, $item);
            } else {
                $stack[++$top] = $expression[$i];
                $i += 1;
            }
        }
        while ($top > 0) {
            $item1 = $stack[$top--];
            if ($item1 === '(') { // () case
                break;
            }
            $op = $stack[$top--];
            if ($op === '(') {
                $stack[++$top] = $item1;
                break;
            }
            if ($top < 0) {
                return null;
            }
            if ($top < 0) {
                return null;
            }
            $item2 = $stack[$top--];
            if ($item2 === '(') {
                return null;
            }
            $stack[++$top] = $this->performOp($item1, $op, $item2);
        }

        if ($top !== 0) {
            return null;
        }

        return $stack[$top];
    }
}
