<?php
/**
 * Staff Login Page (SECURED)
 * Ministry of Health and Population - Nepal
 *
 * Security controls applied:
 * 1. Prepared statements via PDO (no SQL string concatenation).
 * 2. Password verification with password_verify() (hash-based auth).
 * 3. Input length validation before DB access.
 * 4. Session fixation protection (session_regenerate_id on login).
 * 5. Session binding metadata (IP + last activity) for downstream checks.
 *
 * Known limitations (documented for hardening backlog):
 * - No brute-force rate limiting/account lockout yet.
 * - No CSRF token on this login form yet.
 */

session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: search.php");
    exit();
}

require_once 'config.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (
        $username === '' ||
        $password === '' ||
        strlen($username) > 50 ||
        strlen($password) > 100
    ) {
        $error_message = "Invalid credentials. Access denied.";
    } else {
        try {
            // SECURED: query structure is fixed; user input is bound separately.
            $stmt = $pdo->prepare(
                "SELECT username, password, role, full_name, clearance_level
                 FROM staff
                 WHERE username = :username
                 LIMIT 1"
            );
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();

            // SECURED: password validated against bcrypt hash in application layer.
            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);

                $_SESSION['logged_in'] = true;
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['clearance_level'] = $user['clearance_level'];
                $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
                $_SESSION['last_activity'] = time();

                header("Location: search.php");
                exit();
            }

            $error_message = "Invalid credentials. Access denied.";
        } catch (PDOException $e) {
            error_log('Login query failed: ' . $e->getMessage());
            $error_message = "System error. Please contact IT support.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login | Ministry of Health and Population - Nepal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        govred: '#8B0000',
                        govdark: '#991B1B',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-govred text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-center flex-col">
                <div class="text-center">
                    <h1 class="text-xl md:text-2xl font-bold">Government of Nepal | Ministry of Health and Population</h1>
                    <p class="text-sm text-red-200">नेपाल सरकार | स्वास्थ्य तथा जनसंख्या मन्त्रालय</p>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow container mx-auto px-4 py-12 flex items-center justify-center">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                <div class="bg-govdark text-white px-6 py-5">
                    <h2 class="text-xl font-bold text-center">🔐 Staff Authentication</h2>
                    <p class="text-center text-red-200 text-sm mt-1">Patient Record Management System</p>
                </div>

                <div class="px-8 py-8">
                    <?php if ($error_message): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                        <span class="text-xl mr-2">⚠️</span>
                        <div>
                            <p class="font-bold">Authentication Failed</p>
                            <p class="text-sm"><?php echo htmlspecialchars($error_message); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="login.php" class="space-y-6">
                        <div>
                            <label for="username" class="block text-gray-700 font-semibold mb-2">
                                Username
                            </label>
                            <input
                                type="text"
                                id="username"
                                name="username"
                                required
                                maxlength="50"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-govred focus:border-transparent transition"
                                placeholder="Enter your username"
                            >
                        </div>

                        <div>
                            <label for="password" class="block text-gray-700 font-semibold mb-2">
                                Password
                            </label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                maxlength="100"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-govred focus:border-transparent transition"
                                placeholder="Enter your password"
                            >
                        </div>

                        <button
                            type="submit"
                            class="w-full bg-govred hover:bg-red-900 text-white font-bold py-3 px-4 rounded-lg transition duration-300 shadow-md hover:shadow-lg"
                        >
                            Login to System
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <a href="index.php" class="text-govred hover:text-red-900 text-sm font-medium">
                            ← Back to Home
                        </a>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t">
                    <p class="text-xs text-gray-500 text-center">
                        🔒 This is a secure government system. All login attempts are logged and monitored.
                    </p>
                </div>
            </div>

            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-blue-800 text-sm mb-1">🆘 Need Help?</h4>
                <p class="text-xs text-blue-700">
                    Contact IT Support at ext. 4521 or email helpdesk@mohp.gov.np for password resets and access issues.
                </p>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-4">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <p class="text-xs text-gray-400 mb-2">
                    Government of Nepal | Ministry of Health and Population | For Official Use Only
                </p>
                <span class="inline-block bg-green-700 text-green-100 text-xs px-3 py-1 rounded-full">
                    ✅ Secure Build — Prepared Statements + Hashed Password Verification
                </span>
            </div>
        </div>
    </footer>
</body>
</html>
