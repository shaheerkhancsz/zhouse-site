# Changelog
All notable changes to this project will be documented in this file and formatted via [this recommendation](https://keepachangelog.com/en/1.0.0/).

## [1.4.0] - 2026-08-25
### Changed
- Selecting an existing Google Drive folder in the Form Builder is now limited to site administrators.
- The minimum WPForms version supported is 2.0.1.

### Fixed
- Exported entry files waiting to be delivered were stored at a guessable location and could be downloaded by anyone who knew the URL.

## [1.3.0] - 2026-07-30
### Added
- Geolocation Details, Dropbox Links, and Google Drive Links can now be included in additional information for the Entry Automation Export Entries task.

### Fixed
- A user with limited access to the Form Builder could view another form's Entry Automation settings.
- PDF export attachments included all entries instead of only those matching the selected filter.
- The Edit task name icon moved to a new line when editing a task name in an Entry Automation.

## [1.2.0] - 2026-04-07
### Added
- PDF export now supports displaying one entry per page.

### Changed
- The minimum WPForms version supported is 1.10.0.
- Improved compatibility with WordPress 7.0.

### Fixed
- PHP fatal error when adding a new field to a PayPal Commerce payment form.
- Export entries task remained stuck in Running or was incorrectly set to Inactive state after a delivery failure.

## [1.1.0] - 2026-02-03
### Fixed
- Validation of required settings on the Form Builder > Settings > Entry Automation screen.
- Error creating an automation entry in some rare cases.
- Fixed console error during Redo action.

## [1.0.0] - 2025-06-24
### Added
- Initial release.
