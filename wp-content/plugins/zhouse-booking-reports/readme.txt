=== Zhouse Booking Reports ===
Requires at least: 5.6
Tested up to: 7.1.5
Requires PHP: 7.4
Tested with PHP: 8.4
Stable tag: 1.2.1
License: GPLv2 or later

Reports for Booking Calendar meeting-room bookings, with CSV and PDF downloads.

== Description ==

Adds "Booking Calendar > Reports" to the WordPress admin.

* Filters: period presets (today, this week, this month, last month, this year, ...) or a custom range,
  filter by meeting date or by the date the booking was made, status, branch, room, company, and
  free-text search (name, email, phone, booking ID, admin notes).
* Branch-wise reports. Ready-made for Z Houses: Z House – Clifton (Z Meeting Room, Z Conference Room,
  Recreation Room), Z Signature – DHA (Z Signature) and Z Collective – Tariq Road. Branch names and
  the room-to-branch mapping can be changed in Reports > Settings. A "branch" (or "location") field
  in the booking form is also recognised.
* Company logo in the PDF header: the website logo by default, or any image from the Media Library.
* Company-wise reports: pick one company for its own report, or group the booking list by company.
* Group the booking list by branch or company, with subtotals per group.
* Who approved each booking and when (also who cancelled it). Read from Booking Calendar's action log
  in the booking notes, plus the plugin's own approval log for every action from now on.
* All times in 12-hour format (e.g. 2:30 PM).
* Summary cards: bookings, booked hours, average duration, attendees, companies, customers.
* Breakdowns: by branch, by company, by approving staff member, by room, by day / month, by weekday,
  by start hour.
* Booking details table (50 per page) with a link to each booking in Booking Calendar.
* Downloads that follow the current filters:
  - PDF report (A4 landscape: summary, breakdowns, full booking list with totals)
  - Bookings CSV (one row per booking, every form field, opens cleanly in Excel)
  - Summary CSV (totals and breakdown tables)

The plugin never writes to the Booking Calendar tables, and Booking Calendar can be updated
independently. It stores only its settings and an approval log table (wp_zbr_booking_log).

Access: the same user role that Booking Calendar allows on its Bookings page
(Booking > Settings > General > User permissions). Override with the `zbr_capability` filter.

== Installation ==

1. Plugins > Add New > Upload Plugin, choose zhouse-booking-reports.zip, then Install and Activate.
   (Or copy the zhouse-booking-reports folder into wp-content/plugins/.)
2. Open Booking Calendar > Reports. Branches and the logo are already set up; check them on the
   Settings tab (e.g. after adding a new room or branch).

== Notes ==

* Duration comes from the times Booking Calendar saves for each booking (start/end time fields).
  Full-day bookings are counted separately and not added to "Booked hours".
* "Persons" reads the "Number of Person" field (numberofperson). Field names are matched
  case-sensitively; other names can be mapped in ZBR_Data::FIELD_ALIASES.
* The PDF uses the standard Helvetica font (Latin characters). Non-Latin scripts such as Urdu
  are fully preserved in the CSV downloads.
* "Approved by" for older bookings comes from Booking Calendar's notes log (Booking > Settings >
  "Log booking actions", on by default). Approvals made before that option was on show
  "Not recorded". "Auto-approved" means the booking was approved automatically when it was made.
* Bundled library: FPDF 1.86 (lib/fpdf, permissive license, see lib/fpdf/license.txt).

== Changelog ==

= 1.2.1 =
* Tested on WordPress 7.1.5 with PHP 8.4 and MySQL 8.4 (no notices or deprecations from this plugin).
* Bookings starting at 12:00 AM now show their start time and duration.
* Branch names no longer wrap in the summary tables.

= 1.2.0 =
* Default branches and room mapping for Z House – Clifton, Z Signature – DHA, Z Collective – Tariq Road.
* Logo in the PDF header (website logo or Media Library), dark or white header.
* Room column in the PDF booking list; booking list grouped by branch by default.
* Company names matched ignoring case, spaces and punctuation; fixed the "(No company given)" filter.

= 1.1.0 =
* Branch-wise reports (settings page, branch filter, branch breakdown, branch column).
* Company filter and company report PDF title.
* Group booking list by branch or company with subtotals.
* Approved by / approved at, cancelled by / cancelled at, approval log table.
* 12-hour time format everywhere.

= 1.0.0 =
* First release.
