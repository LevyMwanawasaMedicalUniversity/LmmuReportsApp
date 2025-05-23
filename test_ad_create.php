<?php
// Enable detailed error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load Laravel .env file environment variables (using Dotenv)
require __DIR__.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Helper function to mimic Laravel's env() function
function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}

// LDAP configuration from .env
$adServer = 'ldap://' . env('LDAP_SERVER');
$adDomain = env('LDAP_DOMAIN');
$adNetbiosDomain = explode('.', $adDomain)[0]; // Get first part (lmmustudents)
$adUsername = $adNetbiosDomain . '\\' . env('LDAP_ADMIN_USERNAME');
$adPassword = env('LDAP_ADMIN_PASSWORD');
$adBaseDn = env('LDAP_BASE_DN');

echo "<h1>Active Directory User Creation Test</h1>";
echo "<pre>";

// Create a test account
echo "Connecting to LDAP server: $adServer\n";
$ldapConn = ldap_connect($adServer);

if (!$ldapConn) {
    die("Failed to connect to LDAP server\n");
}

// Set LDAP options
ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);
ldap_set_option($ldapConn, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);

// Bind with admin credentials
echo "Binding with credentials: $adUsername\n";
$bind = @ldap_bind($ldapConn, $adUsername, $adPassword);

if (!$bind) {
    die("Bind failed: " . ldap_error($ldapConn) . "\n");
}

echo "Bind successful!\n\n";

// Try a very minimal user creation
$timestamp = time();
$testUser = "testuser" . $timestamp;
$testDn = "CN=$testUser," . $adBaseDn;

echo "Attempting to create minimal test user:\n";
echo "DN: $testDn\n";

// Prepare minimal user attributes
$userAttrs = [
    'objectClass' => ['top', 'person', 'organizationalPerson', 'user'],
    'cn' => $testUser,
    'sn' => 'User',
    'givenName' => 'Test',
    'displayName' => $testUser,
    'sAMAccountName' => $testUser,
    'userAccountControl' => '514'  // 514 = disabled account (512 + 2)
];

echo "Attributes:\n";
print_r($userAttrs);

// Try to create the user
$result = @ldap_add($ldapConn, $testDn, $userAttrs);

if ($result) {
    echo "\nUser creation SUCCESSFUL!\n";
    echo "Now trying to delete the test user...\n";
    
    // Try to delete the test user
    $deleteResult = @ldap_delete($ldapConn, $testDn);
    if ($deleteResult) {
        echo "Test user deleted successfully.\n";
    } else {
        echo "Failed to delete test user: " . ldap_error($ldapConn) . "\n";
    }
} else {
    echo "\nUser creation FAILED: " . ldap_error($ldapConn) . "\n";
    echo "Error number: " . ldap_errno($ldapConn) . "\n";
    
    // Try to determine the issue
    if (ldap_error($ldapConn) == "Server is unwilling to perform") {
        // Try listing the permissions on the OU
        echo "\nChecking permissions on OU...\n";
        $search = @ldap_search($ldapConn, $adBaseDn, "(objectClass=*)", ['ntSecurityDescriptor']);
        
        if ($search) {
            echo "OU exists and is accessible.\n";
            
            // Try a different location - Users container
            $domainDN = str_replace('OU=registeredStudents,', '', $adBaseDn);
            $usersContainer = "CN=Users," . $domainDN;
            $testDnInUsers = "CN=$testUser," . $usersContainer;
            
            echo "\nTrying to create user in the default Users container instead:\n";
            echo "DN: $testDnInUsers\n";
            
            $resultInUsers = @ldap_add($ldapConn, $testDnInUsers, $userAttrs);
            
            if ($resultInUsers) {
                echo "User creation in Users container SUCCESSFUL!\n";
                echo "This suggests the issue is with permissions on your target OU.\n";
                
                // Delete the test user
                $deleteResult = @ldap_delete($ldapConn, $testDnInUsers);
                if ($deleteResult) {
                    echo "Test user deleted successfully.\n";
                } else {
                    echo "Failed to delete test user: " . ldap_error($ldapConn) . "\n";
                }
            } else {
                echo "User creation in Users container also FAILED: " . ldap_error($ldapConn) . "\n";
                echo "This suggests a more fundamental issue with user creation permissions.\n";
            }
        } else {
            echo "Could not access OU: " . ldap_error($ldapConn) . "\n";
        }
    }
}

// Close the connection
ldap_unbind($ldapConn);
echo "</pre>";
?>
