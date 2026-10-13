# Reservation calendar decisions

## Admin reservation access

All `/api/v1/reservations` routes require a valid API bearer token plus an
action-specific permission:

| Route | Permission |
| --- | --- |
| `GET /api/v1/reservations` | `allReservation` |
| `POST /api/v1/reservations/create` | `createReservation` |
| `GET /api/v1/reservations/edit` | `editReservation` |
| `PUT /api/v1/reservations/update` | `updateReservation` |
| `DELETE /api/v1/reservations/delete` | `deleteReservation` |

Before deploying these checks, execute
`database/sql/grant_reservation_permissions.sql` on the application database,
then run `php artisan permission:cache-reset` on the application server. The SQL
creates missing permissions and assigns all five to every existing `api` role,
including `standard`, `limitata`, and `superLimitata`, as requested. It preserves
other assignments and can be rerun. Future roles require assignment or a rerun.
It does not grant `web` permissions to API users or change users' roles.

API JSON requests without authentication receive 401; authenticated users without
the action permission receive 403. Customer booking routes, token-based email
decisions, and public availability endpoints retain their existing access rules.
Frontend menu, route, and button checks must use the corresponding permissions;
a frontend restriction to admin roles will still hide this section from others.

Verify with `php vendor/phpunit/phpunit/phpunit tests/Feature/ReservationPermissionsTest.php`.
These tests execute the SQL inserts against isolated SQLite (adapting only the
transaction-start syntax); they do not execute the SQL on the live database.

## Public schedule availability

`GET /api/v1/free-schedules` and its `check-availability` endpoint read all active
schedule records for the requested client. Weekly entries (`type: fixed`) match
`day`; date-specific entries (`type: dedicated`) match `date`. Both types add
availability when they match the same day, including entries in separate records.
Slots are sorted and deduplicated before checking existing reservations.

Appointment duration and the break between slots retain their existing meaning.
The existing reservation filtering, expired incomplete reservation cleanup,
response shapes, and requested date range remain in place. Invalid legacy time
ranges or nonpositive appointment durations are skipped so they cannot break or
stall availability for valid entries. No migration or frontend payload change is
needed for this availability fix.

Regression tests (isolated SQLite, including Solarolo and Voltido schedules):

```sh
php vendor/phpunit/phpunit/phpunit tests/Feature/FreeReservationScheduleTest.php
```

## Deployment

Run against the application's populated database before serving the updated code:

```sh
php artisan migrate --path=database/migrations/2026_09_28_130000_add_reservation_id_to_events.php
```

The migration adds a nullable, unique `events.reservation_id`. It does not modify
existing events or delete reservation data. No database foreign key is added:
the existing cleanup endpoint can permanently remove reservations.

## Customer email link

`PUT /api/v1/client-reservations/control` keeps its existing payload:

```json
{"reservationId":123,"token":"token-from-email","status":0}
```

- `0`: soft-delete the linked calendar event and record the rejection.
- `2`: keep the same event, restoring it if previously soft-deleted. No new event
  is created by this endpoint.
- Repeating the same decision is safe. A valid token is still required; missing
  or invalid tokens, missing reservations, and statuses other than 0/2 return 422.
- This public endpoint does not send another email.

## Admin update

`PUT /api/v1/reservations/update` keeps its existing payload and email behavior.
Status 2 creates and links an event on first approval; subsequent approvals update
that same event (including its date/title), even after rejection. Status 0 removes
the linked event. Confirmation tokens remain stable while a confirmed reservation
is edited. Admin rejection still clears the token, as before.

Calendar changes and the reservation decision share a transaction. Reservation
row locks serialize customer decisions and admin updates. The unique index prevents
more than one linked event per reservation, including soft-deleted events.

## Legacy events

Before changing reservation data, the service attempts to link an existing event
using the old generated title, description, exact start/end time, client,
ticket client, blank URL, null group, and all-day flag. The event must not predate
the reservation. Exactly one candidate must exist, with no competing reservation
that generates the same title at that time with the same message.

This is a conservative compatibility match, not historical proof of ownership:
legacy data does not contain a reservation reference. Manually edited titles or
dates, renamed clients, or existing duplicates can require manual reconciliation.

Ambiguous matches, already-claimed candidates, and missing matches for previously
confirmed/token-bearing reservations return **409**, rolling back the decision.
No events are deleted or created in those cases. Surface the API's `message` in
the frontend rather than showing a success notification.

For a 409, an administrator must inspect the reservation and calendar records,
identify the original event, and explicitly set its `reservation_id` to the
reservation ID. Review duplicate events separately; they are not bulk-deleted.
After reconciliation, retry the original request. Do not link by date alone.

The generic reservation delete/expired-draft cleanup endpoints are unchanged;
this fix targets customer decisions and the admin update endpoint.

## Verification

```sh
php vendor/phpunit/phpunit/phpunit tests/Feature/ReservationCalendarTest.php
```

Tests use an isolated SQLite database and fake mail delivery, never the production
database or a real recipient. Live MySQL migration and frontend testing are still
required on the deployment environment.
