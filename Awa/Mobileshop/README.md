# Mage2 Module Awa Mobileshop

    ``awa/module-mobileshop``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
this extention use for rest api wishlist, review

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Awa`
 - Enable the module by running `php bin/magento module:enable Awa_Mobileshop`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require awa/module-mobileshop`
 - enable the module by running `php bin/magento module:enable Awa_Mobileshop`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration




## Specifications

 - API Endpoint
	- GET - Awa\Mobileshop\Api\WishlistManagementInterface > Awa\Mobileshop\Model\WishlistManagement

 - API Endpoint
	- POST - Awa\Mobileshop\Api\WishlistManagementInterface > Awa\Mobileshop\Model\WishlistManagement

 - API Endpoint
	- DELETE - Awa\Mobileshop\Api\WishlistManagementInterface > Awa\Mobileshop\Model\WishlistManagement

 - API Endpoint
	- PUT - Awa\Mobileshop\Api\WishlistManagementInterface > Awa\Mobileshop\Model\WishlistManagement


## Attributes



