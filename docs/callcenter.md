# Callcenter rules

**Queue Statuses**

 | Canceled | Pitch Miss | No Pitch | Sold | Query Extras
--- | --- | --- | --- | --- | --- |
**Follow up** | No | No | No | No | now >= call back time
**Reset** | True | No | No | No | now >= call back time
**Rehash** | No | True | No | No | now >= call back time
**Confirm** | No | No | No | No | (now - 3hrs) >= (scheduled time OR call back time if exists)
**Unresulted** | No | No | No | No | (now) >= (scheduled time + EOD OR call back time if exists)

Upon dealing with a call in the call center the rep should "complete" the call
by setting one of the following:

-   Canceled | will prompt reasons why. available anytime.
-   Schedule | will prompt a date/time to schedule. available in follow up, rehash, reset.
-   Set call back time | available when in confirming, follow up, reset, rehash
-   TCPA | available anytime
-   Wrong Number | available anytime
-   No Answer | available anytime
-   Left Message | available anytime
-   Busy Signal | available anytime

## Appointment Entity - PreUpdate/PrePersist rules

Where to check: [Appointment Entity - PreUpdate/PrePersist](http://dev.lev-interactive.com/one-day-roofing/crm-core-backend/blob/master/src/Lev/CRMBundle/Entity/Appointment.php#L605)

**Office consistency**

~~~bash
`office` = `customer.office`
~~~

**Default status = pending**

Before all tests, if no `status` is set, so sets to `pending`.

~~~bash
IF `status IS NULL`
=> SET `status = pending`
~~~

**Scheduled status**

~~~bash
IF `status = pending` AND `datetime is NOT null`
=> SET `callback = datetime - 3 HOURS`
=> SET `status = scheduled`
~~~

**Callback default (NEVER null)**

~~~bash
IF `callback is null` AND `datetime is null`     => SET `callback = NOW`
IF `callback is null` AND `datetime is NOT null` => SET `callback = datetime`
~~~

**Rehashing - pitch-miss status**

See also: [Rehashing CRONTAB -  CallService:updatePitchMiss](http://dev.lev-interactive.com/one-day-roofing/crm-core-backend/blob/master/src/Lev/CRMBundle/Service/CallService.php#L405)

Sets `status = pitch-miss`.

~~~bash
IF `status = confirmed`
AND `customer.tcpa = false`
AND `customer.wrongNumber = false`
AND `COUNT(customer.notQualifiedReason) = 0`
AND `resulted = true`
AND `datetime is NOT null`
AND `datetime <= NOW - 1 HOUR`
=> SET status = pitch-miss
~~~

**Resetting- no-pitch status**

See also: [Resetting CRONTAB - CallService:updateNoPitch](http://dev.lev-interactive.com/one-day-roofing/crm-core-backend/blob/master/src/Lev/CRMBundle/Service/CallService.php#L443)

Sets `status = no-pitch`

-   _DONT_SHOW_QUERY AND appointment was canceled_

~~~bash
IF `customer.tcpa = false`
AND `customer.wrongNumber = false`
AND `COUNT(customer.notQualifiedReason) = 0`
AND `status = canceled`
=> SET status = no-pitch
~~~

-   _DONT_SHOW_QUERY AND If a day has passed and no sales rep is set,
    the call should go to reset._
    (TODO if salesRep is mandatory when datetime is set, this rule is USELESS)

~~~bash
IF `customer.tcpa = false`
AND `customer.wrongNumber = false`
AND `COUNT(customer.notQualifiedReason) = 0`
AND `status = pending`
AND `salesRep IS NULL`
AND `datetime IS NOT NULL`
AND `datetime < today`
=> SET status = no-pitch
~~~

-   _DONT_SHOW_QUERY AND No matter what, only TODAY's calls should be in
    scheduling and confirming. Everything else will either be in rehash,
    reset, or unresulted._

~~~bash
IF `customer.tcpa = false`
AND `customer.wrongNumber = false`
AND `COUNT(customer.notQualifiedReason) = 0`
AND `status IN (pending, scheduled, confirmed)`
AND `datetime IS NOT NULL`
AND `callback < today`
=> SET status = no-pitch
~~~

**Sold status**

~~~bash
IF `status <> canceled`
AND `sale IS NOT NULL`
=> SET `status = 'sold'`
=> SET `resulted = true`
~~~

**OBS: DONT_SHOW_QUERY**

-   Customer is _TCPA_
-   Customer is _Not Qualified_
-   Customer is _Number is Wrong_

## Callcenter Service - Queues

### Queues master filter

Where to check: [CallService:prepareQueryBuilderByType](http://dev.lev-interactive.com/one-day-roofing/crm-core-backend/blob/master/src/Lev/CRMBundle/Service/CallService.php#L166)

This method defines the filters for each queue type
(for both _Outbounds_ and _My Callbacks_).

**Scheduling Queue (also default case)**

~~~bash
IF `status = pending`
AND (`datetime IS NULL` OR `salesRep IS NULL`)
~~~

**Confiming Queue**

~~~bash
IF `status = scheduled`
AND (`datetime IS NOT NULL`)
AND (`datetime > NOW`)
AND (`datetime <= NOW + 3 HOUR`)
~~~

**Resetting Queue**

~~~bash
IF `status = no-pitch`
~~~

**Rehashing Queue**

~~~bash
IF `status = pitch-miss`
~~~

**Unresulted Queue**

~~~bash
IF `status IN (scheduled, confirmed)`
AND (`datetime <= NOW - 1 HOUR`)
~~~

### Outbound filter

Where to check: [CallService:getOutboundCalls](http://dev.lev-interactive.com/one-day-roofing/crm-core-backend/blob/master/src/Lev/CRMBundle/Service/CallService.php#L239).

-   Get **Queues master filter** (see above)
-   Check IF user is sent to the filter his locks (usually **IS** the case)
-   THEN it's locked by the user OR not locked OR locking is expired:

~~~bash
AND (`lockedBy IS NULL` OR `lockedBy = userId` OR `dueBy < NOW`)
~~~

-   ELSE it's not locked OR locking is expired:

~~~bash
AND (`lockedBy IS NULL` OR `dueBy < NOW`)
~~~

-   Sorting

~~~bash
`datetime ASC`
~~~

## CRONTAB

Callcenter queues **Rehashing** (`status = pitch-miss`) and
**Reseting**  (`status = no-pitch`) are run each one minute using a command
([CronCommand](http://dev.lev-interactive.com/one-day-roofing/crm-core-backend/blob/master/src/Lev/CRMBundle/Command/CronCommand.php)).

The rules are the same of **Appointment Entity** for **Rehashing** and
**Reseting**, check above.

Commands:

-   Run ALL - [CallService:runCronTasks](http://dev.lev-interactive.com/one-day-roofing/crm-core-backend/blob/master/src/Lev/CRMBundle/Service/CallService.php#L392)
-   REHASHING -    [CallService:updatePitchMiss](http://dev.lev-interactive.com/one-day-roofing/crm-core-backend/blob/master/src/Lev/CRMBundle/Service/CallService.php#L406)
-   RESETTING -  [CallService:runCronTasks](http://dev.lev-interactive.com/one-day-roofing/crm-core-backend/blob/master/src/Lev/CRMBundle/Service/CallService.php#L444)
