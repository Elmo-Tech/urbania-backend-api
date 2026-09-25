# Microsoft 365 Email Requirements

To send emails from the client’s Microsoft 365 account, we need one of these options:

## 1. SMTP (fastest)

Client provides:

- Sender mailbox email
- Mailbox password
- Sender name

Admin enables:

- SMTP AUTH
- Send As permission if needed

## 2. Microsoft Graph / OAuth

Client provides:

- Tenant ID
- Client ID
- Client Secret
- Sender mailbox
- Sender name

Admin grants `Mail.Send`.
