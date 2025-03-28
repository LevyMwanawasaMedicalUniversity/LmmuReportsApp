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

    New-ADUser -GivenName $FirstName -Surname $LastName -UserPrincipalName $userPrincipalName -SamAccountName $samAccountName -Name $displayName -EmailAddress $Email -AccountPassword $password -Enabled $true

    Write-Output "Success: User $Username created."
} catch {
    Write-Output "Error: $_"
}
