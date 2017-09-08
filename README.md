Getting Started With Chaplean Mailer Bundle
===========================================

# Prerequisites

This version of the bundle requires Symfony 2.8+.

# Installation

## 1. Composer

```bash
composer require chaplean/mailer-bundle
```

## 2. Register bundle

Add in `AppKernel`:
```php
new Chaplean\Bundle\MailerBundle\ChapleanMailerBundle(),
```

## 3. Configuration

Include configuration in `config.yml`

```yaml
chaplean_mailer:
    bcc_address: '<email_reference>'
    bounce_address: '<email_reference>'
    sender_address: '<no_reply_email>'
    sender_name: '<sender_name>'
    subject:
        prefix: '<prefix>'
    test: false
    disabled_email_extensions: ['<domain>'] # default empty
    amazon_tags: # optional, add amazon tag in header message
        configuration_set: <> # required, use in X-SES-CONFIGURATION-SET
        project_name: <project_name> # required, use in X-SES-MESSAGE-TAGS
        env: <environement> # required, use in X-SES-MESSAGE-TAGS
```

##### *Note*:

SwiftmailerBundle configuration is included by this bundle

```yaml
# Swiftmailer Configuration
swiftmailer:
    transport:  '%mailer_transport%'
    host:       '%mailer_host%'
    port:       '%mailer_port%'
    username:   '%mailer_user%'
    password:   '%mailer_password%'
    encryption: '%mailer_encryption%'
    spool:      { type: memory }
```

