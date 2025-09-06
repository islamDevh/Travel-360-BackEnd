<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Verify Your Email</title>
</head>

<body style="font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px;">
    <div
        style="max-width: 600px; margin: auto; background: #fff; padding: 30px; border-radius: 10px; text-align: center;">
        <h2 style="color: #333;">Welcome to {{ config('app.name') }}!</h2>
        <p style="color: #555; font-size: 16px;">Your email verification code is:</p>

        <div style="font-size: 30px; font-weight: bold; color: #28a745; margin: 20px 0;">
            {{ $otp }}
        </div>

        <p style="color: #555; font-size: 16px;">Please enter this code in your app to verify your email address.</p>

        <p style="margin-top: 20px; color: #888; font-size: 14px;">Thanks,<br>{{ config('app.name') }} Team</p>
    </div>
</body>

</html>
