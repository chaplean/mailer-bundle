Getting Started With Chaplean Mailer Bundle
===========================================

# Prerequisites

This version of the bundle requires Symfony 2.8+.

# Installation

## 1. Composer

```
composer require chaplean/mailer-bundle
```

## 2. AppKernel.php

Add
```
new Chaplean\Bundle\MailerBundle\ChapleanMailerBundle(),
```

## 3. config.yml

##### A. Import

    - { resource: '@ChapleanMailerBundle/Resources/config/config.yml' }

##### B. Configuration

```
chaplean_mailer:
    bcc_address: '<email_reference>'
    bounce_address: '<email_reference>'
    sender_address: '<no_reply_email>'
    sender_name: '<sender_name>'
    subject:
        prefix: '<prefix>'
    test: false
```
