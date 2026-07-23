<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Accepted</title>
</head>
<body style="margin:0; padding:24px; font-family:Arial, Helvetica, sans-serif; background-color:#f7f7f7; color:#1f2937;">
    <div style="max-width:640px; margin:0 auto; background-color:#ffffff; border:1px solid #e5e7eb; border-radius:12px; padding:32px;">
        <h1 style="margin:0 0 16px; font-size:24px; line-height:1.3; color:#111827;">Ticket accepted successfully.</h1>

        <p style="margin:0 0 12px; font-size:16px; line-height:1.7;">
            Your ticket has been accepted and created in the system.
        </p>

        <p style="margin:0 0 8px; font-size:16px; line-height:1.7;">
            <strong>Ticket Number:</strong>
            {{ $ticket->ticket_number }}
        </p>

        <p style="margin:0 0 24px; font-size:16px; line-height:1.7;">
            <strong>Created At:</strong>
            {{ optional($ticket->created_at)->format('d/m/Y H:i') }}
        </p>

        <p style="margin:0 0 24px;">
            <a href="{{ $editUrl }}" style="display:inline-block; padding:12px 18px; background-color:#111827; color:#ffffff; text-decoration:none; border-radius:8px; font-size:15px;">
                Edit Ticket
            </a>
        </p>

        <p style="margin:0; font-size:14px; line-height:1.7; color:#6b7280;">
            If the button does not work, open this link:
            <a href="{{ $editUrl }}" style="color:#2563eb; word-break:break-all;">{{ $editUrl }}</a>
        </p>
    </div>
</body>
</html>
