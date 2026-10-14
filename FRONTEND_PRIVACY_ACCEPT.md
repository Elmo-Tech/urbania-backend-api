# Privacy acceptance — Frontend API contract

## الاسم والقيم

- اسم الحقل في كل request وresponse: `privacyAccept`.
- القيمة في response رقم: `0` = غير موافق/لا توجد موافقة مسجلة، `1` = موافق.
- أرسل `1` عندما يوافق العميل بالفعل، وليس تلقائيًا عند فتح النموذج أو إرسال الطلب.
- في JSON أرسل `0` أو `1`. في FormData أرسل النص `"0"` أو `"1"`.
- في الإنشاء: لو الحقل غير مبعوت، القيمة الافتراضية `0`، للحفاظ على توافق العملاء الحاليين.
- في التعديل: لو الحقل غير مبعوت، تظل القيمة الحالية. إرسال `0` يحفظ `0` حتى لو كانت القيمة `1`.
- `null` والقيمة الفارغة والأرقام غير `0` و`1` غير مقبولة.
- الحقل مستقل عن `status` و`acceptStatus`؛ الموافقة على الخصوصية لا تعني قبول التذكرة أو تأكيد الحجز.
- قاعدة البيانات تستخدم `privacy_accept` داخليًا فقط. لا ترسل هذا الاسم في الـ API.

كل المسارات التالية تبدأ بـ `/api/v1`. البيانات القديمة ترجع `privacyAccept: 0` بعد تشغيل migration، ولا توجد موافقة مفترضة بأثر رجعي.

مسارات الـ AI الثلاثة خارج نطاق هذا التغيير وتحتفظ بالـ requests والـ responses السابقة.

## Requests التي تقبل الحقل

أضف الحقل إلى الـ payload الحالي، مع استمرار إرسال الحقول المطلوبة الموجودة من قبل. هذه الإضافة لا تحول update إلى PATCH ولا تلغي متطلبات الحقول الأخرى.

| Method | Endpoint | المعرّف/الاستخدام | اسم الحقل المرسل |
|---|---|---|---|
| POST | `/client-outer-tickets/create` | إنشاء تذكرة خارجية، يدعم FormData والمرفقات الحالية | `privacyAccept` |
| PUT | `/outer-tickets/update` | تعديل/قبول/رفض الطلب الخارجي، `clientOuterTicketId` | `privacyAccept` |
| PUT | `/client-outer-tickets/update` | متابعة العميل لتذكرة داخلية بعد القبول، `ticketId` + `token` | `privacyAccept` |
| POST | `/tickets/create` | إنشاء تذكرة داخلية | `privacyAccept` |
| PUT | `/tickets/update` | تعديل تذكرة داخلية، `ticketId` | `privacyAccept` |
| POST | `/client-reservations/create` | إنشاء حجز خارجي | `privacyAccept` |
| PUT | `/client-reservations/update` | استكمال/تعديل الحجز الخارجي، `reservationId` | `privacyAccept` |
| POST | `/reservations/create` | إنشاء حجز من لوحة الإدارة | `privacyAccept` |
| PUT | `/reservations/update` | تعديل/تأكيد/رفض الحجز، `reservationId` | `privacyAccept` |
| POST | `/events/create` | إنشاء موعد داخلي في الكالندر | `privacyAccept` |
| PUT | `/events/update` | تعديل موعد داخلي، `eventId` | `privacyAccept` |

مثال إضافة الحقل لأي payload إنشاء أو تعديل:

```javascript
const payload = { ...existingFormData, privacyAccept: privacyChecked ? 1 : 0 };
```

ومع رفع الملفات:

```javascript
formData.set('privacyAccept', privacyChecked ? '1' : '0');
```

في شاشة التعديل، اقرأ الحالة من الـ edit response قبل تهيئة الـ checkbox. لا تضف قيمة `0` افتراضية إلى تحديث لا يخص الموافقة؛ احذف الحقل للحفاظ على القيمة الحالية.

## Responses الخاصة بالإنشاء والتعديل

كل responses الناجحة التالية HTTP `200`. الحقول القديمة والـ messages الحالية محفوظة؛ الحقل الجديد إضافة لها. القيم والمعرّفات في الأمثلة توضيحية.

| Request | شكل response بعد الإضافة |
|---|---|
| `POST /client-outer-tickets/create` | `{"message":"ticket has been created !","clientOuterTicketId":123,"privacyAccept":1}` |
| `PUT /outer-tickets/update` | `{"message":"clientOuterTicket has been updated !","privacyAccept":1}` |
| `PUT /client-outer-tickets/update` | `{"message":"ticket has been updated !","status":1,"privacyAccept":1}` |
| `POST /tickets/create` | `{"message":"ticket has been created!","privacyAccept":1}` |
| `PUT /tickets/update` | `{"message":"ticket main info has been updated!","privacyAccept":1}` |
| `POST /client-reservations/create` | `{"message":"schedule has been created !","data":{...existingReservationFields,"privacyAccept":1}}` |
| `PUT /client-reservations/update` | `{"message":"schedule has been updated !","privacyAccept":1}` |
| `POST /reservations/create` | `{"message":"schedule has been created !","privacyAccept":1}` |
| `PUT /reservations/update` | `{"message":"reservation has been updated !","privacyAccept":1}` |
| `POST /events/create` | `{"message":"event has been created!","privacyAccept":1}` |
| `PUT /events/update` | `{"message":"event has been updated!","privacyAccept":1}` |
| `PUT /client-reservations/control` | `{"message":"schedule has been updated !","privacyAccept":1}` |

`...existingReservationFields` توضيح لاستمرار باقي حقول الحجز وليس نصًا يُرسل أو JSON حرفيًا.

`/client-reservations/control` يعرض القيمة المحفوظة فقط؛ الطلب يظل `{reservationId, token, status}` لتأكيد/إلغاء الموعد. تعديل الخصوصية يكون من endpoints التعديل في الجدول السابق. الحذف لا يرجع الحقل.

## GET: الجداول والتفاصيل المستخدمة في التعديل

لا يحتاج GET إرسال الحقل. كل row يحتوي القيمة الرقمية الحالية، مع بقاء الـ pagination والحقول الموجودة كما هي.

| GET endpoint | مكان الحقل في response |
|---|---|
| `/outer-tickets` | `clientOuterTickets[i].privacyAccept` |
| `/outer-tickets/edit?clientOuterTicketId=123` | `privacyAccept` في جذر الكائن |
| `/tickets` | `ticketPage.tickets[i].privacyAccept` |
| `/tickets/edit?ticketId=456` | `privacyAccept` في جذر الكائن |
| `/reservations` | `reservations[i].privacyAccept` |
| `/reservations/edit?reservationId=789` | `privacyAccept` في جذر الكائن |
| `/events` | `[i].privacyAccept` — الاستجابة array مباشرة |
| `/events/edit?eventId=321` | `privacyAccept` في جذر الكائن |

أمثلة مقتطفة توضح مكان الحقل، وليست بديلًا عن باقي حقول الاستجابة:

```json
{
  "clientOuterTickets": [
    { "outerTicketId": 123, "privacyAccept": 1 }
  ]
}
```

```json
{
  "ticketPage": {
    "tickets": [
      { "ticketId": 456, "privacyAccept": 1 }
    ]
  }
}
```

```json
{
  "reservations": [
    { "reservationId": 789, "privacyAccept": 1 }
  ]
}
```

```json
[
  { "eventId": 321, "privacyAccept": 1 }
]
```

في الـ edit ترجع مثلًا `{ "reservationId": 789, "privacyAccept": 1 }` مع باقي تفاصيل الحجز. بالنسبة للتذكرة الخارجية، اسم المعرّف في القائمة `outerTicketId`، واسم المعرّف في edit/update هو `clientOuterTicketId` كما هو حاليًا.

استخدم `/reservations/edit` في لوحة الإدارة لجلب تفاصيل الحجز. الراوت القديم `/client-reservations/edit` معلن بالفعل لكنه لا يملك handler في الكنترولر الحالي؛ لم تتم إضافة endpoint قراءة عام ضمن هذا التغيير.

## انتقال القيمة بعد القبول

1. **التذكرة الخارجية:** عند انتقال `acceptStatus` إلى `1` من `/outer-tickets/update`، يتم نسخ القيمة الحالية من `client_outer_tickets.privacy_accept` إلى `tickets.privacy_accept`. إرسال القيمة في نفس طلب القبول يحفظها أولًا ثم ينقلها. بعدها يعرضها جدول وتفاصيل التذاكر الداخلية.
2. **الحجز:** عند تأكيد الحجز بـ `status: 2` من `/reservations/update`، تنقل القيمة من `reservations.privacy_accept` إلى الموعد الذي يتم إنشاؤه/تحديثه في `events.privacy_accept`. الربط الحالي عن طريق `events.reservation_id`.
3. يمكن تعديل القيمة في الموديول الداخلي باستخدام `/tickets/update` أو `/events/update`.
4. النسخ يتم في مسار القبول/تأكيد الحجز؛ لا توجد مزامنة عكسية من التذكرة الداخلية إلى الطلب الخارجي أو من الموعد إلى الحجز. إعادة تأكيد الحجز عبر الإدارة تستخدم قيمة الحجز الحالية عند تحديث الموعد، مثل بقية بياناته.

## Validation

لأي endpoint يستخدم FormRequest من الجدول، القيمة غير الصحيحة ترجع كود `401` وفق أسلوب التحقق الحالي، مثل:

```json
{
  "message": {
    "privacyAccept": ["Validation error message"]
  }
}
```

رسالة الخطأ الفعلية حسب لغة التطبيق ونوع الخطأ. مسار متابعة التذكرة `/client-outer-tickets/update` يستخدم `422` وشكل Laravel المعتاد `message` + `errors.privacyAccept`. فشل التحقق لا يحفظ الحقل أو ينفذ القبول.

## النشر

التغيير يحتاج migration قبل استخدام الكود الجديد على قاعدة البيانات المستهدفة:

```bash
php artisan migrate --path=database/migrations/2026_10_14_120000_add_privacy_accept_to_tickets_and_reservations.php
```

الجداول الأربعة: `client_outer_tickets`, `tickets`, `reservations`, `events`. العمود `privacy_accept TINYINT NOT NULL DEFAULT 0`. لا يحتاج تغيير permissions أو أسماء مفاتيح الـ sidebar.
