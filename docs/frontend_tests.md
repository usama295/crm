# Frontend Tests Tour

**In ALL forms**: test inputs with maximum lenght

## Login / Logout

### Login

*   [Credentials](http://dev.lev-interactive.com/one-day-roofing/crm-core-frontend/wikis/home#credentials)
*   [Test CallCenter users](http://dev.lev-interactive.com/one-day-roofing/crm-core-frontend/wikis/home#test-callcenter-users)

| Username                 | Email                   | Password                    | Notes                           |
| ---                      | ---                     | ---                         | ---                             |
| dev@lev-interactive.com  | dev@lev-interactive.com | js028NSJQAZLP0Hn193NoO0sms2 | super admin for staging         |
| admin@test.com           | admin@test.com          | 654321                      | CRM admin role                  |
| maria@test.com           | maria@test.com          | 654321                      | Marketing Representative role   |
| nicholaspino65@gmail.com | paula@test.com          | 654321                      | Call Center Representative role |
| john@test.com            | john@test.com           | 654321                      | Sales Representative role       |

~~~text
Not logged
URL `/` redirects to `/login`

Email Address [admin@test.com]
Password      [654321]
~~~

*   Must work with ENTER or clicking submit button
*   Error message:

~~~text
Invalid username and password combination
~~~

**Menu after login**

| Username                 | Role            | Menu options                                                                               |
| ---                      | ---             | ---                                                                                        |
| dev@lev-interactive.com  | Super admin     | Admin / Change Office / Customers / Appointments / Sales / Projects / Call Center / Logout |
| admin@test.com           | CRM admin role  | Admin / Change Office / Customers / Appointments / Sales / Projects / Call Center / Logout |
| nicholaspino65@gmail.com | Call Center Rep | Customers / Appointments / Call Center / Logout                                            |

### Logout

Must end session and redirects to `/login`.

*   [ ] **ERROR**: Logout seems to redirect to Dashboard and
    then redirect to login (ugly refresh behavior)

## Admin

*   Login as `admin@test.com` / `654321`

### Admin -> Manage Permissions

*   [ ] **ERROR**: Menu dropdown doesn't hide after click in any option
   (must click on an empty space)

**Actions**

*   _Click on any checkbox_  
    => PUT permission to users  
    => User must have new permission (added/removed) after logout/login

### Admin -> Manage Staff

**Search/Filter**

*   Search must find by parts of username, name (first/last), email

*   [ ] **ERROR**:
   Search has no minimum length (e.g. 3) and has a small delay
   before request (so if you type 5 letters you get 5 hits on API
   unless you type fast!)

*   [ ] **ERROR**:
   Filter by _Related Office_ is not working (maybe a Office
   could be more effective)  
   Filter by _Employment Type_ `BAD REQUEST => "'employmentType' is not filterable."`

**Add**

*   [ ] **ERROR**: ALL dates are not being sent (check payload!!!)

*   [ ] **ERROR**: ATTACHMENTS not working!

*   [ ] **ERROR**: `Allocate Bonus`: modal `Allocate` button does nothing.

*   Required fields: `First Name`, `Last Name`, `Password`, `Password Confirm`, `Email`, `Street`, `City`, `State`, `Zip Code`
    => If submit without these fields, must have a  
    `Please check the form and resubmit.` message.

*   `Password`/`Password Confirm` must match (frontend validation on the fly)

*   _No coordinates associated for this address. click to search_  
    [ ] **ERROR**: not working???

*   `Position` must allowed multiple select

*   `Office` must default to active one, if changed to a different
    one must work normally (not filtered by active office)

*   `Capabilities`, `Certification Information` not required

*   Phones: with/without spaces, points or dashs, everything allowed

*   `Employment Type`: save EACH type to check

*   `License Information`: all fields not required

*   Fields without maximum length:

~~~javascript
{
  "error":20,
  "error_description":"Record invalid",
  "validation_errors":{
    "addressStreet":["This value is too long. It should have 30 characters or less."],
    "addressCity":["This value is too long. It should have 30 characters or less."],
    "addressZip":["This value is too long. It should have 9 characters or less."]
  }
}
~~~

*   After create a user, must login with email/password.
*   After edit and change password, must login with email/password.

**Delete**

=> Should delete cascading ? - so far we get an ugly error:

~~~text   
An exception occurred while executing 'DELETE FROM staff WHERE id = ?'
with params [37]: SQLSTATE[23000]: Integrity constraint violation: 1451
Cannot delete or update a parent row: a foreign key constraint fails
(`lev-odr-test`.`project`, CONSTRAINT `FK_2FB3D0EE1249A47E` FOREIGN KEY
(`job_manager_id`) REFERENCES `staff` (`id`))
~~~

### Admin -> Offices

**Search/Filter**

*   Must search by `name`

**Add/Edit**

*   `name` is required

**Delete**

*   [ ] SQL Error - Office's deletion is a **VERY DANGEROUS ACTION** if
    cascading is activated. Better remove this option???

~~~text
An exception occurred while executing 'DELETE FROM office WHERE id = ?' with params [2]:\n\nSQLSTATE[23000]: Integrity constraint violation: 1451 Cannot delete or update a parent row: a foreign key constraint fails (`lev-odr-test`.`project`, CONSTRAINT `FK_2FB3D0EEFFA0C224` FOREIGN KEY (`office_id`) REFERENCES `office` (`id`))
~~~

### Admin -> Financial Institutions

**Search/Filter**

*   Must search by `name`

**Add/Edit**

*   `name` is required, max 40 chars

**Delete**

*   **TODO no data to check** Must set related records to NULL

### Admin -> SubContratctors

**Search/Filter**

*   Must filter by `name`

**Add/Edit**

*   `name` is required, max 40 chars

**Delete**

*   **TODO no data to check** Must set related records to NULL

### Admin -> Advisory ZipCodes

**Search/Filter**

*   Must filter by `name`

**Add/Edit**

*   `name` is required, max 40 chars

**Delete**

*   **TODO no data to check** Must set related records to NULL

## Change Office

*   On click, `localStorage.office.active` must change to clicked office object
*   On listings and editing, `office` must be sent

**Checking office filter on editing**

*   List any record (e.g. customers)

*   Right click an edit button and open in a new tab

*   Change office on the _listing_ page

*   Reload (F5) on _editing_ page

*   Expects a 404 error (office doesn't match with record, `Record not found`)
    message

## Customers

**Search/Filter**

*   Must search by `primaryFirstName`, `primaryLastName`, `secondaryFirstName`,
    `secondaryLastName`, `phone1Number`, `phone2Number`, `phone3Number`, `email`

*   [ ] **ERROR**: Filter `Primary Name` is sending `primaryLastName`. It must be
    `fullname` so it filters both `primaryFirstName` and `primaryLastName`

**Add/Edit**

*   Valid address must return Google Lat/Lng:  
    3335 Arundel On The Bay Rd - Annapolis - MD - 20403

*   Invalid address must return line error if zip code is NULL:
    `No coordinates associated for this address.`

*   Invalid address must return line error if zip code is NOT NULL but wrong:
    `No coordinates associated for this address.` + `click to search`

*   [ ] **ERROR:** When `click to search` works, it would be nice to fill zip code
    and when it doesn't, some extra info (it seems nothing happened, maybe
    something like "couldn't find coordinates for this address, fix it")

*   Wrong address can return Lat/Lng in some cases - of course, wrong Lat/Lng:  
    3335 Arundel On The Bay Rd - **Blah** - MD - 20403 => this returns VERY
    different data. Maybe a confirmation before accepts a Lat/Lng could be
    a safe option

*   Check max lenght on all inputs  
    [ ] **ERROR**: `addressZipcode` must be 9 chars lenght max  
    [ ] **ERROR**: `addressStreet` and `addressCity` must be 40 chars lenght max

*   [ ] **ERROR**: `Create Appointment` button must not appear on ADD customer -
    only on EDITING.

**Delete**

*   Must cascade for all related records (Appoitment, Sale, Project,
    History, etc)

## Appointments

**Search/Filter**

*   Must search by **Customer** `primaryFirstName`, `primaryLastName`, `secondaryFirstName`,
    `secondaryLastName`, `phone1Number`, `phone2Number`, `phone3Number`, `email`

*   [ ] **ERROR**: Customer filter must be removed - as `name` field is virtual,
    is searching for `customer.primaryFirstName` and `customer.primaryLastName`.

*   [ ] **ERROR**: Demoed filter is not being sent.

**Add/Edit**

*   On listing, must **NOT** have a `Add` button - adding only available
    from Customer

*   When creating from a Customer is expected that Address fields
    come prefilled

*   All address comments from Customer is valid for Appointment.

*   [ ] **ERROR**: `Create Sale` button must not appear on ADD appointment -
    only on EDITING.

*   `Product of Interest`:  if `Windows`, `How Many Windows` and
    `Windows Last Replaced` must be required

*   `Product of Interest`:  if `Roofing`, `Roof Age` must be required

*   `Product of Interest`:  if `Siding`, `Number of Sides` and
    `Siding age` must be required

*    Marketing Rep must allow to be set to NULL

*    Schedule: changing `Datetime` must reload slots and `Sales Reps`

*    Schedule: `Datetime` is selected but no slot is set, `Datetime` must
     **NOT** be sent on payload (is incomplete)

*    Schedule: chaging slot must reload `Sales Rep` list

*    Schedule: `Datetime` + slot can be saved without `Sales Rep`

*    History: on save, any `Notes (optional)` must be saved and displayied
     as on the list as `User note`

*    History: changing `status` must be registered as
     _"Status changed from 'old status' to 'new status'"_

*    History: changing `datetime` must be registered as
    _"Status changed from 'old datetime' to 'new datetime'"_

*    History: changing `salesRep` must be registered as
    _"Status changed from 'old salesRep name' to 'new salesRep name'"_

*    History: changing `resulted` must be registered as
    _"Resulted changed from not resulted to RESULTED"_ or
    _"Resulted changed from resulted to NOT RESULTED"_
    (on specific link on email/SMS messages)

*   Changing `salesRep` must force sending a Email and SMS message

*   Appointments with `status = sold` must change `Create Sale` to
    `View Sale`

**Delete**

*   Must cascade for all related records (Sale, Project,
    History, etc)

### Sales

**Search/Filter**

*   Must search by **Customer** `primaryFirstName`, `primaryLastName`, `secondaryFirstName`,
    `secondaryLastName`, `phone1Number`, `phone2Number`, `phone3Number`, `email`

**Add/Edit**

*   On listing, must **NOT** have a `Add` button - adding only available
    from Appointment

*   Must have a link to **View Appoinment**

*   Must have a link to **View Project** or **Add Project**

*   Product Calculator must thrown an error on saving if any field
    of an item is not set

*   Adding and removing items on Product Calculator must work in any
    order, for a new sale or editing one.

*   [ ] **ERROR**: If a product calculator item is filled and you change
    "Category", both "Category" and "Option" changes do "- Select -" with
    no dropdown options, as if "Applies to" was not set.
    If you change "Applies to" again, it works.

*   [ ] **ERROR**: If a product calculator item is filled and you change
    "Applies to", both "Category" and "Option" changes do "- Select -" with
    no dropdown options, as if "Applies to" was not set.
    If you change "Applies to" again, it works.

*   Save with all "Discount Method" and check on the response if `jobCeiling`
    matches with frontend calculated `jobCeiling`.



**Delete**

*   Must cascade for all related records (Projects, History, etc)






**TODO**

### Projects

**Search/Filter**

*   Must search by **Customer** `primaryFirstName`, `primaryLastName`, `secondaryFirstName`,
    `secondaryLastName`, `phone1Number`, `phone2Number`, `phone3Number`, `email`

*   [ ] **ERROR**: `Status` filter must be a dropdown

**Add/Edit**

*   On listing, must **NOT** have a `Add` button - adding only available
    from Sale

*   [ ] **ERROR**: `Activities` are not being sent as an array; name is in POST
    "array" format `activities[0][assignee]`

*   [ ] **ERROR**: `Activite` Remove button is not working - maybe because of
    the above error

**Delete**

*   Must cascade for all related records (History, etc)
