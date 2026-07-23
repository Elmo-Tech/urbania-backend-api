<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Confirmation</title>
</head>
<body style="margin:0; padding:24px; font-family:Arial, Helvetica, sans-serif; background-color:#ffffff; color:#1f2937;">
    <div style="max-width:720px; margin:0 auto;">
        <h1 style="margin:0 0 28px; font-size:28px; line-height:1.2; color:#111827;">Reservation Confirmation</h1>

        <p style="margin:0 0 16px; font-size:16px; line-height:1.7;">
            Your reservation has been confirmed with the following details:
        </p>

        <ul style="margin:0 0 28px 28px; padding:0; font-size:16px; line-height:1.8;">
            <li>
                Date:
                {{ \Carbon\Carbon::parse($reservation->date)->format('d/m/Y H:i') }}
            </li>
        </ul>

        <p style="margin:0 0 28px; font-size:16px; line-height:1.7;">
            <a href="{{ $controlUrl }}" style="color:#2563eb; text-decoration:underline;">Confirm Reservation</a>
        </p>

        <p style="margin:0 0 24px; font-size:16px; line-height:1.7;">
            Thank you for choosing us!
        </p>

        <p style="margin:0; font-size:13px; line-height:1.7; color:#6b7280;">
            If the link does not open, use this URL:
            <a href="{{ $controlUrl }}" style="color:#2563eb; word-break:break-all;">{{ $controlUrl }}</a>
        </p>
    </div>
</body>
</html>
