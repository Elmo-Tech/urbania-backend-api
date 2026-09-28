# External ticket decisions

`acceptStatus` is separate from the existing ticket processing field `status`:

| Value | Decision |
| --- | --- |
| `0` | Pending; no decision email or internal ticket creation |
| `1` | Accepted; convert to an internal ticket and send the existing acceptance email |
| `2` | Rejected; save the reason and email the external ticket's customer |

Existing `null` decisions remain pending. New model instances default to `0`.

## Reject a ticket

Authenticated request: `PUT /api/v1/outer-tickets/update`

```json
{
  "clientOuterTicketId": 42,
  "acceptStatus": 2,
  "rejectionReason": "The required supporting document is missing."
}
```

Rejection requires a nonblank text reason, up to 5,000 characters. It updates the
decision and reason without overwriting the existing customer or ticket details.
The saved ticket email is used by default. An optional `email` can correct that
address; a valid recipient is required to avoid silently skipping notification.
Rejection does not create an internal ticket or customer contact.

The field is stored as `client_outer_tickets.rejection_reason` and returned as
`rejectionReason` by both the list and detail APIs. Both also expose `acceptStatus`.
Non-rejection updates ignore a supplied `rejectionReason` and retain saved reasons.
Acceptance and normal editing retain their existing full update request fields.
Omitting `acceptStatus` or sending `null` preserves the existing decision.

The email subject is `Ticket Rejected`. It includes the external ticket number
and the saved reason, with HTML escaped and line breaks preserved. It uses the
configured Laravel mail transport (Microsoft Graph in deployment).

Emails and conversion run only when the decision changes, not on repeated saves
of the same decision. Editing a reason on an already rejected ticket saves the
new reason without resending the email. A row lock serializes concurrent updates.
Email sending remains synchronous within the existing transaction: if the mail
provider fails, the update fails and the decision/reason are rolled back together.

## List filter

`GET /api/v1/outer-tickets`

| Query | Returned decisions |
| --- | --- |
| `isProcessed=1` | Accepted (`1`) and rejected (`2`) together |
| `isProcessed=0` | Pending (`0` or legacy `null`) only |
| Omitted | Pending (`0` or legacy `null`) only |

Filtering happens before pagination, so totals and page counts describe the
filtered results. Existing `page` and `pageSize` parameters continue to work.
Invalid filter values are rejected rather than silently returning the wrong list.

## Database deployment

Run only the new migration when deploying to an existing installation:

```sh
php artisan migrate --path=database/migrations/2026_09_28_120000_add_rejection_reason_to_client_outer_tickets.php
```

It adds the nullable reason column and changes `accept_status` to a nullable
unsigned tiny integer with a default of zero on MySQL. Existing values are not
reclassified. SQLite already uses an integer representation for boolean columns.
Rollback removes the reason column but intentionally retains numeric decision
values so a rejected ticket is never coerced to an accepted boolean value.

## Verification

```sh
php vendor/phpunit/phpunit/phpunit
```

Decision tests use an isolated in-memory SQLite schema and fake mail. Graph tests
fake HTTP; no real rejection or acceptance emails are sent by the suite.
