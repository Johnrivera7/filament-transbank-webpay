# Packagist / Composer publish checklist

This package is **MIT / public**. You do **not** need Private Packagist, a license key, or proprietary docs.

## What you need

1. **Public GitHub repo** — already: `Johnrivera7/filament-transbank-webpay`
2. **Valid `composer.json`** — `name`, `description`, `license`, `autoload`, `require`
3. **Git tag / release** (recommended) — e.g. `v1.0.0` so Packagist has a version
4. **Packagist account** — sign up at https://packagist.org with the same GitHub user (`Johnrivera7`)
5. **Submit the package once** — https://packagist.org/packages/submit  
   - Repository URL: `https://github.com/Johnrivera7/filament-transbank-webpay`
6. **GitHub Service Hook** — Packagist will ask to install the Packagist GitHub app / webhook so every push/tag auto-updates

After that:

```bash
composer require johnrivera7/filament-transbank-webpay
```

## Optional: Packagist API token

If you want CLI submit later: Packagist → Profile → Settings → API tokens.

## Filament directory form

- Composer package: `johnrivera7/filament-transbank-webpay`
- Versions: **4.x** and **5.x**
- Documentation URL: `https://raw.githubusercontent.com/Johnrivera7/filament-transbank-webpay/main/README.md`
