# Employee Import Template

## Required Fields and Format

The following fields are **required** for importing employees:

| Column Name | Required | Format/Values | Example |
|-------------|----------|---------------|---------|
| name | Yes | String (max 255) | John Doe |
| email | Yes | Valid email address | john.doe@company.com |
| phone | Yes | String/Number (max 20) | +20123456789 |
| gender | Yes | male OR female | male |
| marital_status | Yes | single OR married | single |
| national_id | Yes | String (max 255) | 12345678901234 |
| department | Yes | Existing department name | Engineering |
| position | Yes | Existing position name | Software Developer |
| contract_type | Yes | See Contract Types below | permanent |
| social_insurance_status | Yes | See Social Insurance Status below | not_applicable |

## Optional Fields

| Column Name | Required | Format/Values | Example |
|-------------|----------|---------------|---------|
| personal_email | No | Valid email address | john.personal@gmail.com |
| business_phone | No | String/Number (max 20) | +20987654321 |
| date_of_birth | No | Date (YYYY-MM-DD, DD/MM/YYYY, etc.) | 1990-05-15 |
| address | No | Text | 123 Main Street, Cairo, Egypt |
| emergency_contact_name | No | String (max 255) | Jane Doe |
| emergency_contact_relation | No | String (max 255) | Spouse |
| emergency_contact_phone | No | String/Number (max 20) | +20111222333 |
| employee_id | No | String - Auto-generated if empty | ENG0001 |
| employee_level | No | See Employee Levels below | entry |
| social_insurance_number | No | String (max 255) | SI123456789 |
| manager_email | No | Email of existing employee | manager@company.com |
| company_joining_date | No | Date (YYYY-MM-DD, DD/MM/YYYY, etc.) | 2024-01-15 |

## Valid Values

### Contract Types
- `permanent` - Permanent
- `fulltime` - Full Time  
- `parttime` - Part Time
- `freelance` - Freelance
- `credit_hours` - Credit Hours
- `internship` - Internship

### Employee Levels
- `internship` - Internship
- `entry` - Entry
- `junior` - Junior
- `mid` - Mid
- `senior` - Senior
- `lead` - Lead
- `manager` - Manager

### Social Insurance Status
- `not_applicable` - N/A
- `pending` - Pending
- `done` - Done

## Sample Excel/CSV Template

```csv
name,email,phone,gender,marital_status,national_id,department,position,contract_type,social_insurance_status,personal_email,business_phone,date_of_birth,address,emergency_contact_name,emergency_contact_relation,emergency_contact_phone,employee_level,social_insurance_number,manager_email,company_joining_date
John Doe,john.doe@company.com,+20123456789,male,single,12345678901234,Engineering,Software Developer,permanent,not_applicable,john.personal@gmail.com,+20987654321,1990-05-15,"123 Main Street, Cairo",Jane Doe,Spouse,+20111222333,entry,,manager@company.com,2024-01-15
Jane Smith,jane.smith@company.com,+20123456788,female,married,12345678901235,Marketing,Marketing Specialist,fulltime,pending,jane.personal@gmail.com,+20987654322,1988-03-20,"456 Oak Avenue, Cairo",John Smith,Husband,+20111222334,senior,SI123456789,marketing.head@company.com,2024-02-01
```

## Important Notes

1. **Departments and Positions** must exist in the system before importing
2. **Manager emails** must belong to existing employees in the system
3. **Employee IDs** will be auto-generated if not provided (format: DEPT001, DEPT002, etc.)
4. **Passwords** are automatically generated and sent via welcome email
5. **Dates** can be in various formats (YYYY-MM-DD, DD/MM/YYYY, MM/DD/YYYY, etc.)
6. **Phone numbers** should include country codes
7. **Social Insurance Number** is only required if social_insurance_status is 'pending' or 'done'

## Error Handling

If there are errors during import, you will see specific error messages indicating:
- Row number with the error
- Field name that caused the error
- Expected format or values

## Post-Import Actions

After successful import:
1. Welcome emails are automatically sent to all imported employees
2. Passwords are included in the welcome emails
3. Employees can log in immediately after receiving their credentials