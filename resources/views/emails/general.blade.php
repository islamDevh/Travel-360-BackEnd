<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
</head>

<body style="font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: #fff; padding: 30px; border-radius: 10px; text-align: center;">

        <h2 style="color: #333;">{{ $title }}</h2>

        <div style="color: #555; font-size: 16px; margin: 20px 0;">
            {!! $data !!}
        </div>

        <p style="margin-top: 30px; color: #888; font-size: 14px;">Thanks,<br>{{ config('app.name') }} Team</p>
    </div>
</body>

</html>
