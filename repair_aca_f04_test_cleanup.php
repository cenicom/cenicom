<?php

declare(strict_types=1);

/**
 * ACA-YEAR-02 / ACA-F04 regression test repair.
 * Modifies only three test classes, never application code or the database.
 * Run from the CENICOM project root with PHP CLI.
 */

$root = getcwd();
$targets = [
    'tests/Feature/Modules/Academic/AcademicYearCancellationTest.php' => 'AcademicYearCancellationTest',
    'tests/Feature/Modules/Academic/AcademicYearCreationOverlapTest.php' => 'AcademicYearCreationOverlapTest',
    'tests/Feature/Modules/Academic/AcademicYearLifecycleTest.php' => 'AcademicYearLifecycleTest',
];

function failRepair(string $message): never
{
    fwrite(STDERR, "ERROR: {$message}\n");
    exit(1);
}

/** Find the function body ending via PHP tokens, avoiding braces inside strings/comments. */
function replaceTearDown(string $source, string $className, string $relativePath): string
{
    $tokens = token_get_all($source);
    $offsets = [];
    $offset = 0;
    foreach ($tokens as $index => $token) {
        $text = is_array($token) ? $token[1] : $token;
        $offsets[$index] = $offset;
        $offset += strlen($text);
    }

    $inClass = false;
    $classDepth = 0;
    $classBodyOpened = false;
    $functionIndex = null;
    $bodyOpenIndex = null;
    $bodyCloseIndex = null;

    foreach ($tokens as $i => $token) {
        $text = is_array($token) ? $token[1] : $token;

        if (!$classBodyOpened && is_array($token) && $token[0] === T_CLASS) {
            $name = null;
            for ($j = $i + 1; $j < count($tokens); $j++) {
                if (is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                    $name = $tokens[$j][1];
                    break;
                }
                if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                    break;
                }
            }
            if ($name === $className) {
                $inClass = true;
                // Find this class's opening brace.
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if ($tokens[$j] === '{') {
                        $classBodyOpened = true;
                        $classDepth = 1;
                        $i = $j;
                        break;
                    }
                }
            }
        }

        if (!$inClass || !$classBodyOpened) {
            continue;
        }

        if ($functionIndex === null && is_array($token) && $token[0] === T_FUNCTION) {
            $nameToken = null;
            for ($j = $i + 1; $j < count($tokens); $j++) {
                $candidate = $tokens[$j];
                if (is_array($candidate) && in_array($candidate[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                    continue;
                }
                if ($candidate === '&') {
                    continue;
                }
                if (is_array($candidate) && $candidate[0] === T_STRING) {
                    $nameToken = $candidate[1];
                }
                break;
            }
            if ($nameToken === 'tearDown') {
                $functionIndex = $i;
                // Find the opening brace after the signature.
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if ($tokens[$j] === '{') {
                        $bodyOpenIndex = $j;
                        break;
                    }
                    if ($tokens[$j] === ';') {
                        break;
                    }
                }
                if ($bodyOpenIndex === null) {
                    failRepair("No se pudo localizar el cuerpo de tearDown() en {$relativePath}.");
                }
                $depth = 0;
                for ($j = $bodyOpenIndex; $j < count($tokens); $j++) {
                    if ($tokens[$j] === '{') {
                        $depth++;
                    } elseif ($tokens[$j] === '}') {
                        $depth--;
                        if ($depth === 0) {
                            $bodyCloseIndex = $j;
                            break;
                        }
                    }
                }
                break;
            }
        }

        if ($text === '{') {
            // Only used if the class/function lookup needs to follow token stream further.
            $classDepth++;
        } elseif ($text === '}') {
            $classDepth--;
            if ($classDepth <= 0) {
                break;
            }
        }
    }

    if ($functionIndex === null || $bodyOpenIndex === null || $bodyCloseIndex === null) {
        failRepair("No se encontró una función tearDown() reconocible en {$relativePath}; no se modificó ese archivo.");
    }

    $start = $offsets[$functionIndex];
    $end = $offsets[$bodyCloseIndex] + strlen($tokens[$bodyCloseIndex]);
    $replacement = "function tearDown(): void\n    {\n        parent::tearDown();\n    }";

    return substr($source, 0, $start) . $replacement . substr($source, $end);
}

if (!is_file($root . '/artisan') || !is_dir($root . '/tests/Feature/Modules/Academic')) {
    failRepair('Ejecute este archivo desde la raíz del proyecto CENICOM. No se modificó ningún archivo.');
}

foreach ($targets as $relative => $className) {
    $path = $root . '/' . $relative;
    if (!is_file($path)) {
        failRepair("No existe {$relative}. No se completó la reparación.");
    }
    $source = file_get_contents($path);
    if ($source === false || !str_contains($source, 'class ' . $className)) {
        failRepair("El archivo {$relative} no contiene la clase esperada {$className}. No se modificó.");
    }
}

$originals = [];
$changes = [];
foreach ($targets as $relative => $className) {
    $path = $root . '/' . $relative;
    $source = file_get_contents($path);
    $originals[$path] = $source;

    if (!str_contains($source, 'use Illuminate\\Foundation\\Testing\\DatabaseTransactions;')) {
        $updated = preg_replace(
            '/(namespace\s+[A-Za-z0-9_\\\\]+;\s*\r?\n)/',
            "$1use Illuminate\\\\Foundation\\\\Testing\\\\DatabaseTransactions;\n",
            $source,
            1,
            $count,
        );
        if ($updated === null || $count !== 1) {
            failRepair("No se pudo insertar el import de DatabaseTransactions en {$relative}. No se completó la reparación.");
        }
        $source = $updated;
    }

    // Add trait to the named test class only, not to its parent or another class.
    $pattern = '/((?:final\s+)?class\s+' . preg_quote($className, '/') . '\b[^\{]*\{)/';
    if (!preg_match($pattern, $source, $match, PREG_OFFSET_CAPTURE)) {
        failRepair("No se encontró la declaración de {$className} en {$relative}. No se completó la reparación.");
    }
    $classDeclaration = $match[1][0];
    if (!preg_match('/\buse\s+DatabaseTransactions\s*;/', substr($source, $match[1][1], 500))) {
        $position = $match[1][1] + strlen($classDeclaration);
        $source = substr($source, 0, $position)
            . "\n    use DatabaseTransactions;\n"
            . substr($source, $position);
    }

    $source = replaceTearDown($source, $className, $relative);
    $changes[$path] = $source;
}

// All changes are validated in memory before any file is written.
foreach ($changes as $path => $source) {
    $tmp = $path . '.aca-f04-tmp';
    if (file_put_contents($tmp, $source) === false) {
        foreach (array_keys($changes) as $cleanupPath) {
            @unlink($cleanupPath . '.aca-f04-tmp');
        }
        failRepair("No se pudo preparar la escritura temporal de {$path}. No se sobrescribió ningún archivo original.");
    }
}

foreach ($changes as $path => $source) {
    $tmp = $path . '.aca-f04-tmp';
    if (!rename($tmp, $path)) {
        foreach ($originals as $originalPath => $originalSource) {
            file_put_contents($originalPath, $originalSource);
        }
        foreach (array_keys($changes) as $cleanupPath) {
            @unlink($cleanupPath . '.aca-f04-tmp');
        }
        failRepair('Falló la sustitución atómica de un archivo; se restauraron los originales cuando fue posible.');
    }
}

foreach (array_keys($changes) as $path) {
    echo 'ACTUALIZADO: ' . str_replace($root . DIRECTORY_SEPARATOR, '', $path) . "\n";
}
echo "Correcto: los tres tests usan transacciones y dejan el rollback de fixtures al framework.\n";
echo "Ninguna migración, trigger ni archivo de aplicación fue modificado.\n";
