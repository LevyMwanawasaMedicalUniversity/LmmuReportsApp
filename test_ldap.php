<?php
// Enable detailed error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load Laravel .env file environment variables (using Dotenv)
require __DIR__.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// LDAP configuration from .env
$adServer = 'ldap://' . env('LDAP_SERVER');
$adDomain = env('LDAP_DOMAIN');
$adNetbiosDomain = explode('.', $adDomain)[0]; // Get first part (lmmustudents)
$adUsername = $adNetbiosDomain . '\\' . env('LDAP_ADMIN_USERNAME');
$adPassword = env('LDAP_ADMIN_PASSWORD');
$adBaseDn = env('LDAP_BASE_DN');

// Helper function to mimic Laravel's env() function
function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}

echo "<h1>LDAP Connection Test</h1>";
echo "<pre>";

// Step 1: Test basic connection
echo "Testing connection to LDAP server: $adServer\n";
$ldapConn = ldap_connect($adServer);

if (!$ldapConn) {
    echo "Failed to connect to LDAP server\n";
    exit;
}

echo "Successfully connected to LDAP server\n";

// Step 2: Set LDAP options
ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

// Step 3: Try different binding methods
echo "\nTrying to bind with different authentication methods:\n";

// Method 1: NetBIOS (DOMAIN\username)
echo "\nMethod 1: NetBIOS (DOMAIN\\username)\n";
echo "Using credentials: $adUsername / [password hidden]\n";
$bindNetbios = @ldap_bind($ldapConn, $adUsername, $adPassword);
echo "Result: " . ($bindNetbios ? "SUCCESS" : "FAILED - " . ldap_error($ldapConn)) . "\n";

// Method 2: UserPrincipalName (username@domain)
$upnUsername = env('LDAP_ADMIN_USERNAME') . '@' . $adDomain;
echo "\nMethod 2: UPN (username@domain)\n";
echo "Using credentials: $upnUsername / [password hidden]\n";
$bindUpn = @ldap_bind($ldapConn, $upnUsername, $adPassword);
echo "Result: " . ($bindUpn ? "SUCCESS" : "FAILED - " . ldap_error($ldapConn)) . "\n";

// Method 3: Distinguished Name
$dnUsername = "CN=" . env('LDAP_ADMIN_USERNAME') . ",CN=Users," . str_replace('OU=registeredStudents,', '', $adBaseDn);
echo "\nMethod 3: Distinguished Name\n";
echo "Using credentials: $dnUsername / [password hidden]\n";
$bindDn = @ldap_bind($ldapConn, $dnUsername, $adPassword);
echo "Result: " . ($bindDn ? "SUCCESS" : "FAILED - " . ldap_error($ldapConn)) . "\n";

// If any of the binds worked, try to create a test user
if ($bindNetbios || $bindUpn || $bindDn) {
    echo "\n\nAttempting to create a test user...\n";
    
    // Use the successful binding method
    if (!($bindNetbios || $bindUpn || $bindDn)) {
        echo "No binding method worked, can't test user creation\n";
        exit;
    }
    
    // Clean name for the test user
    $testUserName = "Test User " . time();
    $testSamAccountName = "testuser" . time();
    
    // Create DN for new user
    $userDn = "CN=$testUserName,$adBaseDn";
    echo "Attempting to create user with DN: $userDn\n";
    
    // Create minimal attributes for the test user
    $testAttrs = [
        'objectclass' => ['top', 'person', 'organizationalPerson', 'user'],
        'cn' => $testUserName,
        'sn' => 'User',
        'givenname' => 'Test',
        'displayname' => $testUserName,
        'name' => $testUserName,
        'sAMAccountName' => $testSamAccountName,
        'userPrincipalName' => "$testSamAccountName@$adDomain",
        'description' => 'Test Account',
        'userAccountControl' => '512' // Normal account
    ];
    
    // Try to add the user
    $addUser = @ldap_add($ldapConn, $userDn, $testAttrs);
    
    if ($addUser) {
        echo "User creation successful! This means your code should work with the correct attributes.\n";
    } else {
        echo "User creation failed: " . ldap_error($ldapConn) . "\n";
        
        // If we get "Server is unwilling to perform", try to determine why
        if (ldap_error($ldapConn) == "Server is unwilling to perform") {
            echo "\nPossible reasons for 'Server is unwilling to perform':\n";
            echo "1. Insufficient permissions for the user account\n";
            echo "2. The OU does not exist or you don't have permissions to add objects to it\n";
            echo "3. Missing required attributes\n";
            echo "4. Policy restrictions\n";
            
            // Try to perform a search on the base DN to verify it exists
            echo "\nVerifying if the Base DN exists and is accessible: $adBaseDn\n";
            $search = @ldap_search($ldapConn, $adBaseDn, "(objectClass=*)");
            
            if ($search) {
                echo "Base DN exists and is accessible. There might be permission issues or missing required attributes.\n";
                $entries = ldap_get_entries($ldapConn, $search);
                echo "Number of entries found: " . $entries["count"] . "\n";
            } else {
                echo "Base DN check failed: " . ldap_error($ldapConn) . "\n";
                echo "This suggests the OU might not exist or is not accessible with your credentials.\n";
            }
            
            // Try listing the OUs available to this user
            echo "\nListing available OUs at the domain root:\n";
            $domainDN = str_replace('OU=registeredStudents,', '', $adBaseDn);
            $ouSearch = @ldap_search($ldapConn, $domainDN, "(objectClass=organizationalUnit)");
            
            if ($ouSearch) {
                $ous = ldap_get_entries($ldapConn, $ouSearch);
                echo "Found " . $ous["count"] . " OUs:\n";
                
                for ($i = 0; $i < $ous["count"]; $i++) {
                    echo "- " . $ous[$i]["dn"] . "\n";
                }
            } else {
                echo "Could not list OUs: " . ldap_error($ldapConn) . "\n";
            }
        }
    }
}

// Always close the connection
ldap_unbind($ldapConn);
echo "</pre>";
