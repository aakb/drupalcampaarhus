Drupal Camp Aarhus
==================

Get stuff:

```
git clone https://github.com/aakb/drupalcampaarhus.git htdocs
cd htdocs
composer install
```

Install Drupal:

```
drush --yes site-install itkore --db-url='mysql://root@localhost/drupalcampaarhus' --site-name='drupalcampaarhus.dk'
```

Enable the *Drupal Camp Aarhus* theme:

```
drush --yes pm-enable drupalcampaarhus_theme
drush --yes config-set system.theme default drupalcampaarhus_theme
drush cache-rebuild
```
