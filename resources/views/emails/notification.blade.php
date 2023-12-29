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

                <p>We are pleased to inform you that you can also access your results at <a href="http://sisreports.lmmu.ac.zm/">
                    http://sisreports.lmmu.ac.zm</a>.</p>

                <p>Login using '{{ $studentDetails->ID }}@lmmu.ac.zm' as your username and your National Registration Card number 
                    '{{ $studentDetails->GovernmentID }}' as your password. Don't forget to add "@lmmu.ac.zm" to your student number.</p>

                
                <p>Best Regards,</p>
                <p>Registrar</p>
                <p>Levy Mwanawasa Medical University, LUSAKA.</p>                             
            </b>
        </b>
    </div>
</body>
</html>