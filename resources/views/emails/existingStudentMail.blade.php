<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Course Registration Information</title>
</head>
<body>
    <p>Dear {{ $studentDetails->FirstName }} {{ $studentDetails->Surname }} ( {{ $studentId }} ),</p>
    <br>
    <p>We are delighted to inform you that course registration for the 2024 academic year is now open on the <a href="http://sisreports.lmmu.ac.zm/">SIS Reports system</a>. You can easily complete your course registration by logging into your account with your <strong>{{ $studentLocalDetails->email }}</strong> email address as your username and the password is"12345678" if and only if you have not update it. Simply navigate to the Course Registration section on the menu after logging in and submit your preferred courses for registration.</p>

    <p><strong>Please note:</strong> To access the course registration feature, you must have paid at least 25% of your invoice for the year 2024. Your registration will be processed based on payments made in 2024 only.</p>

    <p>Should you encounter any difficulties during the registration process, please do not hesitate to reach out to the Academic Office or ICT Office for assistance. Your academic success is our top priority.</p>

    <p>You can access Sis Reports Using the link below:</p>
        
    <p><a href="http://sisreports.lmmu.ac.zm/">http://sisreports.lmmu.ac.zm</a></p>

    <p><strong>For login:</strong></p>
    <ul>
        <li>Username: <strong>{{ $studentLocalDetails->email }}</strong></li>
        <li>Password: <strong>12345678 (if you have not updated it yet)</strong></li>
        <li><strong>IF YOU HAVE FORGOTTEN YOUR PASSWORD, PLEASE USE THE "FORGOT PASSWORD" OPTION BELOW THE LOGIN SECTION TO RESET YOUR PASSWORD</strong></li>
    </ul>
    
    <p><strong>Note:</strong> Your current password is set to "12345678" if you have not updated it yet. '{{ $studentLocalDetails->email }}' is the registered email address and the only one you can use to log in or reset your password.</p>
    
    <p><strong>If you forget your password, you can utilize the <a href="http://sisreports.lmmu.ac.zm/password/reset">Forgot Password"</a> option below the login section. Enter '{{ $studentLocalDetails->email }}' as the email address, and follow the instructions sent to your email to reset your password.</strong></p>
        <p><strong>Disclaimer:</strong> If you have received this email in error, please disregard it and do not take any action based on its content. Any unauthorized use, dissemination, or reproduction of this email is prohibited and may be unlawful. Please notify the sender immediately by replying to this email, and then delete it from your system.</p>

    <p>Thank you,</p>
    <p>Registrar</p>
</body>
</html>