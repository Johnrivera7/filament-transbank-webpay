# Security Policy

## Supported versions

Security fixes are applied to the latest release on the `main` branch.

## Reporting a vulnerability

Please **do not** open a public GitHub issue for security problems.

Email **johnriveragonzalez7@gmail.com** with:

- A description of the issue
- Steps to reproduce
- Impact assessment (if known)

You should receive an acknowledgement within a few business days.

## Security notes for integrators

- Store Transbank `api_key` values encrypted when persisting multi-tenant credentials in your application database.
- Never log full API keys or card data.
- Prefer production credentials only after Transbank commerce validation.
- Validate return payloads carefully: abort/timeout flows may send `TBK_*` without `token_ws`; when both are present, prefer `token_ws` and run `commit`.
