<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Rejected</title>
</head>
<body style="margin:0; padding:24px; font-family:Arial, Helvetica, sans-serif; background-color:#f7f7f7; color:#1f2937;">
    <div style="max-width:640px; margin:0 auto; background-color:#ffffff; border:1px solid #e5e7eb; border-radius:8px; padding:32px;">
        <h1 style="margin:0 0 16px; font-size:24px; line-height:1.3; color:#111827;">Your ticket has been rejected.</h1>
        <p style="font-size:16px; line-height:1.7;">
            <strong>Ticket Number:</strong> {{ $ticket->number }}
        </p>
        <p style="font-size:16px; line-height:1.7;"><strong>Reason for rejection:</strong></p>
        <div style="font-size:16px; line-height:1.7; white-space:pre-wrap; overflow-wrap:anywhere;">{{ $ticket->rejection_reason }}</div>
    </div>
</body>
</html>
