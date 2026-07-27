app/
└── Core/
    └── Navigation/
        │
        ├── Contracts/
        │   ├── NavigationBuilderInterface.php
        │   ├── NavigationRegistryInterface.php
        │   ├── NavigationRendererInterface.php
        │   ├── BreadcrumbBuilderInterface.php
        │   └── ActiveMenuResolverInterface.php
        │
        ├── DTO/
        │   ├── NavigationGroupData.php
        │   ├── NavigationItemData.php
        │   ├── NavigationTreeData.php
        │   └── BreadcrumbData.php
        │
        ├── Builders/
        │   └── NavigationBuilder.php
        │
        ├── Registry/
        │   └── NavigationRegistry.php
        │
        ├── Renderers/
        │   ├── SidebarRenderer.php
        │   ├── TopbarRenderer.php
        │   └── BreadcrumbRenderer.php
        │
        ├── Resolvers/
        │   ├── ActiveMenuResolver.php
        │   ├── MenuResolver.php
        │   └── PermissionResolver.php
        │
        ├── Support/
        │   ├── NavigationSorter.php
        │   ├── NavigationTreeBuilder.php
        │   ├── NavigationCollection.php
        │   └── IconResolver.php
        │
        ├── Exceptions/
        │   ├── DuplicateNavigationException.php
        │   ├── InvalidNavigationException.php
        │   └── NavigationNotFoundException.php
        │
        └── NavigationServiceProvider.php
