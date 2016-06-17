# Menu pro Nette Framework
Nastavení v **config.neon**
```neon
extensions:
    - NAttreid\Menu\DI\MenuExtension

menu:
    items:
        crm:
            data:
                link: 'link'                            # link na HP modulu
                group:                                  # skupina
                    page:                               # konkretni stranka
                        link: 'linkToPage'              # link na stranku
                        arguments: ['name': 'value']    # argumenty
                        toBlank: TRUE                   # otevre do noveho okna
```

## Menu modulů a submenu
```php
/** @var \NAttreid\Menu\Module\IMenuFactory @inject */
public $menuFactory;

protected function createComponentModuleMenu() {
    $moduleMenu = $this->menuFactory->create('crm');
    $moduleMenu->setBaseUrl('nazev', 'link');
    return $moduleMenu;
}

protected function createComponentMenu() {
    $menu = $this['moduleMenu']->createMenu($this->namespace); // nastaven v BasePresenter pro každý modul
    return $menu;
}
```

## Pouze menu
```php
/** @var \NAttreid\Menu\Menu\IMenuFactory @inject */
public $menuFactory;

protected function createComponentMenu() {
    $menu = $this['moduleMenu']->createMenu('namespace'); // nastaven v BasePresenter pro každý modul
    return $menu;
}
```

Nastaveni počtu za položku v menu
```php
$menu = $this['menu'];
$menu->setCount('nazev', 5, \NAttreid\Menu\Menu\Item::INFO);
```

## Drobečková navigace
```php
protected function createComponentBreadcrumb() {
    $breadcrumb = $this['menu']->createBreadcrumb();
    $breadcrumb->setDelimiter('»');
    return $breadcrumb;
}
```

Nebo samostatně
```php
/** @var NAttreid\Menu\Breadcrumb\IBreadcrumb @inject */
public $breadcrumbFactory;

protected function createComponentBreadcrumbs() {
    $control = $this->breadcrumbFactory->create();
    return $control;
}
```

Přidání linku
```php
$this['breadcrumb']->addLink('nazev', 'link');
```