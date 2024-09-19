<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><b>2024 EXAMINATION DOCKETS</b></title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; font-size: 16px; margin: 20px;">
        <h2>2024 EXAMINATION DOCKETS</h2>
        <p>Dear {{ $studentDetails->FirstName }} {{ $studentDetails->Surname }},</p>

        <p>We are pleased to inform you that you can access your examination docket at <a href="http://sisreports.lmmu.ac.zm/">http://sisreports.lmmu.ac.zm</a>.</p>
        <p>Please log in using your personal email address '{{ $studentDetails->PrivateEmail }}' as your username and '12345678' as your default password, if you have not yet changed it.</p>
        <p><strong>Important:</strong> To view your docket, you must meet the following requirements:</p>
        <ul>
            <li>You must have paid at least 75% of your 2024 invoice.</li>
            <li>You must have completed your course registration.</li>
            <li>Your courses must be approved, and an invoice for 2024 should have been posted to your account.</li>
        </ul>
        <p>If your courses are not approved, please contact your Programme Coordinator for approval. If you have not been invoiced for 2024, kindly visit the Accounts Department.</p>
        <p>If you encounter any difficulties logging in, please use the "Forgot Password" option on the login page. Enter '{{ $studentDetails->PrivateEmail }}' as your registered email address, and follow the instructions to reset your password.</p>
        <p>Remember, '{{ $studentDetails->PrivateEmail }}' is the only email address registered in the system, and it must be used to log in or reset your password.</p>
        <p>Note that your default password is '12345678'. We recommend updating it as soon as possible for security reasons.</p>
        <p>If you are not the intended recipient of this email, please disregard it, and we apologize for any inconvenience.</p>
        
        <p>Best Regards,</p>
        <p>Registrar</p>
        <p>Levy Mwanawasa Medical University, LUSAKA.</p>
    </div>
</body>
</html>
