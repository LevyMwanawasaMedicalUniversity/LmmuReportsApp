<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><b>Exam Results Notification</b></title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; font-size: 16px; margin: 20px;">
        <b>
            <b>
                <h2>Exam Results Notification</h2>
                <p>Dear {{ $studentDetails->FirstName }} {{ $studentDetails->Surname }},</p>            

                <P>Kindly accept our apologies and disregard the previously sent emails.</P>
                <p>We are pleased to inform you that you can also access your results at <a href="http://sisreports.lmmu.ac.zm/">http://sisreports.lmmu.ac.zm</a>.</p>
                <p>Please note that from now on, you will use your personal email '{{ $studentDetails->PrivateEmail }}' as your username and '12345678' as your password.</p>
                <p>Login using '{{ $studentDetails->PrivateEmail }}' as your username and '12345678' as your password.</p>
                <p>If you encounter any challenges logging in, please utilize the "Forgot Password" option located just below the login section to reset your password. Enter '{{ $studentDetails->PrivateEmail }}' as the email address to which the reset password link will be sent, and then follow the provided instructions.</p>
                <p>Note that '{{ $studentDetails->PrivateEmail }}' is the email address registered on the system, and therefore, it is the only one you can use to log in and access your results or reset your password.</p>
                <p>Note that your password has been reset to '12345678'. Be advised to update your passwords as this is not a secure password</p>
                <p>Note that only the students eligible to view their results will be able to view them through the system.</p>
                <p>Disclaimer: If you are not the intended recipient of this email, please ignore it and accept our apologies.</p>
                <p>Best Regards,</p>
                <p>Registrar</p>
                <p>Levy Mwanawasa Medical University, LUSAKA.</p>

            </b>
        </b>
    </div>
</body>
</html>