# Changelog
All notable changes to this project will be documented in this file, formatted via [this recommendation](https://keepachangelog.com/).

## [1.1.1] - 2026-07-23
### Fixed
- Strengthened new Slack account connections to require site owner confirmation.

## [1.1.0] - 2025-08-05
### IMPORTANT
- Support for PHP 7.1 has been discontinued. If you are running PHP 7.1, you MUST upgrade PHP before installing this addon. Failure to do that will disable addon functionality.

### Added
- Ability to post to private channels.

### Changed
- The minimum WPForms version supported is 1.9.5.

### Fixed
- Made the "Select Time" description non-translatable because this feature only works in English.
- PHP notice generated when loading deactivated users in the connection settings.
- Limited number of channels and users were available for sending messages.
- Archived channels were not excluded from the list.

## [1.0.0.1] - 2024-10-23
### Fixed
- Doc link in the Settings > Integrations page.

## [1.0.0] - 2024-10-23
### Added
- Initial release.
