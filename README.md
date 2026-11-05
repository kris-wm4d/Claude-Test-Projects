# PPCv4 Dental Site with Settings Dashboard

A PHP version of the Home and Financing pages. Office details, phone, location, map, form email, reCAPTCHA v3 and Mailgun are all edited from a password-protected dashboard at **/admin.php**.

## Requirements

- PHP 8.1 or newer with the `curl` extension (standard on cPanel hosts)
- HTTPS on the domain (needed for secure admin cookies and reCAPTCHA)
- Apache, which reads the included `.htaccess` files. On Nginx, see "Nginx" below.

## Install

1. Upload everything in this folder to the site's web root (e.g. `public_html`), including the hidden `.htaccess` files.
2. Make the `data` folder writable by PHP. On most cPanel hosts the default permissions already work. If the dashboard says "Data folder not writable", set `data` to `750` (or `755`) and make sure it's owned by the account PHP runs as.
3. Go to `https://yourdomain.com/admin.php` and create the admin password. Do this right after uploading: until a password exists, whoever opens admin.php first can set it.
4. Fill in the settings and click **Save settings**.

## Settings

| Section | Fields | Where it shows |
|---|---|---|
| Office & doctor | Office name, doctor's name, credentials | Header logo alt text, welcome, doctor section, page titles, form consent text, email "from" name |
| Phone | Display number, phone link (`tel:`) | Header, Call/Text buttons, mobile bar, form, error messages. The link can be a tracking number that differs from the display number. |
| Location & map | Address lines, Location URL, Google Map embed code | Visit section, Get directions link, map |
| Contact form | Admin email(s), subject | Where appointment requests are sent |
| reCAPTCHA v3 | Site key, secret key, minimum score | Spam check on every form submission |
| Mailgun | Region, sending domain, API key, from email, from name | Sends the form emails |

**Map embed:** in Google Maps open the practice, click Share → Embed a map → Copy HTML, and paste it. Only the Google Maps address inside the code is kept, so pasted code can't add anything else to the page.

**Secret keys** (reCAPTCHA secret, Mailgun API key) are never sent to the browser. They show as "Saved, ends in 1234". Leave the field blank to keep the saved key, or tick "Remove the saved key".

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
admin.php                  settings dashboard
submit.php                 form handler (reCAPTCHA v3 + Mailgun)
inc/bootstrap.php          settings, escaping, sessions, validation
inc/services.php           reCAPTCHA and Mailgun API calls
assets/                    styles.css, site.js, logo.webp, hero.webp
data/                      created settings + password (keep private)
```
