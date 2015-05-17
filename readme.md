## Chatanoo Core

### Install

Use composer to install dependencies

```
composer install
```

### Deployment

#### Initialize

Chatanoo is hosted on Elastic Beanstalk, you need to use `eb` command line tool to deploy the project (http://docs.aws.amazon.com/elasticbeanstalk/latest/dg/eb-cli3-install.html)

```
eb init
```

Choose `chatanoo-core` application and `chatanoo-core-staging` as environment.

#### Staging

```
eb deploy
```

#### Production

```
eb deploy chatanoo-core
```