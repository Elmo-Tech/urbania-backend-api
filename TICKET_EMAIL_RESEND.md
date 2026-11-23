# إعادة إرسال إيميل قبول التذكرة الداخلية

واجهة جديدة، ترسل إلى إيميل جهة الاتصال الحالي في التذكرة الداخلية.

```http
POST /api/v1/tickets/reconfirm-email
Authorization: Bearer <token>
Accept: application/json
Content-Type: application/json
```

الصلاحية المطلوبة: `updateTicket`.

## الطلب

```json
{
  "ticketId": 42
}
```

`ticketId` معرّف التذكرة الداخلية. الإيميل والعنوان والمحتوى لا تُرسل في الطلب.

## الرد الناجح — 200

```json
{
  "message": "Email reinviata correttamente."
}
```

## الأخطاء

| HTTP | السبب |
| --- | --- |
| 401 | تسجيل الدخول غير صالح |
| 403 | الصلاحية غير متاحة |
| 404 | التذكرة غير موجودة أو محذوفة |
| 409 | التذكرة بدون Token صالح للإيميل |
| 422 | معرّف التذكرة أو إيميل جهة الاتصال غير صالح |
| 429 | إرسال جارٍ أو محاولة خلال آخر دقيقة |
| 502 | فشل إرسال البريد؛ التسليم غير مؤكد |

مثال إيميل غير صالح — 422:

```json
{
  "message": "Il contatto del ticket non ha un indirizzo email valido.",
  "code": "ticket_email_invalid"
}
```

مثال تكرار الإرسال — 429، مع ترويسة `Retry-After`:

```json
{
  "message": "Reinvio gia richiesto. Attendere prima di riprovare.",
  "code": "ticket_email_throttled",
  "retryAfter": 60
}
```

على الفرونت إضافة إجراء مستقل `Reinvia Email` مع تأكيد قبل الإرسال، وعرض رسالة النتيجة.
