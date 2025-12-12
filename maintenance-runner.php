<?php

declare(strict_types=1);

// Opcional: asigna un token y accede usando ?token=TU_TOKEN para proteger el script.
const RUNNER_TOKEN = '';

if (RUNNER_TOKEN !== '' && (($_GET['token'] ?? '') !== RUNNER_TOKEN)) {
    http_response_code(403);
    echo 'Token inválido. Añade ?token=TU_TOKEN en la URL (ver RUNNER_TOKEN en el archivo).';
    exit;
}

set_time_limit(0);

$projectPath = __DIR__;
$procAvailable = function_exists('proc_open');

$phpCli = formatCommandPart(findBinary([
    getenv('PHP_CLI_PATH') ?: null,
    PHP_BINDIR . DIRECTORY_SEPARATOR . 'php',
    PHP_BINDIR . DIRECTORY_SEPARATOR . 'php.exe',
    PHP_BINARY,
    'php',
], 'php'));

$composerCli = formatCommandPart(findBinary([
    getenv('COMPOSER_BINARY') ?: null,
    PHP_BINDIR . DIRECTORY_SEPARATOR . 'composer',
    PHP_BINDIR . DIRECTORY_SEPARATOR . 'composer.phar',
    dirname(PHP_BINARY) . DIRECTORY_SEPARATOR . 'composer',
    '/usr/bin/composer',
    '/usr/local/bin/composer',
    'composer',
], 'composer'));

$artisanPath = formatCommandPart($projectPath . DIRECTORY_SEPARATOR . 'artisan');

$buildArtisan = static fn(string $arguments): string => trim($phpCli . ' ' . $artisanPath . ' ' . $arguments);
$buildComposer = static fn(string $arguments): string => trim($composerCli . ' ' . $arguments);

$commandPresets = [
    'migrate' => [
        'label' => 'Migrar base de datos',
        'description' => 'php artisan migrate --force',
        'commands' => [
            $buildArtisan('migrate --force'),
        ],
    ],
    'cache-clear' => [
        'label' => 'Limpiar cachés (config, route, view, cache)',
        'description' => 'config:clear, route:clear, view:clear, cache:clear',
        'commands' => [
            $buildArtisan('config:clear'),
            $buildArtisan('route:clear'),
            $buildArtisan('view:clear'),
            $buildArtisan('cache:clear'),
        ],
    ],
    'optimize-refresh' => [
        'label' => 'optimize:clear + optimize',
        'description' => 'php artisan optimize:clear && php artisan optimize',
        'commands' => [
            $buildArtisan('optimize:clear'),
            $buildArtisan('optimize'),
        ],
    ],
    'full-deploy' => [
        'label' => 'Migrar + limpiar cachés + optimize',
        'description' => 'Cadena completa recomendada post deploy',
        'commands' => [
            $buildArtisan('migrate --force'),
            $buildArtisan('config:clear'),
            $buildArtisan('route:clear'),
            $buildArtisan('view:clear'),
            $buildArtisan('cache:clear'),
            $buildArtisan('optimize:clear'),
            $buildArtisan('optimize'),
        ],
    ],
    'config-cache' => [
        'label' => 'Regenerar config cache',
        'description' => 'php artisan config:cache',
        'commands' => [
            $buildArtisan('config:cache'),
        ],
    ],
];

$commandPresets['composer-install'] = [
    'label' => 'composer install --no-dev -o',
    'description' => 'Instala dependencias en producción',
    'commands' => [
        $buildComposer('install --no-dev --optimize-autoloader'),
    ],
];

$errorMessage = null;
$outputLog = [];

if ($procAvailable && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected = $_POST['command'] ?? '';
    if (! isset($commandPresets[$selected])) {
        $errorMessage = 'Comando no reconocido.';
    } else {
        foreach ($commandPresets[$selected]['commands'] as $command) {
            try {
                $outputLog[] = array_merge(
                    ['command' => $command],
                    runCommand($command, $projectPath)
                );
            } catch (Throwable $e) {
                $outputLog[] = [
                    'command' => $command,
                    'exitCode' => -1,
                    'stdout' => '',
                    'stderr' => $e->getMessage(),
                ];
                break;
            }
        }
    }
}

function runCommand(string $command, string $cwd): array
{
    $descriptorSpec = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = @proc_open($command, $descriptorSpec, $pipes, $cwd);

    if (! is_resource($process)) {
        throw new RuntimeException('No se pudo iniciar el proceso. ¿Está habilitado proc_open?');
    }

    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);

    $exitCode = proc_close($process);

    return [
        'exitCode' => $exitCode,
        'stdout' => $stdout ?: '',
        'stderr' => $stderr ?: '',
    ];
}

function findBinary(array $candidates, string $default): string
{
    foreach ($candidates as $candidate) {
        if (! $candidate) {
            continue;
        }

        if ($candidate === 'php' || $candidate === 'composer') {
            return $candidate;
        }

        if (is_file($candidate) && is_executable($candidate)) {
            return $candidate;
        }
    }

    return $default;
}

function formatCommandPart(string $value): string
{
    $value = trim($value);
    if ($value === '') {
        return '';
    }

    return str_contains($value, ' ') ? '"' . $value . '"' : $value;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Maintenance Runner</title>
    <style>
        :root {
            color-scheme: light dark;
            font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        body {
            margin: 0;
            padding: 2rem;
            background: #f5f6fb;
            color: #0f172a;
        }

        .container {
            max-width: 960px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 18px;
            padding: 2rem;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.1);
        }

        h1 {
            margin-top: 0;
            font-size: 1.8rem;
            letter-spacing: -0.02em;
        }

        form {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: flex-end;
            margin-bottom: 1.5rem;
        }

        select,
        button {
            padding: 0.75rem 1rem;
            border-radius: 10px;
            border: 1px solid #cbd5f5;
            font-size: 1rem;
        }

        button {
            background: #2563eb;
            border: none;
            color: #fff;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            box-shadow: 0 10px 20px -10px rgba(37, 99, 235, 0.8);
        }

        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 15px 25px -10px rgba(37, 99, 235, 0.9);
        }

        .card {
            background: #f8fafc;
            border-radius: 14px;
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge.success {
            color: #166534;
            background: #dcfce7;
        }

        .badge.error {
            color: #b91c1c;
            background: #fee2e2;
        }

        pre {
            background: #0f172a;
            color: #e2e8f0;
            padding: 1rem;
            border-radius: 12px;
            overflow-x: auto;
            font-size: 0.9rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            border: 1px solid #fecdd3;
            background: #fff1f2;
            color: #9f1239;
        }

        footer {
            margin-top: 2rem;
            font-size: 0.9rem;
            color: #475569;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Laravel Maintenance Runner</h1>
        <p style="margin-top: 0.25rem; color:#475569;">Ruta del proyecto: <strong><?= htmlspecialchars($projectPath, ENT_QUOTES, 'UTF-8'); ?></strong></p>
        <p style="margin-top: 0.25rem; color:#475569;">Usando PHP CLI: <code><?= htmlspecialchars($phpCli, ENT_QUOTES, 'UTF-8'); ?></code> | Composer: <code><?= htmlspecialchars($composerCli, ENT_QUOTES, 'UTF-8'); ?></code></p>

        <?php if (! $procAvailable): ?>
            <div class="alert">
                El hosting bloquea <code>proc_open</code>. Consulta con el proveedor para habilitarlo o ejecuta los comandos
                manualmente por SSH.
            </div>
        <?php else: ?>
            <?php if ($errorMessage): ?>
                <div class="alert"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form method="POST">
                <label style="flex:1; min-width:240px;">
                    <span style="display:block; margin-bottom:0.35rem; color:#475569; font-weight:600;">Selecciona un comando:</span>
                    <select name="command" required style="width:100%;">
                        <?php foreach ($commandPresets as $key => $preset): ?>
                            <option value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>">
                                <?= htmlspecialchars($preset['label'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <button type="submit">Ejecutar</button>
            </form>
        <?php endif; ?>

        <?php if ($outputLog): ?>
            <?php foreach ($outputLog as $log): ?>
                <div class="card">
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
                        <div style="font-weight:600;">Comando ejecutado:</div>
                        <div class="badge <?= ($log['exitCode'] === 0) ? 'success' : 'error'; ?>">
                            Exit code: <?= (int) $log['exitCode']; ?>
                        </div>
                    </div>
                    <p style="margin:0.75rem 0 0.5rem; color:#0f172a; font-family:monospace;">
                        <?= htmlspecialchars($log['command'], ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                    <?php if ($log['stdout'] !== ''): ?>
                        <h4>STDOUT</h4>
                        <pre><?= htmlspecialchars($log['stdout'], ENT_QUOTES, 'UTF-8'); ?></pre>
                    <?php endif; ?>
                    <?php if ($log['stderr'] !== ''): ?>
                        <h4>STDERR</h4>
                        <pre><?= htmlspecialchars($log['stderr'], ENT_QUOTES, 'UTF-8'); ?></pre>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <footer>
            <p>
                • Sube este archivo a la raíz del proyecto (junto a <code>artisan</code>), ejecútalo y elimínalo inmediatamente
                después.<br>
                • Personaliza <code>RUNNER_TOKEN</code> y, si es necesario, define <code>PHP_CLI_PATH</code> o <code>COMPOSER_BINARY</code>
                para forzar rutas específicas.
            </p>
        </footer>
    </div>
</body>

</html>