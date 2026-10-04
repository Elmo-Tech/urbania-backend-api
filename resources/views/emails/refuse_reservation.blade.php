<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appuntamento rifiutato</title>
</head>
<body style="margin:0; padding:24px; font-family:Arial, Helvetica, sans-serif; background-color:#f7f7f7; color:#1f2937;">
    <div style="max-width:640px; margin:0 auto; background-color:#ffffff; border:1px solid #e5e7eb; border-radius:12px; padding:32px;">
        <h1 style="margin:0 0 16px; font-size:24px; line-height:1.3; color:#111827;">Richiesta di appuntamento non accettata.</h1>

        <p style="margin:0 0 12px; font-size:16px; line-height:1.7;">
            Purtroppo la sua richiesta di appuntamento &egrave; stata rifiutata.
        </p>

        <p style="margin:0 0 8px; font-size:16px; line-height:1.7;">
            <strong>Numero appuntamento:</strong>
            {{ $reservation->number }}
        </p>

        @if(!empty($reservation->refuse_reason))
            <p style="margin:0; font-size:16px; line-height:1.7;">
                <strong>Motivo:</strong>
                {{ $reservation->refuse_reason }}
            </p>
        @endif
    </div>
</body>
</html>
