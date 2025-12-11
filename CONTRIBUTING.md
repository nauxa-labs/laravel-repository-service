# Contributing to Laravel Repository Service

Thank you for considering contributing to Laravel Repository Service! This document outlines the guidelines for contributing to this project.

## Code of Conduct

Please be respectful and considerate in all interactions. We're all here to learn and improve.

## How to Contribute

### Reporting Bugs

1. Check if the bug has already been reported in [Issues](https://github.com/refinaldy/laravel-repository-service/issues)
2. If not, create a new issue with:
   - Clear, descriptive title
   - Steps to reproduce
   - Expected vs actual behavior
   - PHP version, Laravel version, and package version

### Suggesting Features

1. Check existing issues for similar suggestions
2. Create a new issue with:
   - Clear description of the feature
   - Use case and benefits
   - Example implementation (if applicable)

### Pull Requests

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/my-feature`
3. Make your changes following our coding standards
4. Write or update tests as needed
5. Run the test suite: `composer test`
6. Commit with clear messages: `git commit -m "feat: add new feature"`
7. Push to your fork: `git push origin feature/my-feature`
8. Open a Pull Request

## Development Setup

```bash
# Clone your fork
git clone https://github.com/YOUR_USERNAME/laravel-repository-service.git
cd laravel-repository-service

# Install dependencies
composer install

# Run tests
composer test
```

## Coding Standards

- Follow PSR-12 coding style
- Use strict types: `declare(strict_types=1);`
- Add PHPDoc blocks for all public methods
- Write meaningful commit messages following [Conventional Commits](https://www.conventionalcommits.org/)

### Commit Message Format

```
<type>: <description>

[optional body]
```

Types:
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `test`: Adding or updating tests
- `refactor`: Code refactoring
- `chore`: Maintenance tasks

## Testing

- Write tests for all new features
- Ensure all existing tests pass
- Aim for meaningful test coverage

```bash
# Run all tests
composer test

# Run with coverage
composer test-coverage
```

## Questions?

Feel free to open an issue for any questions or discussions.

Thank you for contributing! 🎉
