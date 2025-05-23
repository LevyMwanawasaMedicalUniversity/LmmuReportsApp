<?php
// Check if LDAP extension is available
if (function_exists('ldap_connect')) {
    echo "LDAP extension is installed and available.\n";
    echo "LDAP functions found: \n";
    $ldap_functions = get_extension_funcs('ldap');
    if ($ldap_functions) {
        echo implode("\n", $ldap_functions);
    } else {
        echo "No LDAP functions found, which is unexpected.";
    }
} else {
    echo "LDAP extension is NOT installed or enabled.\n";
    echo "You need to enable the LDAP extension in your PHP configuration.\n";
}
