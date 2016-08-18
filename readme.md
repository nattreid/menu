# Menu pro Nette Framework

## Nastavení v **config.neon**
Pokud chcete využít nastavení menu přes **config.neon**
```neon
extensions:
    - NAttreid\Menu\DI\MenuExtension

menu:
    items:
        namespace:
            data:                                       # namespace modulu
                link: 'Homepage:'                       # link na HP modulu
                group:                                  # skupina
                    page:                               # konkretni presenter
                        link: 'action'                  # link akci presenteru
                        arguments: ['name': 'value']    # argumenty
                        toBlank: TRUE                   # otevre do noveho okna
```

nebo postačí pouze zaregistrovat továrnu

```neon
services:
    - NAttreid\Menu\IMenuFactory
```

## Použití
```php
/** @var \NAttreid\Menu\IMenuFactory @inject */
public $menuFactory;

function createComponentMenu() {
    $menu = $this->menuFactory->create('namespace');

    $link = $menu->addLink('test', 'Test:test');
    $group = $link->addGroup('group');
    // ... atd 

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
    $breadcrumb = $this['menu']->getBreadcrumb();
    return $breadcrumb;
}
```

Nebo samostatně
```php

protected function createComponentBreadcrumbs() {
    $control = new Breadcrumb;
    $control->addLink('name', 'Link:action');
    return $control;
}
```