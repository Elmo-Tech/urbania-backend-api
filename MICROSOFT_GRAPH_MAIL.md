# Microsoft Graph mail

All active email routes use Laravel Mail with the `microsoft_graph` transport:

| Route | Trigger |
| --- | --- |
| `POST /api/v1/client-email/send` | Client message with optional attachments |
| `POST /api/v1/email/send` | General HTML message with optional attachments |
| `PUT /api/v1/outer-tickets/update` | Accepted ticket, if its client contact has an email |
| `PUT /api/v1/reservations/update` | Reservation confirmation or refusal |

External-ticket creation acknowledgement remains disabled, as before. Switching
the transport does not enable those commented-out send calls.

## Server configuration

Set these values in the server's existing `.env` (do not replace its database,
application key, or other configuration):

```dotenv
MAIL_MAILER=microsoft_graph
MAIL_FROM_ADDRESS=tributi@urbaniaweb.it
MAIL_FROM_NAME="Tributi Urbania"
MICROSOFT_GRAPH_TENANT_ID=<tenant-id>
MICROSOFT_GRAPH_CLIENT_ID=<application-client-id>
MICROSOFT_GRAPH_CLIENT_SECRET="<client-secret-value>"
```

Keep secrets out of Git, logs and frontend requests. These mail credentials are
separate from the existing Microsoft Calendar configuration. `MAIL_HOST`,
`MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD` and `MAIL_ENCRYPTION` are unused by
this transport. An existing `MAIL_MAILER=smtp` must be changed explicitly.

After updating the server environment:

```sh
php artisan config:cache
php artisan queue:restart
```

Restart any other long-running application workers in use. No schema migration
or Microsoft SDK dependency is required.

The Entra application needs Microsoft Graph **Application** permission
`Mail.Send`, with administrator consent, and access to the sender's Exchange
Online mailbox. Configure the mailbox display name as `Tributi Urbania` as well;
Exchange and recipient clients may use its directory display name.

## Sending behavior

- Uses client credentials with scope `https://graph.microsoft.com/.default`.
- Caches access tokens until 60 seconds before expiry; the cache key changes
  when credentials change. Restrict access to the configured cache store.
- Sends to `/v1.0/users/{MAIL_FROM_ADDRESS}/sendMail` and saves Sent Items.
- Preserves HTML/text content, recipients, CC, BCC, reply-to and attachments.
- Each attachment must be smaller than 3 MiB, and the complete JSON request
  after base64 encoding must be smaller than 4 MiB. Oversize messages fail
  explicitly before any token or send request. Large-attachment upload sessions
  are not implemented; those require draft creation and additional mailbox
  permissions (`Mail.ReadWrite`).
- HTTP 202 means Microsoft accepted the request, not confirmed inbox delivery.
- A 401 refreshes the token and retries once. Permission failures, throttling,
  server errors and timeouts are not automatically retried, avoiding duplicate
  sends after uncertain outcomes. Check Sent Items before manually retrying a
  timeout. Provider response bodies, tokens and secrets are not exposed in errors.
- Sending remains synchronous. Existing reservation/ticket transaction behavior
  is preserved. General email failures return JSON with HTTP 502; client email
  failures retain the existing HTTP 500 response.

## Verification

```sh
php vendor/phpunit/phpunit/phpunit --filter MicrosoftGraphMailTest
```

Tests fake HTTP and use dummy credentials. They do not contact Microsoft or send
emails. Verify production permissions and end-to-end delivery using an explicitly
chosen test recipient before switching live traffic.

This repository's lock file contains `lcobucci/clock` 2.3.0, whose PHP constraint
ends at 8.2. On a PHP 8.3 development machine, dependency installation for local
verification can use `composer install --ignore-platform-req=php+ --no-scripts`.
This does not change the lock file and is not a production compatibility fix.

References:
- https://learn.microsoft.com/en-us/graph/api/user-sendmail
- https://learn.microsoft.com/en-us/entra/identity-platform/v2-oauth2-client-creds-grant-flow
- https://learn.microsoft.com/en-us/graph/outlook-large-attachments
