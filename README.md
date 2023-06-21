# Pimcore Project app-commerce

This skeleton should be used by experienced Pimcore developers for starting a new project from the ground up. 
If you are new to Pimcore, it's better to start with our demo package, listed below 😉

## Getting started

- Clone repository 
- Open in fork 
- Create new blank DataBase
  - `mysql -u root -p -e "CREATE DATABASE app-ecommerce charset=utf8mb4;"`
- Install Pimcore on project
  - `./vendor/bin/pimcore-install --admin-username admin --admin-password admin  --mysql-username root --mysql-password --mysql-database app-ecommerce --no-interaction `
- Point your virtual host to `app-commerce/public`
- Point local host to `127.0.0.1   app-commerce.loc`
- Open http://app-commerce.loc/admin/ in your browser
- After instalation run script `\var\scripts\recreate`


## Installing e-commerce bundle framework
```bash
php bin/console pimcore:bundle:install PimcoreEcommerceFrameworkBundle
php bin/console p:b:l

##este vai criar/atualizar tabela bd
php bin/console ecommerce:indexservice:bootstrap --create-or-update-index-structure --object-list-class \Pimcore\Model\DataObject\Product\Listing

##preenche/atualiza tabela com dados existentes
php bin/console ecommerce:indexservice:bootstrap --update-index --object-list-class \Pimcore\Model\DataObject\Product\Listing
```
- Done! 😎

## Other demo/skeleton packages
- [Pimcore Basic Demo](https://github.com/pimcore/demo)