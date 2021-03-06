# Menu pro Nette Framework

## Nastavení v **config.neon**
Pokud chcete využít nastavení menu přes **config.neon**
```neon
extensions:
    menu: NAttreid\Menu\DI\MenuExtension

menu:
    items:
        module:                                                 # hlavni modul (front, cms)
            data:                                               # namespace modulu
                link: 'Homepage:'                               # link na HP modulu
                group:                                          # skupina
                    page:                                       # presenter
                        link: action                            # link akci presenteru, nebo null pro default
                        arguments: {name: value}                # argumenty
                        toBlank: TRUE                           # otevre do noveho okna
                        count: 5                                # pocet za linkem
                        # nebo
                        count: @SomeClass::countUnapproved()    # pocet za linkem
                        # nebo
                        count: {5, info}                        # muze byt info, warning (info je default)
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
    $menu = $this->menuFactory->create();

    $link = $menu->addLink('test', 'Test:test');
    $group = $link->addGroup('group');
    // ... atd 

    return $menu;
}
```

## Drobečková navigace
```php
protected function createComponentBreadcrumb() {
    $breadcrumb = $this['menu']->getBreadcrumb();
    return $breadcrumb;
}
```