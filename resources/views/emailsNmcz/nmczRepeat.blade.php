<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><b>NMCZ REPEAT COURSES</b></title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; font-size: 16px; margin: 20px;">
        
        <p>Dear {{ $studentDetails->FirstName }} {{ $studentDetails->Surname }} ( {{ $studentId }} ),</p>
        <p>We're excited to inform you that registration for NMCZ courses is now open on SISReports.</p>
        <p>To register your courses, please visit SISReports at <a href="https://sisreports.lmmu.ac.zm/">https://sisreports.lmmu.ac.zm/</a>.</p>
        <p>Here's what you need to know to complete your registration:</p>
        <ol>
            <li>Clear Previous Balances: Before registering, make sure you've cleared any previous balances. It's essential to settle all outstanding fees to proceed with the registration process seamlessly.</li>
            <li>Partial Payment Requirement: You are required to have paid at least 25% of the total invoice for the courses you are scheduled to sit for.</li>
            <li>Login Credentials: Use the following credentials to log in:
            <ul>
                <li>Username: <strong>{{ $studentLocalDetails->email }}</strong></li>
                <li>Password: <strong>12345678 (if you have not updated it yet)</strong></li>
                <li><strong>IF YOU HAVE FORGOTTEN YOUR PASSWORD, PLEASE USE THE "FORGOT PASSWORD" OPTION BELOW THE LOGIN SECTION TO RESET YOUR PASSWORD</strong></li>
            </ul>
            </li>
        </ol>
        <p>Should you encounter any challenges during the registration process, our ICT Office is ready to assist you. Feel free to reach out to them for any technical support needed.</p>
        <p>We understand that circumstances may change, and if you have already graduated or withdrawn from the university, please disregard this email.</p>
        <p>Best Regards,</p>
        <p>Registrar
        <br>Levy Mwanawasa Medical University, LUSAKA</p>                            

    </div>
</body>
</html>