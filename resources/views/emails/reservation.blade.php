<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conferma appuntamento</title>
</head>
<body style="margin:0; padding:24px; font-family:Arial, Helvetica, sans-serif; background-color:#ffffff; color:#1f2937;">
    <div style="max-width:720px; margin:0 auto;">
        <h1 style="margin:0 0 28px; font-size:28px; line-height:1.2; color:#111827;">Conferma appuntamento</h1>

        <p style="margin:0 0 16px; font-size:16px; line-height:1.7;">
            Il suo appuntamento &egrave; stato confermato con i seguenti dettagli:
        </p>

        <ul style="margin:0 0 28px 28px; padding:0; font-size:16px; line-height:1.8;">
            <li>
                Data:
                {{ \Carbon\Carbon::parse($reservation->date)->format('d/m/Y H:i') }}
            </li>
        </ul>

        <p style="margin:0 0 28px; font-size:16px; line-height:1.7;">
            <a href="{{ $controlUrl }}" style="color:#2563eb; text-decoration:underline;">Conferma appuntamento</a>
        </p>

        <p style="margin:0 0 24px; font-size:16px; line-height:1.7;">
            Grazie per averci scelto!
        </p>

    </div>
</body>
</html>
