# Customer Sollecito

`PUT /api/v1/client-outer-tickets/update` also accepts the existing multipart
`POST` with `_method=PUT`. No database migration or frontend payload change is
needed for this fix.

## Payload and behavior

- `ticketId`: the internal ticket ID, not the original external request ID.
- `token`: the matching ticket's email token; required and nonempty.
- `sollecito`: optional boolean (0/1, including multipart strings). When 1,
  `Urgenza` becomes **Sollecitato**. When 0, it becomes **Non urgente**.
  When omitted, urgency is unchanged. Repeated requests can switch in either direction.
  The ticket's `status` is always preserved; this endpoint does not suspend it.
- `message`: optional text appended to `tickets.description`, preceded by
  `d/m/Y H:i`. Existing description content is preserved. Blank text is ignored.
- `files[i][path]`: the uploaded binary file.
- `files[i][actionStatus]`: must be `create`.
- `files[i][name]` and `uploadPath` are not trusted for naming or routing files.

Uploads go through `UploadService` to the `uploads` disk, directly under
`tickets/{verifiedTicketId}`. File Manager can read them with
`GET /api/v1/uploads/getfiles?directory=tickets-{ticketId}`.
Each new attachment gets a unique prefix to avoid replacing same-named files.
Existing naming behavior on other upload routes is unchanged.

Up to 20 files are accepted, at most 20 MiB each (web-server/PHP limits can be
lower). Supported extensions: pdf, doc, docx, xls, xlsx, ppt, pptx, odt, ods, txt,
csv, rtf, jpg, jpeg, png, gif, tif, tiff, bmp, webp, zip, 7z, rar, p7m, eml, msg.
The effective MIME-derived storage extension must also be safe; p7m/eml/msg
retain their original extension, including when detected as generic binary.
Executable/web-content uploads are rejected. No malware scanning is added.

`Segnalazione` is unchanged. No email is sent.

Urgency options belong to `parameter_values.parameter_id = 17`. ID 91 is the
reported Non urgente option and ID 92 is the reported Sollecitato option.
Explicit `sollecito=0` selects 91 / Non urgente; `sollecito=1` selects 92 / Sollecitato.
The preferred ID is used only if active and its `parameter_value` matches the
expected name (case-insensitive, ignoring surrounding spaces). Otherwise, the
backend searches active urgency options by that name and requires one match.
Missing/ambiguous options or unusable description values return 422 without changes.
Omitting `sollecito` never resets urgency to Non urgente.

As in the existing ticket service, `tickets.urgenza` stores the selected option's
**description**, not its ID. The ticket-detail API converts this value back to the
option ID (normally 92); the list API resolves the display label `Sollecitato`.
No existing ticket statuses are bulk-reverted by this change.

## Failure handling

- Validation failures or an invalid ticket/token return 422 without changes.
- Closed tickets (status 2) return 409, rather than a misleading success.
- Soft-deleted tickets cannot be updated.
- Ticket rows are locked during updates to preserve concurrent appended messages.
- A failed upload rolls back the database transaction. Already-uploaded files
  from that request are deleted; existing attachments are not removed.
- The shared audit trait allows unauthenticated token-based updates to record
  `updated_by = null` instead of failing. Authenticated updates still record the
  user's ID.

## Verification

```sh
php vendor/phpunit/phpunit/phpunit tests/Feature/SollecitoTest.php
```

Tests use isolated SQLite tables and fake storage/mail. They exercise the actual
multipart update, file listing, and ticket-detail endpoints. The ticket-detail
test bypasses only the custom JWT middleware; token validation on the public
Sollecito endpoint is exercised normally. No request is sent to the hosted site.
