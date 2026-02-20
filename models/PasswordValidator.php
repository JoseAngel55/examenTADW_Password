<?php

class PasswordValidator
{
    public function validate(string $password, array $requirements = []): array
    {
        $req = array_merge([
            'minLength'        => 8,
            'maxLength'        => null,
            'requireUppercase' => false,
            'requireLowercase' => false,
            'requireNumbers'   => false,
            'requireSymbols'   => false,
        ], $requirements);

        $length = mb_strlen($password);
        $checks = $this->runChecks($password, $length, $req);
        $score  = $this->computeScore($password, $length);
        $passed = array_reduce($checks, fn($carry, $c) => $carry && $c['passed'], true);

        return [
            'valid'    => $passed,
            'score'    => $score,
            'strength' => $this->strengthLabel($score),
            'length'   => $length,
            'checks'   => $checks,
        ];
    }

    private function runChecks(string $pw, int $length, array $req): array
    {
        $checks = [];

        $minLen   = (int) $req['minLength'];
        $checks[] = [
            'rule'   => "Longitud mínima ({$minLen})",
            'passed' => $length >= $minLen,
            'detail' => "Longitud actual: {$length}",
        ];

        if ($req['maxLength'] !== null) {
            $maxLen   = (int) $req['maxLength'];
            $checks[] = [
                'rule'   => "Longitud máxima ({$maxLen})",
                'passed' => $length <= $maxLen,
                'detail' => "Longitud actual: {$length}",
            ];
        }

        if ($req['requireUppercase']) {
            $has      = (bool) preg_match('/[A-Z]/', $pw);
            $checks[] = ['rule' => 'Contiene mayúsculas', 'passed' => $has, 'detail' => $has ? 'OK' : 'Sin mayúsculas'];
        }
        if ($req['requireLowercase']) {
            $has      = (bool) preg_match('/[a-z]/', $pw);
            $checks[] = ['rule' => 'Contiene minúsculas', 'passed' => $has, 'detail' => $has ? 'OK' : 'Sin minúsculas'];
        }
        if ($req['requireNumbers']) {
            $has      = (bool) preg_match('/[0-9]/', $pw);
            $checks[] = ['rule' => 'Contiene números', 'passed' => $has, 'detail' => $has ? 'OK' : 'Sin dígitos'];
        }
        if ($req['requireSymbols']) {
            $has      = (bool) preg_match('/[^a-zA-Z0-9]/', $pw);
            $checks[] = ['rule' => 'Contiene símbolos', 'passed' => $has, 'detail' => $has ? 'OK' : 'Sin símbolos'];
        }

        return $checks;
    }

    private function computeScore(string $pw, int $length): int
    {
        $score = min(25, (int) ($length / PasswordGenerator::MAX_LENGTH * 100));
        if (preg_match('/[A-Z]/', $pw))        $score += 15;
        if (preg_match('/[a-z]/', $pw))        $score += 15;
        if (preg_match('/[0-9]/', $pw))        $score += 20;
        if (preg_match('/[^a-zA-Z0-9]/', $pw)) $score += 25;
        return min(100, $score);
    }

    private function strengthLabel(int $score): string
    {
        return match (true) {
            $score >= 80 => 'very_strong',
            $score >= 60 => 'strong',
            $score >= 40 => 'fair',
            default      => 'weak',
        };
    }
}