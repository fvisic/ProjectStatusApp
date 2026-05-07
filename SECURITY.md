# Security Policy

## Supported Versions

Only the latest release receives security fixes.

| Version | Supported |
|---------|-----------|
| 1.x     | Yes       |

## Reporting a Vulnerability

**Do not open a public GitHub issue for security vulnerabilities.**

Use GitHub's private vulnerability reporting instead:
**[Report a vulnerability](https://github.com/fvisic/ProjectStatusApp/security/advisories/new)**

Include:

- A description of the vulnerability and its potential impact
- Steps to reproduce or a proof-of-concept
- Affected version(s)

You can expect an acknowledgement within **48 hours** and a resolution timeline within **7 days** for critical issues.

Once a fix is released, the vulnerability will be disclosed publicly in the release notes with credit to the reporter (unless you prefer to remain anonymous).

## Scope

In scope:
- Authentication and authorization bypasses
- SQL injection, XSS, CSRF vulnerabilities
- Remote code execution
- Sensitive data exposure

Out of scope:
- Issues requiring physical access to the server
- Social engineering attacks
- Vulnerabilities in dependencies (report those upstream; open an issue here to track)

## Security Best Practices for Deployment

- Always set a strong, unique `APP_KEY`
- Use HTTPS in production (`SESSION_SECURE_COOKIE=true`)
- Keep the Docker image updated to pick up OS-level patches
- Restrict database access to localhost or a private network
- Do not expose `APP_DEBUG=true` in production
