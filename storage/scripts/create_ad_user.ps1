param (
    [string]$FirstName,
    [string]$LastName,
    [string]$Email,
    [string]$Username,
    [string]$Password
)

try {
    Import-Module ActiveDirectory

    $userPrincipalName = "$Username@example.com"
    $samAccountName = $Username
    $displayName = "$FirstName $LastName"
    $password = ConvertTo-SecureString $Password -AsPlainText -Force

    # Specify credentials (replace with your service account username and password)
    $securePassword = ConvertTo-SecureString "YourPassword" -AsPlainText -Force
    $credential = New-Object System.Management.Automation.PSCredential("YourDomain\YourUsername", $securePassword)

    # Use the credentials to create the user
    New-ADUser -GivenName $FirstName -Surname $LastName -UserPrincipalName $userPrincipalName -SamAccountName $samAccountName -Name $displayName -EmailAddress $Email -AccountPassword $password -Enabled $true -Credential $credential

    Write-Output "Success: User $Username created."
} catch {
    Write-Output "Error: $_"
}
