<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><b>Exam Docket For Deffered and Supplementary Exams</b></title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; font-size: 16px; margin: 20px;">
        {{-- <b>
            <b> --}}
                <h2>Exam Docket For Deferred and Supplementary Exams</h2>

                <p>Dear {{ $studentDetails->FirstName }} {{ $studentDetails->Surname }},</p>

                <p>We are pleased to provide you with your exam docket, which is attached to this email. Please ensure the accuracy of the document. Use your student ID or National Registration Card for validation when attending your exams.</p>

                <p>You can also access your docket online at <a href="http://sisreports.lmmu.ac.zm/">http://sisreports.lmmu.ac.zm</a>.</p>

                <p><strong>Starting now, please use your personal email '{{ $studentDetails->PrivateEmail }}' as your username and '12345678' as your password for login. If you encounter any challenges logging in, utilize the "Forgot Password" option located just below the login section. Enter '{{ $studentDetails->PrivateEmail }}' as the email address and submit. Following this, instructions will be sent to your email. You can then click on the "Reset Password" link, which will direct you to a page where you can create a new password</strong></p>

                <p><strong>Note:</strong> '{{ $studentDetails->PrivateEmail }}' is the registered email address, and it is the only one you can use to log in and access your docket or reset your password.</p>

                <p>If you encounter any issues, please visit the Academic Office or ICT Office. Your success is our priority.</p>

                <p><strong>IMPORTANT NOTICE: All students are required to be seated in the Examination Room 30 minutes before the exam. NO STUDENT WILL BE ALLOWED IN THE EXAM ROOM IF THEY ARRIVE LATE.</strong></p>

                <p>Best Regards,</p>
                <p>Registrar</p>
                <p>Levy Mwanawasa Medical University, Lusaka.</p>                           
            {{-- </b>
        </b> --}}
    </div>
</body>
</html>