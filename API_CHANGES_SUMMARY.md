# ملخص تغييرات الـAPI

## التذاكر الخارجية

| الراوت | الـkey | التغيير |
| --- | --- | --- |
| `PUT /api/v1/outer-tickets/update` | `acceptStatus` | موجود أصلًا، أُضيفت قيمة الرفض `2` |
| `PUT /api/v1/outer-tickets/update` | `rejectionReason` | جديد، مطلوب عند `acceptStatus = 2` |
| `GET /api/v1/outer-tickets` | `acceptStatus` | بدل `isProcessed`: فاضي أو غير موجود = الكل؛ يقبل `0` أو `1` أو `2` أو قيمًا مفصولة بفواصل مثل `0,1` |
| `GET /api/v1/outer-tickets` | `acceptStatus`, `rejectionReason` | حقول مضافة لرد القائمة |
| `GET /api/v1/outer-tickets/edit?clientOuterTicketId={id}` | `rejectionReason` | حقل جديد في رد التفاصيل، و`acceptStatus` موجود أصلًا |

### قيم `acceptStatus`

| القيمة | قبل | بعد |
| --- | --- | --- |
| `0` أو `null` | غير مقبول بعد | معلق |
| `1` | قبول وتحويل لتيكت داخلي | كما هي |
| `2` | لا يوجد مسار رفض مخصص | رفض مع حفظ السبب وإيميل للعميل |

`acceptStatus` منفصل عن `status` الخاص بالتيكت.

في `PUT /api/v1/outer-tickets/update`: إرسال `status` لم يعد مطلوبًا. عند القبول `acceptStatus = 1` يبدأ التيكت الداخلي الجديد تلقائيًا بـ`status = 1` (نشط)، بصرف النظر عن حالة الطلب الخارجي. غياب `status` يحتفظ بحالة الطلب الخارجي الحالية.

فلتر القائمة: `0` المعلق بما فيه `null` القديم، `1` المقبول، `2` المرفوض. مثال: `GET /api/v1/outer-tickets?acceptStatus=0,1`.
القيم المتعددة تخص راوت القائمة فقط؛ راوت التحديث ما زال يقبل قيمة واحدة.

## المواعيد

الراوتس: `PUT /api/v1/client-reservations/control` و`PUT /api/v1/reservations/update`.

**لا keys جديدة، وأرقام الحالات لم تتغير.**

| `status` | قبل | بعد |
| --- | --- | --- |
| `0` | رفض مع بقاء الحدث في التقويم | رفض وحذف الحدث المرتبط من التقويم |
| `2` | قبول؛ تحديث الأدمن كان ينشئ حدثًا جديدًا كل مرة | قبول مع الحفاظ على نفس الحدث دون تكرار |

## Sollecito

الراوت: `PUT /api/v1/client-outer-tickets/update`.

**لا keys جديدة؛ `sollecito` موجود أصلًا.**

| الحقل | قبل | بعد |
| --- | --- | --- |
| `status` | المسار يغيّره إلى `1` | يظل كما هو؛ لا يتحول إلى `3` |
| `urgenza` عند `sollecito = 1` | لا يتغير | يصبح `Sollecitato`، عادة ID `92` بدل `Non urgente` ID `91` |
| `urgenza` عند `sollecito = 0` | لا يتغير | يصبح `Non urgente`، عادة ID `91` |
| عدم إرسال `sollecito` | لا توجد معالجة مستقلة | الأولوية تظل كما هي |

يمكن تكرار إرسال `0` أو `1` والتبديل بينهما. يُتحقق من اسم الخيار `91` أو `92`، وإن لم يتطابق يُبحث بالاسم المطلوب في `parameter_values.parameter_value`.

البريد والمرفقات: لا keys جديدة للفرونت ولا تغيير في الحالات.
