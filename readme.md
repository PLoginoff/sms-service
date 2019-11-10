# sms-service

SMS sending service through N gates. Embeds in any messenger broker (via Symfony Messenger & Enqueue).
At the moment, the following gates:
 - Sms Intel (Russia);
 - Easy Sms (Russia);
 - fake (fake, only log).

Current algorithm:
  - if it wasn’t possible to send via first gate, then mark it temporarily as a non-working one and take the next one.

See `.env` for possible settings.

Installing:
 - `composer install`
 - `bin/simple-phpunit`
 - set up your logins & passwords in `.env.*`

Real tests — send sms to yourself:
 - `bin/console sms:send 9260613031 "Hi!" --gate=intel`
 - `bin/console sms:send 9260613031 "Hi!" --gate=easy`

Unit tests:
 - `bin/simple-phpunit`
 
Run consumer:
 - `bin/console messenger:consume sms.send -vv`

And send a very simple message to queue `sms.send` from another service:
 - `{"phone": "9260613031", "text": "Hi!"}`
