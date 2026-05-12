# Public HTML Website

Static assets and PHP pages for the website currently deployed from Hostinger `public_html`.

## Local setup

This site expects MySQL database credentials from environment variables:

- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`
- `DB_CHARSET` optional, defaults to `utf8mb4`

Do not commit live credentials. Use `db.example.php` as a reminder of the required values when configuring hosting.

## Deploying updates

1. Make changes locally.
2. Commit them to Git.
3. Push to GitHub.
4. Pull or upload the updated files into Hostinger `public_html`.
