# Changelog
All notable changes to this project will be documented in this file, formatted via [this recommendation](https://keepachangelog.com/).

## [2.7.0] - 2026-08-25
### Changed
- Selecting an existing spreadsheet in the Form Builder is now limited to site administrators.

### Fixed
- Users without administrator permissions were able to manage Google Sheets accounts on the site. #18110,

## [2.6.0] - 2026-03-31
### Added
- Abandoned entries can now be sent to Google Sheets.
- Failed submissions can now be resent to Google Sheets from the Single Entry admin page.

### Changed
- The minimum WPForms version supported is 1.10.0.

### Fixed
- Google Sheets reconnection modal was appearing unnecessarily in Builder when no connections existed.
- Protected Camera field uploads displayed actual links instead of being redacted.

## [2.5.0] - 2025-10-14
### Fixed
- Google Sheets field mapping logic was affecting other WPForms integrations.

## [2.4.0] - 2025-05-06
### IMPORTANT
- Support for PHP 7.1 has been discontinued. If you are running PHP 7.1, you MUST upgrade PHP before installing this addon. Failure to do that will disable addon functionality.
- Addon requires the cURL extension to work. If you are running PHP without the cURL extension, you MUST enable it before upgrading this addon. Failure to do that will disable addon functionality.

### Added
- Compatibility with WPForms 1.9.5.

### Changed
- The minimum WPForms version supported is 1.9.5.
- Error handling was improved.

### Fixed
- The logic for displaying addon settings on the Integrations page.
- Unsaved changes modal no longer appears after saving the form.

## [2.3.1] - 2025-02-10
### Fixed
- Custom fields disappeared when you saved a form before connections are loaded.
- The custom fields were removed after duplicating the form.

## [2.3.0] - 2024-12-03
### Fixed
- The {entry_user_journey} smart tag value.

## [2.2.0] - 2024-09-24
### IMPORTANT
- Support for PHP 5.6 has been discontinued. If you are running PHP 5.6, you MUST upgrade PHP before installing WPForms Google Sheets 2.2.0. Failure to do that will disable WPForms Google Sheets functionality.
- Support for WordPress 5.4 and below has been discontinued. If you are running any of those outdated versions, you MUST upgrade WordPress before installing WPForms Google Sheets 2.2.0. Failure to do that will disable WPForms Google Sheets functionality.

### Added
- Support for authorizing ".dev" websites.

### Changed
- The minimum WPForms version supported is 1.9.1.

### Fixed
- Fixed an issue where the Single Item field only returned the Item Price when data was sent to Google Sheets.
- Fixed a bug where Google Sheets connections were inadvertently removed when changing the form template.
- Corrected field sorting to match the form fields order in settings when using field mapping and smart tags.

## [2.1.0] - 2024-06-11
### Added
- Compatibility with WPForms 1.8.9.
- New filter `wpforms_google_sheets_api_client_auth_args` to modify authorization and reauthorization URL arguments.

### Changed
- The minimum WPForms version supported is 1.8.9.

### Fixed
- Various RTL problems in the admin dashboard.

## [2.0.1] - 2024-01-12
### IMPORTANT
- All users who are sending entries to Google Sheets will need to re-authenticate their Google connection once version 2.0.1 is installed to avoid interruptions in service.

### Changed
- Lowered minimum WPForms version supported to 1.8.3.
- Lowered minimum PHP version supported to 5.6.
- Lowered minimum WordPress version supported to 5.2.

## [2.0.0] - 2024-01-11
### IMPORTANT
- All users who are sending entries to Google Sheets will need to re-authenticate their Google connection once version 2.0.0 is installed to avoid interruptions in service.
- Support for PHP 5.6 has been discontinued. If you are running PHP 5.6, you MUST upgrade PHP before installing WPForms Google Sheets 2.0.0. Failure to do that will disable WPForms Google Sheets functionality.
- Support for WordPress 5.4 and below has been discontinued. If you are running any of those outdated versions, you MUST upgrade WordPress before installing WPForms Google Sheets 2.0.0. Failure to do that will disable WPForms Google Sheets functionality.

### Changed
- Minimum WPForms version supported is 1.8.4.

## [1.1.0] - 2023-04-20
### Added
- Support for form field smart tags in field mapping of custom values.

### Fixed
- Attempt to activate the addon with WPForms version prior to 1.7.3 resulted in a fatal error.
- Field Mapping layout had incorrect subfield widths in Safari.

## [1.0.0] - 2022-10-25
- Initial release.
