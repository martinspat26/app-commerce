# Pimcore Project app-commerce

This skeleton should be used by experienced Pimcore developers for starting a new project from the ground up. 
If you are new to Pimcore, it's better to start with our demo package, listed below 😉

## Getting started

- Clone repository 
- Open in fork 
- Create new blank DataBase
  - `mysql -u root -p < dump/app-commerce.sql`
- Install Pimcore on project
  - `./vendor/bin/pimcore-install --admin-username admin --admin-password admin  --mysql-username root --mysql-password --mysql-database app-commerce --no-interaction `
- Point your virtual host to `app-commerce/public`
- Point local host to `127.0.0.1   app-commerce.loc`
- Open http://app-commerce.loc/admin/ in your browser


## Installing e-commerce bundle framework
```bash
php bin/console pimcore:bundle:install PimcoreEcommerceFrameworkBundle
php bin/console p:b:l

```
- Done! 😎

## Other demo/skeleton packages
- [Pimcore Basic Demo](https://github.com/pimcore/demo)