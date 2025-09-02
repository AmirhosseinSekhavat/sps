# Changelog

All notable changes to the PDCCUT.IR application will be documented in this file.

## [1.0.0] - 2025-01-27

### Added
- **Core Application Structure**
  - Laravel 12 framework setup
  - Filament Admin Panel v3 integration
  - SQLite database support
  - Persian (Farsi) localization

- **User Management System**
  - Complete user CRUD operations
  - Custom user fields (first_name, last_name, father_name, etc.)
  - Financial data management (share amounts, profits, payments)
  - Excel/CSV import/export functionality
  - Bulk operations (activate, deactivate, delete)

- **Authentication System**
  - OTP-based user authentication
  - National code only login
  - Mobile number verification
  - Queue-based SMS sending
  - Session management

- **Admin Panel Features**
  - User management interface
  - Share certificate management
  - Notification system
  - Settings management
  - Dashboard with statistics
  - Quick user access (`/u/{national_code}`)

- **PDF Generation**
  - Share certificate PDF templates
  - Earned profits reports
  - RTL and Persian support
  - Laravel Snappy integration

- **API Endpoints**
  - User profile management
  - Certificate retrieval
  - Notification handling
  - Mobile app support

- **Advanced Features**
  - Search functionality
  - Statistics and analytics
  - Widget system
  - Queue management
  - File storage

### Technical Features
- **Database Schema**
  - Users table with custom fields
  - Share certificates table
  - Notifications and replies
  - Earned profits tracking
  - OTP codes management

- **Services**
  - SMS service (ready for Melipayamak)
  - PDF generation service
  - Excel import/export service

- **Security**
  - Admin middleware
  - CSRF protection
  - Input validation
  - Role-based access control

### Development Tools
- **Setup Scripts**
  - Automated installation script
  - Docker support
  - Makefile for common commands
  - Environment configuration

- **Documentation**
  - Comprehensive README
  - API documentation
  - Setup instructions
  - Troubleshooting guide

## [0.9.0] - 2025-01-26

### Added
- Initial project setup
- Basic Laravel structure
- Filament admin panel integration

### Changed
- Database migrations structure
- User model implementation

### Fixed
- Icon compatibility issues
- Route naming conflicts
- Cache management

## [0.8.0] - 2025-01-25

### Added
- User authentication system
- OTP verification
- Admin panel resources

### Changed
- Database schema optimization
- Model relationships

## [0.7.0] - 2025-01-24

### Added
- Excel import/export functionality
- PDF generation system
- API endpoints

### Changed
- Service layer implementation
- Queue system setup

## [0.6.0] - 2025-01-23

### Added
- Widget system
- Dashboard statistics
- User management interface

### Changed
- Admin panel customization
- Persian localization

## [0.5.0] - 2025-01-22

### Added
- Basic admin panel
- User resource management
- Authentication middleware

### Changed
- Project structure
- Configuration files

## [0.4.0] - 2025-01-21

### Added
- Project initialization
- Laravel 12 setup
- Composer dependencies

### Changed
- Development environment
- Package versions

## [0.3.0] - 2025-01-20

### Added
- Requirements analysis
- Technical specifications
- Project planning

## [0.2.0] - 2025-01-19

### Added
- Initial concept
- Feature requirements
- User stories

## [0.1.0] - 2025-01-18

### Added
- Project conception
- Basic requirements
- Technology stack selection

---

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/your-username/pdccut/tags).

## Authors

- **Development Team** - *Initial work* - [PDCCUT.IR](https://github.com/your-username/pdccut)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

- Laravel Framework
- Filament Admin Panel
- Laravel Excel
- Laravel Snappy
- Heroicons 