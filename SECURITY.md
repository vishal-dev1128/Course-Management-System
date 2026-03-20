# Security Policy

## Supported Versions

The following versions of **EduManage CMS** are currently being supported with security updates.

| Version | Supported          |
| ------- | ------------------ |
| 3.0.x   | :white_check_mark: |
| 2.x     | :x:                |
| 1.x     | :x:                |

## Reporting a Vulnerability

We take the security of EduManage CMS seriously. If you believe you have found a security vulnerability, please report it to us responsibly.

**Please do not report security vulnerabilities through public GitHub issues.**

### Vulnerability Reporting Process

1. **Email us**: Send a detailed report to `security@edumanage.com`.
2. **Include details**: Provide a description of the vulnerability, steps to reproduce, and potential impact.
3. **Response time**: We will acknowledge your report within 48 hours and provide an estimated timeline for a fix.
4. **Public disclosure**: After a fix is released, we will coordinate a public disclosure if appropriate.

## Security Best Practices for Users

- **Passwords**: Always use a strong, unique password for your account.
- **Session Security**: Always log out when using a shared or public computer.
- **Reporting**: If you notice any suspicious activity on your account, please contact an administrator immediately.

## Developer Guidelines

- Always use **PDO Prepared Statements** for database queries.
- Always use `htmlspecialchars()` when outputting user-provided data.
- Ensure `requireRole()` is used on every protected admin, instructor, or student page.
- Keep the `uploads/` directory protected and only allow whitelisted file types (JPG, PNG, WEBP).
