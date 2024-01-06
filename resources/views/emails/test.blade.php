<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><b>Exam Docket Notification</b></title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; font-size: 16px; margin: 20px;">
        <b>
            <b>
                <h2>Exam Docket Notification</h2>
                <p>Dear {{ $studentDetails->FirstName }} {{ $studentDetails->Surname }},</p>

                <p>We are pleased to provide you with your exam docket, which is attached to this email. You have been awarded this docket because your 2023 payments account for 25% of the total payments required for the 2023 academic year.</p>
                <p>Ensure document accuracy. Use your student ID or National Registration Card for validity when attending your exams.</p>

                <p>You can also access your docket at <a href="http://sisreports.lmmu.ac.zm/">http://sisreports.lmmu.ac.zm</a>.</p>

                <p><strong>If you encounter any challenges logging in, please utilize the "Forgot Password" option located just below the login section to reset your password. Enter '{{ $studentDetails->PrivateEmail }}' as the email address to which the reset password link will be sent, and then follow the provided instructions.</strong></p>

                <p>If errors arise, visit the Academic Office or ICT Office. Your success is our priority.</p>
                <p>Please accept our apologies if you have already graduated or withdrawn from the university and should not be receiving this email. In such cases, please disregard this notification.</p>
                <p>Best Regards,</p>
                <p>Registrar</p>
                <p>Levy Mwanawasa Medical University, LUSAKA.</p>                             
            </b>
        </b>
    </div>
</body>
</html>