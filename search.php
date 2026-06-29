<?php
/**
 * Patient Search Page (SECURED)
 * Ministry of Health and Population - Nepal
 *
 * Security controls applied:
 * 1. Prepared statements via PDO for LIKE searches.
 * 2. Input length and pattern validation before query execution.
 * 3. Generic user-facing errors (no DB error leakage).
 * 4. Debug SQL output removed.
 * 5. Session hardening (IP binding + inactivity timeout).
 */

session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

function force_logout_and_redirect(): void
{
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }

    session_destroy();
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// SECURED: session inactivity timeout (15 minutes).
if (isset($_SESSION['last_activity']) && (time() - (int) $_SESSION['last_activity'] > 900)) {
    force_logout_and_redirect();
}

// SECURED: bind session to origin IP address.
$current_ip = $_SERVER['REMOTE_ADDR'] ?? '';
if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $current_ip) {
    force_logout_and_redirect();
}

$_SESSION['last_activity'] = time();

require_once 'config.php';

$search_query = '';
$results = [];
$error_message = '';
$search_performed = false;

if (isset($_GET['q'])) {
    $search_performed = true;
    $raw_input = trim($_GET['q']);
    $search_query = $raw_input;

    if ($raw_input === '') {
        $error_message = "Please enter a patient name to search.";
    } elseif (strlen($raw_input) > 100) {
        $error_message = "Search term too long.";
    } elseif (!preg_match("/^[\\p{L}\\s'-]+$/u", $raw_input)) {
        $error_message = "Search term contains invalid characters.";
    } else {
        try {
            // SECURED: wildcard added in PHP, value bound as parameter.
            $stmt = $pdo->prepare(
                "SELECT id, patient_id, full_name, age, gender, district, diagnosis, contact
                 FROM patients
                 WHERE full_name LIKE :search
                 LIMIT 50"
            );
            $stmt->execute([':search' => '%' . $raw_input . '%']);
            $results = $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Search query failed: ' . $e->getMessage());
            $error_message = "Search is temporarily unavailable. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Search | Ministry of Health and Population - Nepal</title>
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
    <header class="bg-govred text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg md:text-xl font-bold">Government of Nepal | Ministry of Health and Population</h1>
                    <p class="text-xs text-red-200">Patient Record Management System</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm font-semibold"><?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                        <p class="text-xs text-red-200"><?php echo htmlspecialchars($_SESSION['role']); ?></p>
                    </div>
                    <a href="logout.php" class="bg-white text-govred px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-100 transition">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Patient Record Search</h2>
                        <p class="text-gray-600">Search patient records by name</p>
                    </div>
                    <div class="bg-green-100 text-green-800 px-4 py-2 rounded-lg">
                        <span class="text-sm font-medium">🟢 Clearance: <?php echo htmlspecialchars($_SESSION['clearance_level']); ?></span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <form method="GET" action="search.php" class="flex gap-4">
                    <div class="flex-grow">
                        <label for="search" class="block text-gray-700 font-semibold mb-2">
                            Search Patient by Name
                        </label>
                        <input
                            type="text"
                            id="search"
                            name="q"
                            maxlength="100"
                            value="<?php echo htmlspecialchars($search_query); ?>"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-govred focus:border-transparent"
                            placeholder="Enter patient name..."
                        >
                    </div>
                    <div class="flex items-end">
                        <button
                            type="submit"
                            class="bg-govred hover:bg-red-900 text-white font-bold py-3 px-8 rounded-lg transition duration-300"
                        >
                            🔍 Search
                        </button>
                    </div>
                </form>
                <p class="text-xs text-gray-500 mt-3">
                    Allowed input: letters, spaces, apostrophes, and hyphens.
                </p>
            </div>

            <?php if ($error_message): ?>
            <div class="bg-red-100 border border-red-300 rounded-lg p-4 mb-6">
                <p class="text-red-700 text-sm font-medium"><?php echo htmlspecialchars($error_message); ?></p>
            </div>
            <?php endif; ?>

            <?php if ($search_performed && !$error_message): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Search Results
                        <span class="text-sm font-normal text-gray-500">(<?php echo count($results); ?> records found)</span>
                    </h3>
                </div>

                <?php if (count($results) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-govred text-white">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold">ID</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Patient ID</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Full Name</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Age</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Gender</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">District</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Diagnosis</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Contact</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($results as $index => $row): ?>
                            <tr class="<?php echo $index % 2 === 0 ? 'bg-white' : 'bg-gray-50'; ?> hover:bg-red-50 transition">
                                <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($row['id'] ?? ''); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-800 font-mono"><?php echo htmlspecialchars($row['patient_id'] ?? ''); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-800 font-medium"><?php echo htmlspecialchars($row['full_name'] ?? ''); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($row['age'] ?? ''); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($row['gender'] ?? ''); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($row['district'] ?? ''); ?></td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">
                                        <?php echo htmlspecialchars($row['diagnosis'] ?? ''); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-800 font-mono"><?php echo htmlspecialchars($row['contact'] ?? ''); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="p-8 text-center">
                    <div class="text-4xl mb-4">📋</div>
                    <p class="text-gray-500 text-lg">No records found</p>
                    <p class="text-gray-400 text-sm mt-1">Try a different search term</p>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (!$search_performed): ?>
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <div class="text-5xl mb-4">🔍</div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Search Patient Records</h3>
                <p class="text-gray-500">Enter a patient name above to search the database</p>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-4">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <p class="text-xs text-gray-400 mb-2">
                    Government of Nepal | Ministry of Health and Population | For Official Use Only
                </p>
                <span class="inline-block bg-green-700 text-green-100 text-xs px-3 py-1 rounded-full">
                    ✅ Secure Build — Parameterized Search + No Debug SQL Leakage
                </span>
            </div>
        </div>
    </footer>
</body>
</html>
