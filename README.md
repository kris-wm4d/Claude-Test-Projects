# Primary PPC Dental Website

A responsive dental website with a static homepage, PHP inner pages, and a password-protected settings dashboard at **/admin.php**.

## Requirements

- PHP 8.1 or newer with the `curl` extension (standard on cPanel hosts)
- HTTPS on the domain (needed for secure admin cookies and reCAPTCHA)
- Apache, which reads the included `.htaccess` files. On Nginx, see "Nginx" below.

## Install

1. Upload everything in this folder to the site's web root (e.g. `public_html`), including the hidden `.htaccess` files.
2. Make the `data` folder writable by PHP. On most cPanel hosts the default permissions already work. If the dashboard says "Data folder not writable", set `data` to `750` (or `755`) and make sure it's owned by the account PHP runs as.
3. Go to `https://yourdomain.com/admin.php` and create the admin password. Do this right after uploading: until a password exists, whoever opens admin.php first can set it.
4. Fill in the settings and click **Save settings**.

## Static GitHub homepage

GitHub Pages cannot execute PHP. Generate the static homepage and Dental Implants page with `php scripts/build-static.php`, then commit the generated `index.html` files. Links to other inner `.php` pages work only on PHP-enabled hosting.

The static homepage uses the public defaults in `inc/bootstrap.php`; generation stops if local `data/settings.php` exists. Appointment requests on the static homepage show a phone fallback because static hosting cannot run `submit.php`.

To customize the static homepage, update the public defaults in `inc/bootstrap.php` before building. The settings dashboard is available only on PHP-enabled hosting.

All public pages share the homepage header and footer through `inc/site-header.php` and `inc/site-footer.php`. The shared footer includes the office address, hours, photos, and map link. The header logo is styled at 60px tall.

## Settings

| Section | Fields | Where it shows |
|---|---|---|
| Site info | Site name, reporting email, reCAPTCHA v3 keys, WhatConverts, Analytics, chat/header/footer scripts | Site name is used throughout the public site. Reporting email receives appointment requests. WhatConverts and Analytics values are stored only. Script fields are inserted into the public pages at their named positions. |
| Practice info | Repeatable practice name, location, directions URL, phone number and URL, description, office hours | Every saved practice is shown in the shared Visit section with a Google Maps embed generated from its Location. The first practice supplies the site-wide call number and directions fallback. |
| Office & doctor | Doctor's name, credentials | Doctor section, form consent text and email "from" name |
| Contact form | Subject | Appointment request email subject |
| reCAPTCHA v3 | Minimum score (keys are under Site info) | Spam check on every form submission |
| Mailgun | Region, sending domain, API key, from email, from name | Sends the form emails |

**Practice maps:** each map is embedded from its practice's Location using a Google Maps search URL; a separate iframe or embed snippet is not needed.

**Secret keys** (reCAPTCHA secret, Mailgun API key) are never sent to the browser. They show as "Saved, ends in 1234". Leave the field blank to keep the saved key, or tick "Remove the saved key". Header, chat and footer script fields execute on public pages; only trusted administrators should edit them.

## Mailgun setup

1. In Mailgun, add and verify a sending domain (e.g. `mg.yourpractice.com`) with the DNS records Mailgun gives you.
2. Create a Sending API key for that domain (Domain settings → Sending API keys). It can only send mail, which limits the damage if it ever leaks.
3. In the dashboard choose the region your Mailgun account uses (US or EU), enter the domain and key, and use a from address on that domain or its parent domain.
4. Save, then click **Send test email**.

Each form email has the patient's address as Reply-To, so the front desk can reply directly. Mailgun sandbox domains only deliver to "authorized recipients" you've added in Mailgun.

## reCAPTCHA v3 setup

1. Go to https://www.google.com/recaptcha/admin/create, choose **Score based (v3)**, and add your domain.
2. Paste the site key and secret key into the dashboard and save.
3. Leave the minimum score at 0.5 unless real patients are being blocked (lower it) or spam gets through (raise it).

If reCAPTCHA is left empty, the form still works and relies on a hidden honeypot field and rate limiting.

## How the form works

`submit.php` runs these checks in order: same-site request → honeypot → max 5 submissions per 10 minutes per visitor → required fields → reCAPTCHA v3 (action `appointment`, score ≥ minimum) → send through Mailgun. If Mailgun isn't set up or fails, the visitor is told to call the office number. Failures are logged to `data/form.log` with no patient details.

## Security notes

- `data/` holds `settings.php` (all settings including keys) and `auth.php` (password hash). Both are PHP files, so a web request returns nothing, and `data/.htaccess` blocks the folder.
- Admin login is limited to 5 attempts per 15 minutes per IP, sessions expire after 2 hours idle, and every admin form has a CSRF token.
- To reset a lost admin password, delete `data/auth.php` over FTP or the file manager and open admin.php again to set a new one. Your settings are kept.
- Back up `data/settings.php` when you back up the site.

## Nginx

Add rules equivalent to the `.htaccess` files:

```nginx
location ~ ^/(data|inc)/ { deny all; }
location ~ /\.  { deny all; }
location ~ \.(md|log|json)$ { deny all; }
```

## Files

```
index.php, financing.php   public pages (read settings)
implants/dental-implants/  dental implant service page at /implants/dental-implants/
admin.php                  settings dashboard
submit.php                 form handler (reCAPTCHA v3 + Mailgun)
inc/bootstrap.php          settings, escaping, sessions, validation
inc/site-header.php        shared site navigation and masthead
inc/site-footer.php        shared site footer
inc/services.php           reCAPTCHA and Mailgun API calls
assets/                    styles.css, implants.css, implants.js, site.js
Uploads/2026/              Site images and other media for 2026
data/                      created settings + password (keep private)
```
