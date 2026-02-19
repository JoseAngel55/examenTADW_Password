<?php


class PasswordGenerator
{
    public const MIN_LENGTH = 4;
    public const MAX_LENGTH = 128;

    private const CHARS_UPPER     = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const CHARS_LOWER     = 'abcdefghijklmnopqrstuvwxyz';
    private const CHARS_DIGITS    = '0123456789';
    private const CHARS_SYMBOLS   = '!@#$%^&*()-_=+[]{}|;:,.<>?';
    private const CHARS_AMBIGUOUS = 'Il1O0o';

    public function generate(int $length, array $opts = []): string
    {
        $opts = $this->mergeDefaults($opts);
        $this->validateLength($length);

        $sets  = $this->buildSets($opts);
        $pool  = implode('', array_values($sets));
        $chars = [];

        if ($opts['require_each']) {
            foreach ($sets as $setChars) {
                $chars[] = $this->randomChar($setChars);
            }
        }

        $remaining = $length - count($chars);
        for ($i = 0; $i < $remaining; $i++) {
            $chars[] = $this->randomChar($pool);
        }

        return $this->shuffleSecure(implode('', $chars));
    }

    public function generateMultiple(int $count, int $length, array $opts = []): array
    {
        if ($count < 1 || $count > 100) {
            throw new InvalidArgumentException('El campo "count" debe ser un entero entre 1 y 100.');
        }

        $passwords = [];
        for ($i = 0; $i < $count; $i++) {
            $passwords[] = $this->generate($length, $opts);
        }
        return $passwords;
    }

    private function mergeDefaults(array $opts): array
    {
        return array_merge([
            'upper'           => true,
            'lower'           => true,
            'digits'          => true,
            'symbols'         => false,
            'avoid_ambiguous' => false,
            'exclude'         => '',
            'require_each'    => true,
        ], $opts);
    }

    private function validateLength(int $length): void
    {
        if ($length < self::MIN_LENGTH || $length > self::MAX_LENGTH) {
            throw new InvalidArgumentException(sprintf(
                'La longitud debe estar entre %d y %d caracteres.',
                self::MIN_LENGTH,
                self::MAX_LENGTH
            ));
        }
    }

    private function buildSets(array $opts): array
    {
        $raw = [];
        if ($opts['upper'])   $raw['upper']   = self::CHARS_UPPER;
        if ($opts['lower'])   $raw['lower']   = self::CHARS_LOWER;
        if ($opts['digits'])  $raw['digits']  = self::CHARS_DIGITS;
        if ($opts['symbols']) $raw['symbols'] = self::CHARS_SYMBOLS;

        if (empty($raw)) {
            throw new InvalidArgumentException('Debe activarse al menos una categoría (upper/lower/digits/symbols).');
        }

        $excludeStr = $opts['exclude'];
        if ($opts['avoid_ambiguous']) {
            $excludeStr .= self::CHARS_AMBIGUOUS;
        }
        $excludeMap = array_flip(array_unique($this->splitChars($excludeStr)));

        $sets = [];
        foreach ($raw as $name => $chars) {
            $filtered = array_values(array_filter(
                $this->splitChars($chars),
                fn($c) => !isset($excludeMap[$c])
            ));
            if (empty($filtered)) {
                throw new InvalidArgumentException(
                    "Después de aplicar exclusiones, la categoría '{$name}' no tiene caracteres disponibles."
                );
            }
            $sets[$name] = implode('', $filtered);
        }

        return $sets;
    }

    private function randomChar(string $pool): string
    {
        return $pool[random_int(0, strlen($pool) - 1)];
    }

    private function shuffleSecure(string $str): string
    {
        $arr = $this->splitChars($str);
        $n   = count($arr);
        for ($i = $n - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$arr[$i], $arr[$j]] = [$arr[$j], $arr[$i]];
        }
        return implode('', $arr);
    }

    private function splitChars(string $str): array
    {
        return preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }
}