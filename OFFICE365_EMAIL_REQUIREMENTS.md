# Office 365 Email Sending Requirements

This note explains what the client needs to provide if we want the system to send emails through their Microsoft 365 / Office 365 account instead of Google SMTP.

## Recommended options

### Option 1: Fast setup using SMTP

This is the quickest option and requires the fewest code changes.

The client should provide:

- A dedicated mailbox for sending, such as `noreply@company.com` or `support@company.com`
- The full email address for that mailbox
- The mailbox password
- The sender name they want recipients to see
- One or two test recipient email addresses

The client's Microsoft 365 administrator must also:

- Enable Authenticated SMTP for that mailbox
- Make sure security policies do not block SMTP AUTH for this account
- Grant `Send As` permission if the system must send from a different mailbox or shared mailbox

Typical SMTP settings:

- Host: `smtp.office365.com`
- Port: `587`
- Encryption: `TLS / STARTTLS`

### Option 2: Better long-term setup using OAuth / Microsoft Graph

This is the more modern and recommended solution.

The client should provide:

- Tenant ID
- Client ID
- Client Secret or certificate
- Admin consent for `Mail.Send`
- The mailbox that the system should send from
- The sender name they want recipients to see
- One or two test recipient email addresses

This option is more secure and better for long-term support.

## Recommendation

If the goal is to go live quickly, use the SMTP option first.

If the goal is a more secure and future-proof setup, use Microsoft Graph / OAuth.
