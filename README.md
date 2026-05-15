# Public HTML Website

Static assets and PHP pages for the website currently deployed from Hostinger `public_html`.

## Local setup

This site expects MySQL database credentials from a private `.env` file,
`db.local.php`, or environment variables.

For Hostinger Git deploys, put the private `.env` file one level above
`public_html` so redeploys do not overwrite it:

```text
domains/alpacatravels.com/.env
domains/alpacatravels.com/public_html/
```

Use the same values shown in `.env.example`:

```text
DB_HOST=localhost
DB_NAME=your_database_name
DB_USER=your_database_user
DB_PASS=your_database_password
DB_CHARSET=utf8mb4
```

- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`
- `DB_CHARSET` optional, defaults to `utf8mb4`

Do not commit live credentials. Copy `.env.example` to `.env` outside
`public_html` on Hostinger and fill in the real database values there, or
configure the environment variables above.

## Deploying updates

1. Make changes locally.
2. Commit them to Git.
3. Push to GitHub.
4. Pull or upload the updated files into Hostinger `public_html`.
