<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><b>Exam Docket For Deffered and Supplementary Exams</b></title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; font-size: 16px; margin: 20px;">
        <b>
            <b>
                <h2>Exam Docket For Deffered and Supplementary Exams</h2>
                <p>Dear {{ $studentDetails->FirstName }} {{ $studentDetails->Surname }},</p>

                <p>We are pleased to provide you with your exam docket, which is attached to this email.</p>
                <p>Ensure document accuracy. Use your student ID or National Registration Card for validity when attending your exams.</p>

                <p>You can also access your docket at <a href="http://sisreports.lmmu.ac.zm/">http://sisreports.lmmu.ac.zm</a>.</p>
                <p>Please note that from now on you will use your personal email '{{ $studentDetails->PrivateEmail }}' as your username and your National Registration Card number '{{ $studentDetails->GovernmentID }}' as your password.</p>
                <p>Login using '{{ $studentDetails->PrivateEmail}}' as your username and National Registration Card number '{{ $studentDetails->GovernmentID }}' as your password. </p>
                <p>If you encounter any challenges logging in, please utilize the "Forgot Password" option located just below the login section to reset your password. Enter '{{ $studentDetails->PrivateEmail}}' as the email address to which the reset password link will be sent, and then follow the provided instructions.</p>
                <p>Note that '{{ $studentDetails->PrivateEmail}}' is the email address registered on the system, and therefore it is the only one you can use to log in and access your docket or reset your password.</p>
                <p>If errors arise, visit the Academic Office or ICT Office. Your success is our priority.</p>
                
                <p>Best Regards,</p>
                <p>Registrar</p>
                <p>Levy Mwanawasa Medical University, LUSAKA.</p>                             
            </b>
        </b>
    </div>
</body>
</html>