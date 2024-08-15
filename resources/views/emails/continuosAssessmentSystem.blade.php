<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CONTINOUS ASSESSMENTS</title>
</head>
<body>
    <p>Dear {{ $studentDetails->FirstName }} {{ $studentDetails->Surname }} ( {{ $studentId }} ),</p>
    <p>We are pleased to inform you that you can now view your Continuous Assessment (CA) results for the 2024 academic year on the <a href="http://sisreports.lmmu.ac.zm/">SIS Reports system</a>. To access your results, simply log in to your account using your <strong>{{ $studentLocalDetails->email }}</strong> email address as your username. If you have not updated your password, it remains "12345678". Navigate to the "CA Results" section in the menu after logging in to review your scores.</p>
    <p><strong>Please note: The CA results are still in the process of being uploaded. If you notice any discrepancies or have concerns regarding your results, please contact your Programme Coordinator for assistance. Ensuring the accuracy of your academic records is our priority. <b>Only students that are Registered for the 2024 Academic Year will be able to view their Continuous Assessment.</b></strong></p>
    <p>If you experience any issues while accessing your results, feel free to reach out to ICT Office for support. Your academic success is our top priority.</p>

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