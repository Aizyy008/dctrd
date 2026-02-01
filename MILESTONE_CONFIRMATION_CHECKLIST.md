# Milestone Confirmation Checklist
# RocketLMS v3.0 Project


## MILESTONE 1: Foundation & Core Infrastructure

**Duration:** 2 Weeks | **Budget:** PKR 30,000

### API Keys & Credentials

#### Exchange Rate APIs
| Service | Required | Status | Where to Get | Notes |
|---------|----------|--------|--------------|-------|
| ExchangeRatesAPI.io API Key | YES | [ ] | https://exchangeratesapi.io/pricing/ | Primary API (Free tier available) |
| ExchangeRate.host API | OPTIONAL | [ ] | https://exchangerate.host/ | Free backup, no key needed |
| Currency Layer API Key | OPTIONAL | [ ] | https://currencylayer.com/ | Alternative backup |

**Questions:**
1. Which currencies should be supported? (USD, EUR, GBP, PKR, etc.)
   - Answer: _______________
2. What should be the base currency? (USD recommended)
   - Answer: _______________
3. Should historical rates be stored? For how long?
   - Answer: _______________

#### Redis/Cache Configuration
| Item | Required | Status | Notes |
|------|----------|--------|-------|
| Redis server access | RECOMMENDED | [ ] | Host, Port, Password |
| Alternative cache driver? | YES | [ ] | If no Redis: file, database, memcached? |

**Questions:**
1. Is Redis installed on the server?
   - Answer: _______________
2. If not, can we install it?
   - Answer: _______________

### Configuration Decisions

#### Unit Preferences
**Questions:**
1. Which units should be supported?
   - Length: [ ] Kilometers, [ ] Miles, [ ] Meters, [ ] Feet
   - Mass: [ ] Kilograms, [ ] Pounds, [ ] Grams, [ ] Ounces
   - Area: [ ] Square Meters, [ ] Square Feet, [ ] Acres
   - Temperature: [ ] Celsius, [ ] Fahrenheit
   - Answer: _______________

2. What should be the default base units for storage?
   - Length: _______________ (recommend: kilometers)
   - Mass: _______________ (recommend: kilograms)
   - Answer: _______________

3. Should unit preferences be per-user or global setting?
   - Answer: _______________

#### Admin Panel Settings
**Questions:**
1. Where should the currency auto-update toggle appear?
   - Answer: Admin → Settings → Financial → Currency (Confirm: YES/NO)

2. Should admins receive notifications when API fails?
   - Answer: _______________

3. Who should have permission to change unit settings?
   - Answer: _______________

### Version Control Setup

**Questions:**
1. Repository name preference?
   - Answer: _______________

2. Should we keep _ERP, _Mirror, nextcloud folders in Git?
   - Answer: _______________ (Recommend: NO, use .gitignore)

3. Commit message language: English or Arabic?
   - Answer: _______________

### Testing Requirements

**Questions:**
1. Should we write unit tests for all services?
   - Answer: _______________ (Recommend: YES)

2. Who will perform UAT (User Acceptance Testing)?
   - Answer: _______________

3. Staging server URL for testing?
   - Answer: _______________

---

## MILESTONE 2: Geolocation, Bookings & Integrations

**Duration:** 3-4 Weeks | **Budget:** PKR 45,000

### API Keys & Credentials

#### Google Calendar API
| Item | Required | Status | Where to Get | Notes |
|------|----------|--------|--------------|-------|
| Google Cloud Project ID | YES | [ ] | https://console.cloud.google.com | Create new project |
| OAuth 2.0 Client ID | YES | [ ] | APIs & Services → Credentials | For web application |
| OAuth 2.0 Client Secret | YES | [ ] | Same as above | Keep secret |
| Google Calendar API enabled | YES | [ ] | APIs & Services → Library | Enable API |
| Authorized redirect URIs | YES | [ ] | Add: yourdomain.com/calendar/callback | Whitelist domain |

**Questions:**
1. Which Google account should we use for API setup?
   - Answer: _______________

2. Should calendar sync be real-time or scheduled (every 30 min)?
   - Answer: _______________

3. Who can use calendar sync? (All users, Organizations only, etc.)
   - Answer: _______________

#### Microsoft Outlook/Graph API
| Item | Required | Status | Where to Get | Notes |
|------|----------|--------|--------------|-------|
| Microsoft Azure App Registration | YES | [ ] | https://portal.azure.com | Register application |
| Application (client) ID | YES | [ ] | App registrations → Overview | Copy Client ID |
| Client Secret | YES | [ ] | Certificates & secrets → New secret | Keep secret |
| Microsoft Graph API permissions | YES | [ ] | API permissions → Add permission | Calendars.ReadWrite |
| Redirect URI configured | YES | [ ] | Authentication → Add platform | Add web redirect URI |

**Questions:**
1. Should we use personal or organizational Microsoft account?
   - Answer: _______________

2. Should sync be one-way or two-way?
   - Answer: _______________ (Recommend: two-way)

#### OpenStreetMap / Geocoding
| Item | Required | Status | Where to Get | Notes |
|------|----------|--------|--------------|-------|
| OpenStreetMap Nominatim | NO KEY | [ ] | Free, no key needed | Rate limit: 1 req/sec |
| Alternative: Google Maps API | OPTIONAL | [ ] | https://console.cloud.google.com | If more requests needed |
| Alternative: Mapbox API | OPTIONAL | [ ] | https://www.mapbox.com/ | Free tier available |

**Questions:**
1. Expected traffic for geocoding requests?
   - Answer: _______________ (users/day)

2. Should we use Google Maps for better accuracy (costs money)?
   - Answer: _______________

3. Which map provider to display? OpenStreetMap or Google Maps?
   - Answer: _______________

### Booking System Configuration

#### Business Rules
**Questions:**
1. Minimum booking advance time? (e.g., 2 hours before)
   - Answer: _______________

2. Maximum booking advance time? (e.g., 6 months)
   - Answer: _______________

3. Default cancellation policy?
   - Free cancellation until: _______________ (e.g., 24 hours before)
   - Cancellation fee: _______________ (percentage or fixed)

4. Reschedule policy?
   - Allow reschedule: YES/NO _______________
   - Reschedule fee: _______________
   - How many times: _______________

5. No-show policy?
   - Charge full amount: YES/NO _______________
   - Other action: _______________

6. Buffer time between bookings?
   - Answer: _______________ (e.g., 15 minutes)

#### Booking Categories Priority
**Questions:**
Which 23 templates should be implemented first? (Rank 1-23)
1. [ ] Hotel/Accommodation - Rank: ___
2. [ ] Restaurant - Rank: ___
3. [ ] Beauty Salon - Rank: ___
4. [ ] Car Rental - Rank: ___
5. [ ] Real Estate - Rank: ___
6. [ ] Healthcare Clinic - Rank: ___
7. [ ] Fitness Center - Rank: ___
8. [ ] Event Space - Rank: ___
9. [ ] Photography Studio - Rank: ___
10. [ ] Consulting Services - Rank: ___
11. [ ] Repair Services - Rank: ___
12. [ ] Tours & Travel - Rank: ___
13. [ ] Spa & Wellness - Rank: ___
14. [ ] Automotive Services - Rank: ___
15. [ ] Home Services - Rank: ___
16. [ ] Pet Services - Rank: ___
17. [ ] Education/Tutoring - Rank: ___
18. [ ] Legal Consultation - Rank: ___
19. [ ] Financial Advisory - Rank: ___
20. [ ] Equipment Rental - Rank: ___
21. [ ] Venue Booking - Rank: ___
22. [ ] Appointment Services - Rank: ___
23. [ ] General Services - Rank: ___

**Or should we implement all 23 at once?**
- Answer: _______________

#### Resource Management
**Questions:**
1. Should resources (rooms, vehicles, staff) be shared across bookings?
   - Answer: _______________

2. How to handle resource conflicts?
   - Block double booking: YES/NO _______________
   - Allow waitlist: YES/NO _______________

3. Should resources have maintenance/unavailable periods?
   - Answer: _______________

### Import/Export Specifications

**Questions:**
1. What file format for export? (Excel, CSV, or both)
   - Answer: _______________

2. Should import validate against duplicates?
   - Answer: _______________

3. Maximum rows per import file?
   - Answer: _______________ (Recommend: 1000)

4. Who can import/export?
   - Admin only: YES/NO _______________
   - Organizations: YES/NO _______________

### Notification Preferences

**Questions:**
1. Booking confirmation sent via:
   - [ ] Email
   - [ ] SMS
   - [ ] Push notification
   - [ ] In-app notification

2. Reminder notifications timing?
   - ___ hours before booking
   - ___ days before booking
   - Both: _______________

3. SMS provider preference?
   - Currently using: _______________
   - Continue or change: _______________

---

## MILESTONE 3: Advanced Features & Polish

**Duration:** 3 Weeks | **Budget:** PKR 25,000

### API Keys & Credentials

#### Perfex ERP Integration
| Item | Required | Status | Where to Get | Notes |
|------|----------|--------|--------------|-------|
| Perfex ERP URL | YES | [ ] | Your Perfex installation | e.g., yourdomain.com/_ERP |
| Perfex API Key | YES | [ ] | Perfex Admin → Settings → API | Enable API first |
| Admin API Key (full access) | YES | [ ] | Generate for sync user | Keep secret |
| Webhook URL setup | YES | [ ] | For real-time updates | Configure in Perfex |
| SSL certificate on Perfex | RECOMMENDED | [ ] | For secure API calls | HTTPS required |

**Questions:**
1. Should all users sync to Perfex or only Organizations?
   - Answer: _______________

2. Sync frequency: Real-time or Scheduled?
   - Answer: _______________ (Recommend: Real-time with queue)

3. What to sync?
   - [ ] Customers (Users → Clients)
   - [ ] Orders (Orders → Invoices)
   - [ ] Products (Products → Items)
   - [ ] Payments (Payments → Payments)
   - [ ] Bookings (Bookings → Appointments)
   - All: YES/NO _______________

4. Who should have access to ERP from RocketLMS?
   - Answer: _______________

5. Should failed syncs retry automatically?
   - Answer: _______________ (Recommend: YES, 3 retries)

#### MailWizz Integration
| Item | Required | Status | Where to Get | Notes |
|------|----------|--------|--------------|-------|
| MailWizz URL | YES | [ ] | Your MailWizz installation | e.g., mail.yourdomain.com |
| MailWizz API Key | YES | [ ] | MailWizz → API Keys | Create new key |
| Default mailing list ID | YES | [ ] | For auto-subscribe | List ID number |

**Questions:**
1. Should all new users auto-subscribe to mailing list?
   - Answer: _______________

2. Which user types should sync?
   - [ ] Students
   - [ ] Instructors
   - [ ] Organizations
   - All: _______________

3. Allow users to unsubscribe from within RocketLMS?
   - Answer: _______________

#### Premium URL Shortener
| Item | Required | Status | Where to Get | Notes |
|------|----------|--------|--------------|-------|
| URL Shortener installation URL | YES | [ ] | Your installation | e.g., s.yourdomain.com |
| API Token | YES | [ ] | Generate from dashboard | Keep secret |
| Custom domain configured | OPTIONAL | [ ] | For branded short links | e.g., short.brand.com |

**Questions:**
1. Should QR codes be generated automatically for:
   - [ ] All products
   - [ ] All courses
   - [ ] All bookings
   - [ ] All certificates
   - [ ] All invoices
   - On-demand only: _______________

2. QR code size/resolution preference?
   - Answer: _______________ (Recommend: 512x512px)

3. Should QR codes be embeddable in emails?
   - Answer: _______________

4. Track anonymous QR scans or authenticated only?
   - Answer: _______________

5. QR code expiry time (if any)?
   - Answer: _______________ (Recommend: Never expire)

### Checkout Modules Configuration

**Questions:**
1. Which checkout modules should be available by default?
   - [ ] Days (date picker)
   - [ ] Hours (time slot selector)
   - [ ] Staff Member (dropdown)
   - [ ] Persons + Children (quantity with age)
   - [ ] Extra Services (checkboxes with price)
   - [ ] Cancellation Policy (info + checkbox)
   - [ ] Meal Plan
   - [ ] Special Requests (text area)
   - Custom: _______________

2. Who can enable/disable modules?
   - Admin only: YES/NO _______________
   - Organizations: YES/NO _______________
   - Per product/course/booking: YES/NO _______________

3. Should modules be mandatory or optional per seller?
   - Answer: _______________

### Multi-Role System

#### Role Configuration
**Questions:**
1. Which sub-roles should be active at launch?
   
   **Instructors:**
   - [ ] Seller
   - [ ] Operator (Tour)
   - [ ] Event Organizer
   - [ ] Agent
   
   **Organizations:**
   - [ ] Business Seller
   - [ ] Business Producer
   - [ ] Business Services
   - [ ] Tour Operator
   - [ ] Agency / OTA
   
   **Customers:**
   - [ ] Individual
   - [ ] Student
   - [ ] Store
   - [ ] Wholesaler
   - [ ] Importer
   - [ ] Advertiser
   - [ ] Promoter
   - [ ] Travel Agency / OTA
   - [ ] Tour Operator

2. Should users be able to have multiple roles simultaneously?
   - Answer: _______________ (Recommend: YES)

3. Role approval process:
   - Auto-approve: YES/NO _______________
   - Admin review required: YES/NO _______________
   - Document verification required: YES/NO _______________

4. Can users switch between active roles?
   - Answer: _______________

5. Role-based pricing:
   - Different prices per customer group: YES/NO _______________
   - Wholesale discounts: YES/NO _______________

#### Form Requirements
**Questions:**
1. Which documents/forms required per role?
   - Instructor/Seller: _______________
   - Organization: _______________
   - Importer: _______________
   - Wholesaler: _______________

2. Document verification turnaround time?
   - Answer: _______________ (SLA for admin review)

3. What happens if role rejected?
   - Allow resubmit: YES/NO _______________
   - Show reason: YES/NO _______________

### Testing & Quality Assurance

**Questions:**
1. Who will perform final testing?
   - Internal team: _______________
   - Client team: _______________
   - External QA: _______________

2. Testing devices/browsers required?
   - Desktop: [ ] Chrome [ ] Firefox [ ] Safari [ ] Edge
   - Mobile: [ ] iOS [ ] Android
   - Tablet: [ ] iPad [ ] Android Tablet

3. Load testing requirements?
   - Concurrent users: _______________
   - Transactions per second: _______________

4. Security testing required?
   - Penetration testing: YES/NO _______________
   - Third-party audit: YES/NO _______________

### Deployment & Go-Live

**Questions:**
1. Preferred deployment time?
   - Date: _______________
   - Time: _______________ (Recommend: Off-peak hours)
   - Timezone: _______________

2. Rollback plan if issues occur?
   - Answer: _______________

3. Downtime acceptable?
   - Duration: _______________ (minutes)
   - Notify users: YES/NO _______________

4. Post-launch monitoring?
   - Duration: _______________ (hours/days)
   - 24/7 support: YES/NO _______________

### Documentation Requirements

**Questions:**
1. Documentation language: English or Arabic or Both?
   - Answer: _______________

2. Video tutorial language?
   - Answer: _______________

3. Documentation format preference?
   - [ ] PDF
   - [ ] Online wiki/docs site
   - [ ] Google Docs
   - [ ] Markdown files

4. Who should be trained?
   - Names: _______________
   - Training format: Video call, In-person, Recorded videos?
   - Answer: _______________

---

## GENERAL QUESTIONS & CLARIFICATIONS

### Business Logic

1. **Time zones:**
   - Default timezone for system: _______________
   - Show times in user's timezone: YES/NO _______________

2. **Currency display:**
   - Show multiple currencies simultaneously: YES/NO _______________
   - Default currency: _______________

3. **Date format:**
   - Preferred format: DD/MM/YYYY or MM/DD/YYYY or YYYY-MM-DD
   - Answer: _______________

4. **Tax handling:**
   - Tax inclusive or exclusive pricing: _______________
   - Different tax rates per region: YES/NO _______________

5. **Commission structure:**
   - Platform commission percentage: _______________ %
   - Different rates for courses vs products vs bookings: YES/NO _______________

### Performance & Scalability

1. **Expected load:**
   - Current active users: _______________
   - Expected users after v3.0: _______________
   - Peak concurrent users: _______________

2. **Storage requirements:**
   - Current storage used: _______________ GB
   - Expected storage after bookings: _______________ GB
   - Media file size limits: _______________ MB

3. **Backup frequency:**
   - Current backup schedule: _______________
   - Preferred backup frequency: Daily/Weekly/Real-time _______________
   - Backup retention: _______________ days

### Support & Maintenance

1. **Post-launch support:**
   - Duration: _______________ days (included: 30 days)
   - Response time SLA: _______________
   - Support hours: _______________

2. **Bug fix policy:**
   - Critical bugs: Fix within _______________ hours
   - Major bugs: Fix within _______________ days
   - Minor bugs: Fix within _______________ days

3. **Ongoing maintenance:**
   - Monthly retainer: YES/NO _______________
   - Scope: Bug fixes, Updates, New features _______________

4. **Emergency contact:**
   - Primary: _______________
   - Secondary: _______________
   - Available: 24/7 or Business hours _______________

---

## SIGN-OFF CHECKLIST

Before starting each milestone, ensure:

### Milestone 1 Kickoff
- [ ] All pre-project requirements completed
- [ ] Exchange rate API keys received
- [ ] Redis/Cache configuration confirmed
- [ ] Unit preferences decided
- [ ] Git repository set up and access granted
- [ ] Staging environment URL confirmed
- [ ] Kickoff meeting conducted
- [ ] Timeline and deliverables agreed
- [ ] Payment terms confirmed

### Milestone 2 Kickoff
- [ ] Milestone 1 completed and approved
- [ ] Milestone 1 payment received
- [ ] Google Calendar API credentials received
- [ ] Microsoft Graph API credentials received
- [ ] OpenStreetMap/Geocoding provider confirmed
- [ ] Booking business rules finalized
- [ ] Template priorities ranked
- [ ] Notification preferences confirmed
- [ ] Import/export format agreed

### Milestone 3 Kickoff
- [ ] Milestone 2 completed and approved
- [ ] Milestone 2 payment received
- [ ] Perfex ERP credentials received
- [ ] MailWizz credentials received
- [ ] Premium URL Shortener credentials received
- [ ] Checkout modules finalized
- [ ] Multi-role configuration confirmed
- [ ] Testing plan agreed
- [ ] Deployment date scheduled
- [ ] Documentation requirements confirmed

### Final Delivery
- [ ] All milestones completed
- [ ] All acceptance criteria met
- [ ] Client testing completed
- [ ] Documentation delivered
- [ ] Training completed
- [ ] Deployment to production successful
- [ ] Post-launch monitoring period started
- [ ] Final payment received
- [ ] Project closure meeting conducted

---

## NOTES SECTION

Use this space for additional clarifications, special requirements, or notes:

```
_______________________________________________________________________________
_______________________________________________________________________________
_______________________________________________________________________________
_______________________________________________________________________________
_______________________________________________________________________________
```

---

**Document Version:** 1.0  
**Last Updated:** 2026-01-31  
**Next Review:** Before each milestone kickoff  

**Prepared By:** Development Team  
**Reviewed By:** [Client Name] _______________  
**Date:** _______________  
**Signature:** _______________

---

## APPENDIX: Common Issues & Troubleshooting

### If API Keys Don't Work
1. Check expiry date
2. Verify IP whitelist
3. Check rate limits
4. Verify correct endpoint URL
5. Test with Postman first

### If OAuth Fails
1. Verify redirect URI matches exactly
2. Check SSL certificate is valid
3. Verify scopes/permissions granted
4. Check token expiry and refresh logic
5. Test in incognito mode

### If Database Migration Fails
1. Check current database version
2. Backup database before retry
3. Verify user has proper permissions
4. Check for foreign key constraints
5. Run migrations one at a time

### If Server Access Issues
1. Verify SSH key format
2. Check firewall rules
3. Verify user permissions
4. Test with different client (PuTTY, Terminal)
5. Contact hosting provider

---

*This checklist ensures smooth project execution and prevents delays due to missing information. Please complete all sections before milestone kickoff.*
