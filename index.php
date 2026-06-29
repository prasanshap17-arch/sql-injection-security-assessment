<!DOCTYPE html>
<!--
    Secure CSIC Lab Build
    - Authentication and search flows are hardened in login.php and search.php.
    - Least-privilege DB account and hashed passwords are defined in setup.sql/config.php.
-->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ministry of Health and Population - Nepal | Patient Record Management System</title>
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
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-center flex-col">
                <div class="text-center mb-4">
                    <div class="text-3xl font-bold mb-1">🇳🇵</div>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-wide">नेपाल सरकार</h1>
                    <h2 class="text-xl md:text-2xl font-semibold">Government of Nepal</h2>
                </div>
                <div class="border-t border-white/30 pt-4 w-full max-w-2xl text-center">
                    <h3 class="text-lg md:text-xl font-medium">स्वास्थ्य तथा जनसंख्या मन्त्रालय</h3>
                    <h4 class="text-lg md:text-xl">Ministry of Health and Population</h4>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <!-- Main Card -->
            <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                <!-- Card Header -->
                <div class="bg-govdark text-white px-8 py-6">
                    <h2 class="text-2xl md:text-3xl font-bold text-center">Patient Record Management System</h2>
                    <p class="text-center mt-2 text-red-200">बिरामी अभिलेख व्यवस्थापन प्रणाली</p>
                </div>
                
                <!-- Card Body -->
                <div class="px-8 py-10">
                    <div class="text-center mb-8">
                        <div class="inline-block bg-red-50 border border-red-200 rounded-lg px-6 py-3 mb-6">
                            <p class="text-govred font-semibold text-lg">⚠️ Authorized Personnel Only</p>
                            <p class="text-gray-600 text-sm">अधिकृत कर्मचारीहरूको लागि मात्र</p>
                        </div>
                    </div>

                    <div class="prose max-w-none text-gray-700 mb-8">
                        <p class="text-center text-lg leading-relaxed">
                            Welcome to the official Patient Record Management System of the Ministry of Health 
                            and Population, Nepal. This secure platform provides authorized healthcare personnel 
                            with access to patient records, medical histories, and treatment information across 
                            all government health facilities nationwide.
                        </p>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6 mb-10">
                        <div class="bg-gray-50 rounded-lg p-5 text-center">
                            <div class="text-3xl mb-2">🏥</div>
                            <h4 class="font-semibold text-gray-800">Patient Records</h4>
                            <p class="text-sm text-gray-600">Access comprehensive patient data</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-5 text-center">
                            <div class="text-3xl mb-2">📋</div>
                            <h4 class="font-semibold text-gray-800">Medical History</h4>
                            <p class="text-sm text-gray-600">View diagnosis & treatment records</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-5 text-center">
                            <div class="text-3xl mb-2">🔒</div>
                            <h4 class="font-semibold text-gray-800">Secure Access</h4>
                            <p class="text-sm text-gray-600">Role-based authentication system</p>
                        </div>
                    </div>

                    <!-- Login Button -->
                    <div class="text-center">
                        <a href="login.php" class="inline-block bg-govred hover:bg-red-900 text-white font-bold py-4 px-12 rounded-lg text-xl transition duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            Staff Login
                        </a>
                        <p class="mt-4 text-gray-500 text-sm">Use your official credentials to access the system</p>
                    </div>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="grid md:grid-cols-2 gap-6 mt-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <h4 class="font-bold text-govred mb-2">📞 Technical Support</h4>
                    <p class="text-gray-600 text-sm">For system issues, contact IT Helpdesk at ext. 4521 or email support@mohp.gov.np</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h4 class="font-bold text-govred mb-2">📜 Data Protection Notice</h4>
                    <p class="text-gray-600 text-sm">All patient data is protected under the Nepal Health Act 2074. Unauthorized access is prohibited.</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <p class="text-sm mb-2">
                    Government of Nepal | Ministry of Health and Population | For Official Use Only
                </p>
                <p class="text-xs text-gray-400 mb-4">
                    Ramshahpath, Kathmandu | Tel: +977-1-4262802 | www.mohp.gov.np
                </p>
                <div class="border-t border-gray-700 pt-4 mt-4">
                    <span class="inline-block bg-yellow-600 text-yellow-100 text-xs px-3 py-1 rounded-full">
                        ⚠️ Educational Demo — Not Real Data — Academic Lab Simulation
                    </span>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
