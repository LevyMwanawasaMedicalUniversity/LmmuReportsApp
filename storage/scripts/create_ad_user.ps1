param (
    [string]$FirstName,
    [string]$LastName,
    [string]$Email,
    [string]$Username,
    [string]$Password,
    [string]$LdapServer,
    [int]$LdapPort,
    [string]$LdapBaseDN,
    [string]$Domain,
    [string]$AdminUsername,
    [string]$AdminPassword
)

# Determine which method to use (Active Directory module or generic LDAP)
$useActiveDirectory = $true  # Set to $false to use generic LDAP instead

try {
    if ($useActiveDirectory) {
        # Method 1: Using Active Directory PowerShell Module
        # Check if the module is available
        if (-not (Get-Module -ListAvailable -Name ActiveDirectory)) {
            Write-Output "Error: ActiveDirectory module not available. Install RSAT tools or use LDAP method."
            exit 1
        }
        
        Import-Module ActiveDirectory

        # Set up user properties
        $userPrincipalName = "$Username@$Domain"
        $samAccountName = $Username
        $displayName = "$FirstName $LastName"
        $securePassword = ConvertTo-SecureString $Password -AsPlainText -Force

        # Set up admin credentials for the operation
        $adminSecurePassword = ConvertTo-SecureString $AdminPassword -AsPlainText -Force
        $credential = New-Object System.Management.Automation.PSCredential("$Domain\$AdminUsername", $adminSecurePassword)

        # Create the user with the admin credentials
        New-ADUser -GivenName $FirstName `
                  -Surname $LastName `
                  -UserPrincipalName $userPrincipalName `
                  -SamAccountName $samAccountName `
                  -Name $displayName `
                  -EmailAddress $Email `
                  -AccountPassword $securePassword `
                  -Enabled $true `
                  -Path $LdapBaseDN `
                  -Credential $credential

        Write-Output "Success: User $Username created in Active Directory."
    }
    else {
        # Method 2: Using DirectoryServices.Protocols for generic LDAP
        Add-Type -AssemblyName System.DirectoryServices.Protocols

        # Set up the LDAP connection
        $ldapConnection = New-Object System.DirectoryServices.Protocols.LdapConnection "$LdapServer`:$LdapPort"
        $ldapConnection.AuthType = [System.DirectoryServices.Protocols.AuthType]::Basic
        $networkCredential = New-Object System.Net.NetworkCredential($AdminUsername, $AdminPassword)
        $ldapConnection.Bind($networkCredential)

        # Create a new entry
        $userDN = "cn=$displayName,$LdapBaseDN"
        $addRequest = New-Object System.DirectoryServices.Protocols.AddRequest($userDN)

        # Add required object classes (adjust based on your LDAP schema)
        $addRequest.Attributes.Add((New-Object System.DirectoryServices.Protocols.DirectoryAttribute("objectClass", @("top", "person", "organizationalPerson", "inetOrgPerson"))))
        
        # Add user attributes
        $addRequest.Attributes.Add((New-Object System.DirectoryServices.Protocols.DirectoryAttribute("cn", $displayName)))
        $addRequest.Attributes.Add((New-Object System.DirectoryServices.Protocols.DirectoryAttribute("sn", $LastName)))
        $addRequest.Attributes.Add((New-Object System.DirectoryServices.Protocols.DirectoryAttribute("givenName", $FirstName)))
        $addRequest.Attributes.Add((New-Object System.DirectoryServices.Protocols.DirectoryAttribute("uid", $Username)))
        $addRequest.Attributes.Add((New-Object System.DirectoryServices.Protocols.DirectoryAttribute("mail", $Email)))
        $addRequest.Attributes.Add((New-Object System.DirectoryServices.Protocols.DirectoryAttribute("userPassword", $Password)))

        # Send the request
        $ldapConnection.SendRequest($addRequest)
        
        Write-Output "Success: User $Username created in LDAP directory."
    }
} catch {
    Write-Output "Error: $_"
    exit 1
}
