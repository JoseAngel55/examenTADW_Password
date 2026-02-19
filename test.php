<?php

require_once __DIR__ . '/models/PasswordGenerator.php';

$generator = new PasswordGenerator();


echo "=== Caso 1: Básica ===\n";
$pw = $generator->generate(12);
echo $pw . "\n\n";


echo "=== Caso 2: Mayúsculas + números ===\n";
$pw = $generator->generate(12, [
    'upper'  => true,
    'lower'  => false,
    'digits' => true,
    'symbols'=> false,
]);
echo $pw . "\n\n";


echo "=== Caso 3: Símbolos + excluir ambiguos ===\n";
$pw = $generator->generate(16, [
    'symbols'         => true,
    'avoid_ambiguous' => true,
]);
echo $pw . "\n\n";


echo "=== Caso 4: Múltiples (5) ===\n";
$passwords = $generator->generateMultiple(5, 16);
foreach ($passwords as $i => $pw) {
    echo ($i + 1) . ". $pw\n";
}
echo "\n";


echo "=== Caso 5: Error longitud inválida ===\n";
try {
    $generator->generate(1000);
} catch (InvalidArgumentException $e) {
    echo "Error capturado: " . $e->getMessage() . "\n\n";
}


echo "=== Caso 6: Error sin categorías ===\n";
try {
    $generator->generate(12, [
        'upper'  => false,
        'lower'  => false,
        'digits' => false,
        'symbols'=> false,
    ]);
} catch (InvalidArgumentException $e) {
    echo "Error capturado: " . $e->getMessage() . "\n\n";
}