# Client export

`GET /api/v1/clients/export` uses `ClientController::export` and the same `auth:api` middleware as the client list. Send the usual bearer token.

## Query parameters

| Parameter | Values / behavior |
| --- | --- |
| `exportType` | Required: `excel`, `csv`, or `pdf`. `excel` downloads an `.xlsx` file. |
| `clientId` | Optional client ID. Empty or `0` means all clients, matching the list. |
| `tradeRegister` | Optional partial match on P. iva, matching the list. |
| `cf` | Optional partial match on the client's fiscal code. |
| `peopleNumber` | Optional comparison, e.g. `>=|1000`. Operators: `=`, `!=`, `<>`, `>`, `>=`, `<`, `<=`. The list's legacy single-digit fallback is also supported. |

Filters are combined with AND and share the same query builder as `GET /api/v1/clients`. Deleted clients are excluded. Empty filters are ignored. `page` and `pageSize` do not limit the export: every matching client is exported, ordered by ID.

Example, with the population comparison URL-encoded:

```http
GET /api/v1/clients/export?exportType=excel&peopleNumber=%3E%3D%7C1000
Authorization: Bearer <token>
```

## Download response

The response is the file's binary content, with `Content-Disposition: attachment`, the appropriate `Content-Type`, and `Cache-Control: private, no-store, max-age=0`. It is not a JSON response or a download URL. No export file is stored on the server or recorded in the database.

The filename is `Clienti.xlsx`, `Clienti.csv`, or `Clienti.pdf`. All formats contain these columns:

| Column | Client field |
| --- | --- |
| Ragione sociale | `company_name` |
| Name Acronym | `name_acronym` |
| Numero Abitanti | `people_number` |
| P. iva | `trade_register` |

Excel stores P. iva as text to preserve leading zeros. CSV includes a UTF-8 BOM and preserves the original digits; spreadsheet applications may need the P. iva column imported as text to prevent automatic numeric conversion. Text that could be interpreted as a CSV formula is prefixed with an apostrophe. PDF uses an embedded Unicode font and repeats column headings on subsequent pages.

No matches still returns a valid file containing the column headings. Missing or unsupported `exportType` and malformed filters return JSON with HTTP `422` and field errors.

For Axios, use the existing authenticated API client with `responseType: 'blob'`, pass the page filters in `params`, and save the returned Blob through a browser download link. `Content-Disposition` is exposed through CORS so the frontend can read the filename. JSON validation errors also arrive as a Blob when using that response type.

## Deployment and verification

Run `composer install` on the application's supported PHP runtime to install the locked `tecnickcom/tcpdf` dependency. The Excel writer uses the project's existing PhpSpreadsheet dependency directly, without Laravel Excel's temporary-file download workflow.

```sh
php vendor/bin/phpunit tests/Feature/ClientExportTest.php
```

The tests use an isolated in-memory SQLite database and cover authentication, validation, shared filters, pagination independence, more than one database chunk, download headers, empty results, Unicode, leading zeros, and spreadsheet formula handling.
