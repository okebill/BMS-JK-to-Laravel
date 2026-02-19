<?php
/**
 * Okenet BMS Monitoring — Web Installer
 * Seperti WordPress installer — buka di browser, isi data, selesai!
 *
 * HAPUS file ini setelah instalasi berhasil untuk keamanan.
 */

define('INSTALLER_VERSION', '1.0.0');
define('BASE_PATH', dirname(__DIR__));
define('ENV_FILE', BASE_PATH . '/.env');
define('ENV_EXAMPLE', BASE_PATH . '/.env.example');
define('LOCK_FILE', BASE_PATH . '/storage/installed.lock');

session_start();

// ─── Helpers ────────────────────────────────────────────────────────────────

function checkInstalled(): bool {
    return file_exists(LOCK_FILE);
}

function getStep(): int {
    return isset($_GET['step']) ? (int)$_GET['step'] : 1;
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function updateEnv(array $values): bool {
    $env = file_exists(ENV_FILE) ? file_get_contents(ENV_FILE) : file_get_contents(ENV_EXAMPLE);
    foreach ($values as $key => $value) {
        $value = str_replace('"', '\\"', $value);
        if (preg_match("/^{$key}=.*$/m", $env)) {
            $env = preg_replace("/^{$key}=.*$/m", "{$key}=\"{$value}\"", $env);
        } else {
            $env .= "\n{$key}=\"{$value}\"";
        }
    }
    return file_put_contents(ENV_FILE, $env) !== false;
}

function generateAppKey(): string {
    return 'base64:' . base64_encode(random_bytes(32));
}

function testDbConnection(string $host, string $port, string $db, string $user, string $pass): array {
    try {
        $pdo = new PDO(
            "mysql:host={$host};port={$port};charset=utf8mb4",
            $user, $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]
        );
        // Cek apakah database ada
        $stmt = $pdo->query("SHOW DATABASES LIKE '{$db}'");
        $exists = $stmt->rowCount() > 0;
        return ['ok' => true, 'pdo' => $pdo, 'db_exists' => $exists];
    } catch (PDOException $e) {
        return ['ok' => false, 'error' => $e->getMessage()];
    }
}

function importSchema(PDO $pdo, string $dbName): array {
    $errors = [];
    try {
        // Buat database jika belum ada
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$dbName}`");

        // Baca SQL schema
        $sqlFile = BASE_PATH . '/database/schema.sql';
        if (!file_exists($sqlFile)) {
            return ['ok' => false, 'error' => 'File database/schema.sql tidak ditemukan. Pastikan sudah di-extract dengan benar.'];
        }

        $sql = file_get_contents($sqlFile);
        // Hapus komentar dan baris kosong
        $sql = preg_replace('/^--.*$/m', '', $sql);
        $sql = preg_replace('/^\/\*.*?\*\/;/ms', '', $sql);

        // Eksekusi per statement
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        foreach ($statements as $stmt) {
            if (!empty($stmt)) {
                try {
                    $pdo->exec($stmt);
                } catch (PDOException $e) {
                    // Skip duplikat tabel
                    if ($e->getCode() != '42S01') {
                        $errors[] = $e->getMessage();
                    }
                }
            }
        }
        return ['ok' => true, 'errors' => $errors];
    } catch (PDOException $e) {
        return ['ok' => false, 'error' => $e->getMessage()];
    }
}

function createAdminUser(PDO $pdo, string $dbName, string $name, string $email, string $password): array {
    try {
        $pdo->exec("USE `{$dbName}`");
        // Password hashed Bcrypt (sama seperti Laravel bcrypt)
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $now = date('Y-m-d H:i:s');

        // Cek apakah user sudah ada
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            // Update password
            $upd = $pdo->prepare("UPDATE users SET name=?, password=?, updated_at=? WHERE email=?");
            $upd->execute([$name, $hash, $now, $email]);
        } else {
            $ins = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
            $ins->execute([$name, $email, $hash, $now, $now]);
        }
        return ['ok' => true];
    } catch (PDOException $e) {
        return ['ok' => false, 'error' => $e->getMessage()];
    }
}

function setPermissions(): array {
    $dirs = [
        BASE_PATH . '/storage',
        BASE_PATH . '/storage/app',
        BASE_PATH . '/storage/app/public',
        BASE_PATH . '/storage/framework',
        BASE_PATH . '/storage/framework/cache',
        BASE_PATH . '/storage/framework/sessions',
        BASE_PATH . '/storage/framework/views',
        BASE_PATH . '/storage/logs',
        BASE_PATH . '/bootstrap/cache',
    ];
    $errors = [];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        if (!@chmod($dir, 0755)) {
            $errors[] = $dir;
        }
    }
    return ['ok' => empty($errors), 'errors' => $errors];
}

function clearBootstrapCache(): void {
    $cacheFiles = glob(BASE_PATH . '/bootstrap/cache/*.php');
    if ($cacheFiles) {
        foreach ($cacheFiles as $f) @unlink($f);
    }
}

function checkRequirements(): array {
    $req = [];

    $req[] = ['name' => 'PHP >= 8.2', 'ok' => version_compare(PHP_VERSION, '8.2.0', '>='), 'value' => PHP_VERSION];
    $req[] = ['name' => 'Ekstensi PDO MySQL', 'ok' => extension_loaded('pdo_mysql'), 'value' => extension_loaded('pdo_mysql') ? 'Aktif' : 'Tidak ada'];
    $req[] = ['name' => 'Ekstensi OpenSSL', 'ok' => extension_loaded('openssl'), 'value' => extension_loaded('openssl') ? 'Aktif' : 'Tidak ada'];
    $req[] = ['name' => 'Ekstensi Mbstring', 'ok' => extension_loaded('mbstring'), 'value' => extension_loaded('mbstring') ? 'Aktif' : 'Tidak ada'];
    $req[] = ['name' => 'Ekstensi Tokenizer', 'ok' => extension_loaded('tokenizer'), 'value' => extension_loaded('tokenizer') ? 'Aktif' : 'Tidak ada'];
    $req[] = ['name' => 'Ekstensi JSON', 'ok' => extension_loaded('json'), 'value' => extension_loaded('json') ? 'Aktif' : 'Tidak ada'];
    $req[] = ['name' => 'Ekstensi cURL', 'ok' => extension_loaded('curl'), 'value' => extension_loaded('curl') ? 'Aktif' : 'Tidak ada'];
    $req[] = ['name' => 'storage/ dapat ditulis', 'ok' => is_writable(BASE_PATH . '/storage'), 'value' => is_writable(BASE_PATH . '/storage') ? 'Writable' : 'Read-only ❌'];
    $req[] = ['name' => 'bootstrap/cache/ dapat ditulis', 'ok' => is_writable(BASE_PATH . '/bootstrap/cache'), 'value' => is_writable(BASE_PATH . '/bootstrap/cache') ? 'Writable' : 'Read-only ❌'];
    $req[] = ['name' => '.env.example ada', 'ok' => file_exists(ENV_EXAMPLE), 'value' => file_exists(ENV_EXAMPLE) ? 'Ada' : 'Tidak ada ❌'];
    $req[] = ['name' => 'database/schema.sql ada', 'ok' => file_exists(BASE_PATH . '/database/schema.sql'), 'value' => file_exists(BASE_PATH . '/database/schema.sql') ? 'Ada' : 'Tidak ada ❌'];

    return $req;
}

// ─── Process POST ────────────────────────────────────────────────────────────

$error = '';
$success = '';
$step = getStep();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'check') {
        redirect('install.php?step=2');
    }

    if ($action === 'db_config') {
        $dbHost = trim($_POST['db_host'] ?? '127.0.0.1');
        $dbPort = trim($_POST['db_port'] ?? '3306');
        $dbName = trim($_POST['db_name'] ?? '');
        $dbUser = trim($_POST['db_user'] ?? '');
        $dbPass = $_POST['db_pass'] ?? '';
        $appUrl = rtrim(trim($_POST['app_url'] ?? ''), '/');

        if (empty($dbName) || empty($dbUser)) {
            $error = 'Nama database dan username tidak boleh kosong.';
        } else {
            $test = testDbConnection($dbHost, $dbPort, $dbName, $dbUser, $dbPass);
            if (!$test['ok']) {
                $error = 'Koneksi database gagal: ' . $test['error'];
            } else {
                // Simpan ke session
                $_SESSION['db_host'] = $dbHost;
                $_SESSION['db_port'] = $dbPort;
                $_SESSION['db_name'] = $dbName;
                $_SESSION['db_user'] = $dbUser;
                $_SESSION['db_pass'] = $dbPass;
                $_SESSION['app_url'] = $appUrl;
                redirect('install.php?step=3');
            }
        }
    }

    if ($action === 'admin_setup') {
        $adminName  = trim($_POST['admin_name'] ?? 'Admin');
        $adminEmail = trim($_POST['admin_email'] ?? '');
        $adminPass  = $_POST['admin_pass'] ?? '';
        $adminPass2 = $_POST['admin_pass2'] ?? '';

        if (empty($adminEmail) || empty($adminPass)) {
            $error = 'Email dan password admin tidak boleh kosong.';
        } elseif ($adminPass !== $adminPass2) {
            $error = 'Konfirmasi password tidak cocok.';
        } elseif (strlen($adminPass) < 6) {
            $error = 'Password minimal 6 karakter.';
        } else {
            $_SESSION['admin_name']  = $adminName;
            $_SESSION['admin_email'] = $adminEmail;
            $_SESSION['admin_pass']  = $adminPass;
            redirect('install.php?step=4');
        }
    }

    if ($action === 'install') {
        $dbHost = $_SESSION['db_host'] ?? '';
        $dbPort = $_SESSION['db_port'] ?? '3306';
        $dbName = $_SESSION['db_name'] ?? '';
        $dbUser = $_SESSION['db_user'] ?? '';
        $dbPass = $_SESSION['db_pass'] ?? '';
        $appUrl = $_SESSION['app_url'] ?? '';
        $adminName  = $_SESSION['admin_name'] ?? 'Admin';
        $adminEmail = $_SESSION['admin_email'] ?? '';
        $adminPass  = $_SESSION['admin_pass'] ?? '';

        $log = [];

        // 1. Buat .env
        $appKey = generateAppKey();
        $envOk = updateEnv([
            'APP_NAME'    => 'Okenet BMS Monitoring',
            'APP_ENV'     => 'production',
            'APP_KEY'     => $appKey,
            'APP_DEBUG'   => 'false',
            'APP_URL'     => $appUrl ?: ('http://' . $_SERVER['HTTP_HOST']),
            'DB_CONNECTION' => 'mysql',
            'DB_HOST'     => $dbHost,
            'DB_PORT'     => $dbPort,
            'DB_DATABASE' => $dbName,
            'DB_USERNAME' => $dbUser,
            'DB_PASSWORD' => $dbPass,
        ]);
        $log[] = ['label' => 'Membuat file .env', 'ok' => $envOk];

        // 2. Connect
        $conn = testDbConnection($dbHost, $dbPort, $dbName, $dbUser, $dbPass);
        $log[] = ['label' => 'Koneksi ke database', 'ok' => $conn['ok'], 'msg' => $conn['error'] ?? ''];

        if ($conn['ok']) {
            // 3. Import schema
            $import = importSchema($conn['pdo'], $dbName);
            $log[] = ['label' => 'Import struktur database', 'ok' => $import['ok'], 'msg' => $import['error'] ?? implode(', ', $import['errors'] ?? [])];

            // 4. Buat admin
            if ($import['ok']) {
                $admin = createAdminUser($conn['pdo'], $dbName, $adminName, $adminEmail, $adminPass);
                $log[] = ['label' => "Membuat user admin ({$adminEmail})", 'ok' => $admin['ok'], 'msg' => $admin['error'] ?? ''];
            }
        }

        // 5. Set permissions
        $perms = setPermissions();
        $log[] = ['label' => 'Mengatur permission folder', 'ok' => $perms['ok'], 'msg' => implode(', ', $perms['errors'] ?? [])];

        // 6. Clear bootstrap cache
        clearBootstrapCache();
        $log[] = ['label' => 'Clear bootstrap cache', 'ok' => true];

        // 7. Buat lock file
        $allOk = !in_array(false, array_column($log, 'ok'));
        if ($allOk) {
            file_put_contents(LOCK_FILE, date('Y-m-d H:i:s') . "\nInstalled by: " . $adminEmail);
            $log[] = ['label' => 'Instalasi selesai', 'ok' => true];
        }

        $_SESSION['install_log'] = $log;
        $_SESSION['install_ok']  = $allOk;
        $_SESSION['install_url'] = $appUrl ?: ('http://' . $_SERVER['HTTP_HOST']);
        redirect('install.php?step=5');
    }
}

// ─── Check if already installed ─────────────────────────────────────────────
if (checkInstalled() && $step !== 5) {
    $step = 99; // Show "already installed" page
}

// ─── HTML Output ─────────────────────────────────────────────────────────────
$requirements = ($step === 1 || $step === 2) ? checkRequirements() : [];
$allOk = empty(array_filter($requirements, fn($r) => !$r['ok']));

?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Installer — Okenet BMS Monitoring</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
    min-height: 100vh;
    color: #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }
  .card {
    background: rgba(30,41,59,0.95);
    border: 1px solid rgba(99,102,241,0.3);
    border-radius: 16px;
    padding: 40px;
    max-width: 600px;
    width: 100%;
    box-shadow: 0 25px 50px rgba(0,0,0,0.5);
  }
  .logo {
    text-align: center;
    margin-bottom: 32px;
  }
  .logo h1 {
    font-size: 1.8rem;
    font-weight: 700;
    background: linear-gradient(135deg, #6366f1, #a855f7);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  .logo p { color: #94a3b8; font-size: 0.9rem; margin-top: 6px; }
  .steps {
    display: flex;
    gap: 8px;
    margin-bottom: 32px;
    justify-content: center;
  }
  .step-dot {
    width: 32px; height: 32px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.8rem; font-weight: 600;
    border: 2px solid #334155;
    color: #64748b;
    transition: all 0.3s;
  }
  .step-dot.active { background: #6366f1; border-color: #6366f1; color: #fff; }
  .step-dot.done { background: #10b981; border-color: #10b981; color: #fff; }
  .step-line { width: 40px; height: 2px; background: #334155; margin: auto 0; border-radius: 1px; }
  h2 { font-size: 1.3rem; font-weight: 600; margin-bottom: 8px; color: #f1f5f9; }
  .subtitle { color: #94a3b8; font-size: 0.875rem; margin-bottom: 24px; }
  .form-group { margin-bottom: 16px; }
  label { display: block; font-size: 0.85rem; font-weight: 500; color: #cbd5e1; margin-bottom: 6px; }
  input[type=text], input[type=password], input[type=email], input[type=url] {
    width: 100%;
    background: rgba(15,23,42,0.8);
    border: 1px solid #334155;
    border-radius: 8px;
    padding: 10px 14px;
    color: #e2e8f0;
    font-size: 0.9rem;
    outline: none;
    transition: border-color 0.2s;
  }
  input:focus { border-color: #6366f1; }
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
  .btn {
    width: 100%; padding: 12px;
    background: linear-gradient(135deg, #6366f1, #a855f7);
    color: #fff; border: none;
    border-radius: 8px;
    font-size: 0.95rem; font-weight: 600;
    cursor: pointer; margin-top: 8px;
    transition: opacity 0.2s;
  }
  .btn:hover { opacity: 0.9; }
  .btn-outline {
    background: transparent;
    border: 1px solid #475569;
    color: #94a3b8;
    margin-top: 8px;
  }
  .error {
    background: rgba(239,68,68,0.15);
    border: 1px solid rgba(239,68,68,0.4);
    border-radius: 8px;
    padding: 12px 16px;
    color: #fca5a5;
    margin-bottom: 16px;
    font-size: 0.875rem;
  }
  .req-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
  .req-table td { padding: 8px 4px; font-size: 0.85rem; border-bottom: 1px solid #1e293b; }
  .req-table td:last-child { text-align: right; }
  .badge { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
  .badge-ok { background: rgba(16,185,129,0.2); color: #34d399; }
  .badge-err { background: rgba(239,68,68,0.2); color: #f87171; }
  .log-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid #1e293b;
    font-size: 0.875rem;
  }
  .log-item:last-child { border-bottom: none; }
  .icon-ok { color: #34d399; font-size: 1.1rem; }
  .icon-err { color: #f87171; font-size: 1.1rem; }
  .log-msg { color: #94a3b8; font-size: 0.8rem; }
  .success-box {
    text-align: center; padding: 20px 0;
  }
  .success-icon { font-size: 3rem; margin-bottom: 16px; }
  .cred-box {
    background: rgba(15,23,42,0.8);
    border: 1px solid #334155;
    border-radius: 8px;
    padding: 16px;
    margin: 20px 0;
    text-align: left;
  }
  .cred-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 0.875rem; }
  .cred-label { color: #94a3b8; }
  .cred-value { color: #a5b4fc; font-family: monospace; }
  .warn-box {
    background: rgba(245,158,11,0.1);
    border: 1px solid rgba(245,158,11,0.3);
    border-radius: 8px;
    padding: 12px 16px;
    color: #fcd34d;
    font-size: 0.8rem;
    margin-top: 12px;
  }
  small { color: #64748b; font-size: 0.8rem; }
</style>
</head>
<body>
<div class="card">
  <div class="logo">
    <h1>⚡ Okenet BMS Monitoring</h1>
    <p>Web Installer v<?= INSTALLER_VERSION ?></p>
  </div>

  <?php if ($step <= 4): ?>
  <div class="steps">
    <?php
    $steps = ['Cek Sistem', 'Database', 'Admin', 'Install'];
    foreach ($steps as $i => $label):
      $n = $i + 1;
      $cls = $n < $step ? 'done' : ($n === $step ? 'active' : '');
      echo '<div class="step-dot ' . $cls . '">' . ($n < $step ? '✓' : $n) . '</div>';
      if ($n < count($steps)) echo '<div class="step-line"></div>';
    endforeach;
    ?>
  </div>
  <?php endif; ?>

  <?php if ($error): ?>
  <div class="error">⚠️ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <!-- ─── STEP 99: Sudah Terinstall ─── -->
  <?php if ($step === 99): ?>
  <div class="success-box">
    <div class="success-icon">🔒</div>
    <h2>Sudah Terinstall</h2>
    <p class="subtitle">Aplikasi sudah pernah diinstall sebelumnya.<br>Jika ingin reinstall, hapus file <code>storage/installed.lock</code></p>
    <a href="../"><button class="btn">Buka Aplikasi</button></a>
  </div>

  <!-- ─── STEP 1: Cek Requirements ─── -->
  <?php elseif ($step === 1): ?>
  <h2>Persyaratan Sistem</h2>
  <p class="subtitle">Pastikan server Anda memenuhi semua persyaratan di bawah ini.</p>

  <table class="req-table">
    <?php foreach ($requirements as $r): ?>
    <tr>
      <td><?= $r['name'] ?></td>
      <td><?= $r['value'] ?></td>
      <td><span class="badge <?= $r['ok'] ? 'badge-ok' : 'badge-err' ?>"><?= $r['ok'] ? '✓ OK' : '✗ Gagal' ?></span></td>
    </tr>
    <?php endforeach; ?>
  </table>

  <?php if ($allOk): ?>
  <form method="POST">
    <input type="hidden" name="action" value="check">
    <button type="submit" class="btn">Lanjut ke Konfigurasi Database →</button>
  </form>
  <?php else: ?>
  <div class="error">
    ⚠️ Beberapa persyaratan belum terpenuhi. Hubungi penyedia hosting Anda atau periksa kembali.
    <br><small>Refresh halaman setelah diperbaiki.</small>
  </div>
  <button onclick="location.reload()" class="btn">Cek Ulang</button>
  <?php endif; ?>

  <!-- ─── STEP 2: Database Config ─── -->
  <?php elseif ($step === 2): ?>
  <h2>Konfigurasi Database</h2>
  <p class="subtitle">Masukkan detail koneksi database MySQL yang sudah Anda buat di cPanel/hosting.</p>

  <form method="POST">
    <input type="hidden" name="action" value="db_config">

    <div class="form-row">
      <div class="form-group">
        <label>DB Host</label>
        <input type="text" name="db_host" value="localhost" required>
      </div>
      <div class="form-group">
        <label>DB Port</label>
        <input type="text" name="db_port" value="3306" required>
      </div>
    </div>

    <div class="form-group">
      <label>Nama Database</label>
      <input type="text" name="db_name" placeholder="nama_database" required>
      <small>Database yang sudah dibuat di cPanel → MySQL Databases</small>
    </div>

    <div class="form-group">
      <label>Username Database</label>
      <input type="text" name="db_user" placeholder="username_db" required>
    </div>

    <div class="form-group">
      <label>Password Database</label>
      <input type="password" name="db_pass" placeholder="password database">
    </div>

    <div class="form-group">
      <label>URL Aplikasi</label>
      <input type="text" name="app_url" value="https://<?= htmlspecialchars($_SERVER['HTTP_HOST']) ?>" placeholder="https://domain-anda.com">
      <small>URL utama aplikasi tanpa trailing slash</small>
    </div>

    <button type="submit" class="btn">Uji & Lanjutkan →</button>
    <a href="install.php?step=1"><button type="button" class="btn btn-outline">← Kembali</button></a>
  </form>

  <!-- ─── STEP 3: Admin Account ─── -->
  <?php elseif ($step === 3): ?>
  <h2>Akun Administrator</h2>
  <p class="subtitle">Buat akun untuk login ke dashboard BMS Monitoring.</p>

  <form method="POST">
    <input type="hidden" name="action" value="admin_setup">

    <div class="form-group">
      <label>Nama Lengkap</label>
      <input type="text" name="admin_name" value="Administrator" required>
    </div>

    <div class="form-group">
      <label>Email</label>
      <input type="email" name="admin_email" placeholder="admin@domain.com" required>
    </div>

    <div class="form-group">
      <label>Password</label>
      <input type="password" name="admin_pass" placeholder="Minimal 6 karakter" required>
    </div>

    <div class="form-group">
      <label>Konfirmasi Password</label>
      <input type="password" name="admin_pass2" placeholder="Ulangi password" required>
    </div>

    <button type="submit" class="btn">Lanjut ke Konfirmasi →</button>
    <a href="install.php?step=2"><button type="button" class="btn btn-outline">← Kembali</button></a>
  </form>

  <!-- ─── STEP 4: Konfirmasi Install ─── -->
  <?php elseif ($step === 4): ?>
  <h2>Konfirmasi Instalasi</h2>
  <p class="subtitle">Periksa konfigurasi berikut sebelum memulai instalasi.</p>

  <div class="cred-box">
    <div class="cred-row"><span class="cred-label">Database Host</span><span class="cred-value"><?= htmlspecialchars($_SESSION['db_host'] ?? '') ?>:<?= htmlspecialchars($_SESSION['db_port'] ?? '') ?></span></div>
    <div class="cred-row"><span class="cred-label">Database Name</span><span class="cred-value"><?= htmlspecialchars($_SESSION['db_name'] ?? '') ?></span></div>
    <div class="cred-row"><span class="cred-label">Database User</span><span class="cred-value"><?= htmlspecialchars($_SESSION['db_user'] ?? '') ?></span></div>
    <div class="cred-row"><span class="cred-label">URL Aplikasi</span><span class="cred-value"><?= htmlspecialchars($_SESSION['app_url'] ?? '') ?></span></div>
    <div class="cred-row"><span class="cred-label">Admin Email</span><span class="cred-value"><?= htmlspecialchars($_SESSION['admin_email'] ?? '') ?></span></div>
  </div>

  <form method="POST">
    <input type="hidden" name="action" value="install">
    <button type="submit" class="btn">🚀 Mulai Instalasi</button>
    <a href="install.php?step=3"><button type="button" class="btn btn-outline">← Ubah Data</button></a>
  </form>

  <!-- ─── STEP 5: Hasil Install ─── -->
  <?php elseif ($step === 5): ?>
  <?php
    $log      = $_SESSION['install_log'] ?? [];
    $isOk     = $_SESSION['install_ok'] ?? false;
    $appUrl   = $_SESSION['install_url'] ?? '';
    $adminEmail = $_SESSION['admin_email'] ?? '';
  ?>

  <?php if ($isOk): ?>
  <div class="success-box">
    <div class="success-icon">🎉</div>
    <h2>Instalasi Berhasil!</h2>
    <p class="subtitle">Okenet BMS Monitoring sudah siap digunakan.</p>
  </div>
  <?php else: ?>
  <h2>Instalasi Selesai dengan Peringatan</h2>
  <p class="subtitle">Beberapa langkah gagal. Periksa detail di bawah ini.</p>
  <?php endif; ?>

  <?php foreach ($log as $item): ?>
  <div class="log-item">
    <span class="<?= $item['ok'] ? 'icon-ok' : 'icon-err' ?>"><?= $item['ok'] ? '✓' : '✗' ?></span>
    <div>
      <div><?= htmlspecialchars($item['label']) ?></div>
      <?php if (!empty($item['msg'])): ?>
      <div class="log-msg"><?= htmlspecialchars($item['msg']) ?></div>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>

  <?php if ($isOk): ?>
  <div class="cred-box" style="margin-top:20px">
    <div class="cred-row"><span class="cred-label">URL Login</span><span class="cred-value"><?= htmlspecialchars($appUrl) ?>/login</span></div>
    <div class="cred-row"><span class="cred-label">Email</span><span class="cred-value"><?= htmlspecialchars($adminEmail) ?></span></div>
    <div class="cred-row"><span class="cred-label">Password</span><span class="cred-value">sesuai yang Anda isi</span></div>
  </div>

  <div class="warn-box">
    ⚠️ <strong>PENTING KEAMANAN:</strong> Hapus file <code>public/install.php</code> dari server hosting Anda setelah ini!
  </div>

  <a href="<?= htmlspecialchars($appUrl) ?>/login" target="_blank">
    <button class="btn" style="margin-top:16px">🚀 Buka Aplikasi</button>
  </a>
  <?php else: ?>
  <a href="install.php?step=2">
    <button class="btn" style="margin-top:16px">← Coba Lagi</button>
  </a>
  <?php endif; ?>

  <?php endif; ?>

</div>
</body>
</html>
