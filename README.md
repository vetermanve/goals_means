# Goals and Means
Сайт для учета расходов и доходов. 

Написан на Verse Framework (php)

# Фичи
- [x] Аваторизация
- [x] Заведение аккаунта друга
- [x] Планы
- [ ] Счета
- [ ] Списки хотелок
- [ ] Грппы
- [ ] Бюджет
- [ ] Долги
- [ ] Трекинг почасовой работы
- [ ] Примерка хотелок на бюджет
- [ ] Telegram bot
- [ ] Объединение бюджетов

# Объекты
## Пользователь / User
Состояние: Готово.

```
id 
name
email
is_registered // todo
```

## Бюджет / Budget

```
id 
name
descripition
```


## Счет / Balance

```
id
name
name_official 
description
type - Наличные, Карта, Счет, Между Людьми 
balance_type - Текущий, Частный
budget_id - Принадлежность к бюджету
amount - текущее состояние 
```

## Цель / Goal

```
id
name - имя цели
description - описание цели
amount - сумма цели
budget_id - принадлежность к бюджету
created_at - когда создана
creator_id - кто cоздал
for_users - массив пользователей, для кого
status - завершена ли цель.
priority - срочность
time_accurancy - важность срока 
severity - обязательность
```

## Транзакция / Transaction

```
id
description - описание транзакции
belongs_type - к чему принадлежит (цель, отношение)
belongs_id - идентификатор сущности к которой принадлежит
type - Текущий, Частный
balance_from - Балнс с которого
balance_to - Баланс на который
send_confirmed - Отправка подтверждена
send_document - Документ подтвержадющий отпарвку
send_date - Дата отправки 
receive_confirmed - Получение подтвержено
receive_document - Докумнет подтвержающий получение
receive_date - Дата подтверждеия получения
created_date - Дата создания транзакции
status - состояние (создана, отправлена, подтверждена)
suggestion_id - в рамках какого плана.
amount - размер
```

## План / Suggestion
Состояние: Готово
```
id
name - описание предложения
date - планируемая дата
amount - сколько
belongs_type - к чему принадлежит (цель, отношение)
belongs_id - идентификатор сущности к которой принадлежит
```

## Отношение / Relation
Состояние: Готово

```
id
owner_user_id - кто создал отношение
related_user_id - к кому отношение 
description - описание
// buget_id - Принадлежность к бюджету // todo 
```

