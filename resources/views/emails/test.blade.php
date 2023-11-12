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
                <p>Please review the document to ensure that all the details are accurate.</p>
                <p>As part of our examination procedure, it is a requirement that you have a form of official identification, such as your student ID or National Registration Card, to use along with this docket. This is an important step to ensure the validity of your exam docket.</p>
                <p>If you find any errors or discrepancies, we kindly request that you visit the Academic Office to have them corrected. Your academic success is our priority, and we are here to assist you with any concerns you may have.</p>
                <p>You can also access your docket by visiting our docket printing dashboard at <a href="http://sisreports.lmmu.ac.zm/">http://sisreports.lmmu.ac.zm/</a>.</p>
                <p>You can log in to the system using your student number as the username (email), which is '{{ $studentDetails->ID }}@lmmu.ac.zm', and your National Registration Card number as the password, which is '{{ $studentDetails->GovernmentID }}'.</p>
                <p>Do not forget to add the "@lmmu.ac.zm" at the end of your student number when logging in</p>
                <p>Please accept our apologies if you have already graduated or withdrawn from the university and should not be receiving this email. In such cases, please disregard this notification.</p>
                <p>Best Regards,</p>
                <p>Registrar</p>
                <p>Levy Mwanawasa Medical University,</p>
                <p>LUSAKA.</p>                             
            </b>
        </b>
    </div>
</body>
</html>