<?php

namespace App\Helpers;

class MenuHelper
{
    public static function getMainNavItems()
    {
        return [
            [
                'icon' => 'dashboard',
                'name' => 'Dashboard',
                'path' => '/admin',
            ],
            [
                'icon' => 'box',
                'name' => 'Catalog',
                'path' => '/admin/catalogs',
                'subItems' => [
                    ['name' => 'Products', 'path' => '/admin/products'],
                    ['name' => 'Brands', 'path' => '/admin/brands'],
                    ['name' => 'Catalogs', 'path' => '/admin/catalogs'],
                    ['name' => 'Catalog Types', 'path' => '/admin/catalogs/types'],
                    ['name' => 'Attributes', 'path' => '/admin/attributes'],
                ],
            ],
            [
                'icon' => 'shopping-cart',
                'name' => 'Sales',
                'subItems' => [
                    ['name' => 'Orders', 'path' => '/admin/orders'],
                    ['name' => 'Coupons', 'path' => '/admin/coupons'],
                    ['name' => 'Flash Sales', 'path' => '/admin/flash-sales'],
                ],
            ],
            [
                'icon' => 'users',
                'name' => 'Customers',
                'subItems' => [
                    ['name' => 'All Customers', 'path' => '/admin/customers'],
                    ['name' => 'Loyalty Program', 'path' => '/admin/loyalty'],
                    ['name' => 'Reviews', 'path' => '/admin/reviews'],
                ],
            ],
            [
                'icon' => 'inventory',
                'name' => 'Inventory',
                'subItems' => [
                    ['name' => 'Stock Overview', 'path' => '/admin/inventory'],
                    ['name' => 'Purchase Batches', 'path' => '/admin/purchase-batches'],
                    ['name' => 'Stock Movements', 'path' => '/admin/inventory-movements'],
                    ['name' => 'Low Stock Alerts', 'path' => '/admin/inventory/alerts'],
                ],
            ],
            [
                'icon' => 'document',
                'name' => 'Content',
                'subItems' => [
                    ['name' => 'CMS Pages', 'path' => '/admin/cms/pages'],
                ],
            ],
            [
                'icon' => 'chart',
                'name' => 'Reports',
                'subItems' => [
                    ['name' => 'Sales Report', 'path' => '/admin/reports/sales'],
                    ['name' => 'Products Report', 'path' => '/admin/reports/products'],
                    ['name' => 'Batch Stock Report', 'path' => '/admin/reports/batch-stock'],
                    ['name' => 'Expiring Items', 'path' => '/admin/reports/expiring'],
                    ['name' => 'Product Profit', 'path' => '/admin/reports/product-profit'],
                    ['name' => 'Category Profit', 'path' => '/admin/reports/category-profit'],
                    ['name' => 'Partner Report', 'path' => '/admin/reports/partners'],
                    ['name' => 'Partner Contribution', 'path' => '/admin/reports/partner-contribution'],
                    ['name' => 'Investor ROI', 'path' => '/admin/reports/investor-roi'],
                    ['name' => 'Expense Report', 'path' => '/admin/reports/expenses'],
                    ['name' => 'Investment Report', 'path' => '/admin/reports/investments'],
                    ['name' => 'Profit & Loss', 'path' => '/admin/reports/profit-loss'],
                    ['name' => 'Financial Summary', 'path' => '/admin/reports/financial-summary'],
                    ['name' => 'Cost History', 'path' => '/admin/reports/cost-history'],
                ],
            ],
            [
                'icon' => 'wallet',
                'name' => 'Finance',
                'subItems' => [
                    ['name' => 'Expenses', 'path' => '/admin/expenses'],
                    ['name' => 'Partners', 'path' => '/admin/partners'],
                    ['name' => 'Investments', 'path' => '/admin/investments'],
                    ['name' => 'Capital Accounts', 'path' => '/admin/capital-accounts'],
                    ['name' => 'Transactions', 'path' => '/admin/financial-transactions'],
                ],
            ],
            [
                'icon' => 'settings',
                'name' => 'Settings',
                'subItems' => [
                    ['name' => 'Roles', 'path' => '/admin/roles'],
                    ['name' => 'Permissions', 'path' => '/admin/permissions'],
                    ['name' => 'General Settings', 'path' => '/admin/settings'],
                ],
            ],
        ];
    }

    public static function getMenuGroups()
    {
        return [
            [
                'title' => 'Main',
                'items' => self::getMainNavItems()
            ],
        ];
    }

    /**
     * Get current request path (cleaned)
     */
    public static function getCurrentPath()
    {
        return trim(request()->path(), '/');
    }

    /**
     * Check if a path exactly matches the current path
     */
    public static function isExactActive($path)
    {
        return self::getCurrentPath() === trim($path, '/');
    }

    /**
     * Check if current path is a child of the given path
     * e.g., /admin/catalogs/types is a child of /admin/catalogs
     */
    public static function isChildActive($path)
    {
        $currentPath = self::getCurrentPath();
        $cleanPath = trim($path, '/');

        // Check if current path starts with this path followed by a slash
        return $cleanPath !== '' && str_starts_with($currentPath, $cleanPath . '/');
    }

    /**
     * Check if parent menu should be active (child is active)
     */
    public static function isParentActive($subItems)
    {
        foreach ($subItems as $subItem) {
            // Parent is active if any subitem is exactly matched
            // or if any subitem's child path is matched
            if (self::isExactActive($subItem['path'])) {
                return true;
            }
            // Also check if current path is a child of this subitem's path
            if (self::isChildActive($subItem['path'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the submenu keys that should be open based on current path
     */
    public static function getOpenSubmenus($menuGroups)
    {
        $openSubmenus = [];

        foreach ($menuGroups as $groupIndex => $menuGroup) {
            foreach ($menuGroup['items'] as $itemIndex => $item) {
                if (isset($item['subItems'])) {
                    // Open submenu if parent is active
                    if (self::isParentActive($item['subItems'])) {
                        $openSubmenus[] = $groupIndex . '-' . $itemIndex;
                    }
                }
            }
        }

        return $openSubmenus;
    }

    /**
     * Get which submenu item is exactly active
     */
    public static function getActiveSubmenuItem($menuGroups)
    {
        foreach ($menuGroups as $groupIndex => $menuGroup) {
            foreach ($menuGroup['items'] as $itemIndex => $item) {
                if (isset($item['subItems'])) {
                    foreach ($item['subItems'] as $subItem) {
                        if (self::isExactActive($subItem['path'])) {
                            return [
                                'groupIndex' => $groupIndex,
                                'itemIndex' => $itemIndex,
                                'path' => $subItem['path']
                            ];
                        }
                    }
                }
            }
        }
        return null;
    }

    /**
     * Get icon SVG by name
     */
    public static function getIconSvg($iconName)
    {
        $icons = [
            'dashboard' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z" fill="currentColor"></path></svg>',

            'ai-assistant' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18.75 2.42969V7.70424M9.42261 13.673C10.0259 14.4307 10.9562 14.9164 12 14.9164C13.0438 14.9164 13.9742 14.4307 14.5775 13.673M20 12V18.5C20 19.3284 19.3284 20 18.5 20H5.5C4.67157 20 4 19.3284 4 18.5V12C4 7.58172 7.58172 4 12 4C16.4183 4 20 7.58172 20 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M18.75 2.42969V2.43969M9.50391 9.875L9.50391 9.885M14.4961 9.875V9.885" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>',

            'ecommerce' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.31641 4H3.49696C4.24468 4 4.87822 4.55068 4.98234 5.29112L5.13429 6.37161M5.13429 6.37161L6.23641 14.2089C6.34053 14.9493 6.97407 15.5 7.72179 15.5L17.0833 15.5C17.6803 15.5 18.2205 15.146 18.4587 14.5986L21.126 8.47023C21.5572 7.4795 20.8312 6.37161 19.7507 6.37161H5.13429Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M7.7832 19.5H7.7932M16.3203 19.5H16.3303" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>',

            'calendar' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M8 2C8.41421 2 8.75 2.33579 8.75 2.75V3.75H15.25V2.75C15.25 2.33579 15.5858 2 16 2C16.4142 2 16.75 2.33579 16.75 2.75V3.75H18.5C19.7426 3.75 20.75 4.75736 20.75 6V9V19C20.75 20.2426 19.7426 21.25 18.5 21.25H5.5C4.25736 21.25 3.25 20.2426 3.25 19V9V6C3.25 4.75736 4.25736 3.75 5.5 3.75H7.25V2.75C7.25 2.33579 7.58579 2 8 2ZM8 5.25H5.5C5.08579 5.25 4.75 5.58579 4.75 6V8.25H19.25V6C19.25 5.58579 18.9142 5.25 18.5 5.25H16H8ZM19.25 9.75H4.75V19C4.75 19.4142 5.08579 19.75 5.5 19.75H18.5C18.9142 19.75 19.25 19.4142 19.25 19V9.75Z" fill="currentColor"></path></svg>',

            'user-profile' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 3.5C7.30558 3.5 3.5 7.30558 3.5 12C3.5 14.1526 4.3002 16.1184 5.61936 17.616C6.17279 15.3096 8.24852 13.5955 10.7246 13.5955H13.2746C15.7509 13.5955 17.8268 15.31 18.38 17.6167C19.6996 16.119 20.5 14.153 20.5 12C20.5 7.30558 16.6944 3.5 12 3.5ZM17.0246 18.8566V18.8455C17.0246 16.7744 15.3457 15.0955 13.2746 15.0955H10.7246C8.65354 15.0955 6.97461 16.7744 6.97461 18.8455V18.856C8.38223 19.8895 10.1198 20.5 12 20.5C13.8798 20.5 15.6171 19.8898 17.0246 18.8566ZM2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12ZM11.9991 7.25C10.8847 7.25 9.98126 8.15342 9.98126 9.26784C9.98126 10.3823 10.8847 11.2857 11.9991 11.2857C13.1135 11.2857 14.0169 10.3823 14.0169 9.26784C14.0169 8.15342 13.1135 7.25 11.9991 7.25ZM8.48126 9.26784C8.48126 7.32499 10.0563 5.75 11.9991 5.75C13.9419 5.75 15.5169 7.32499 15.5169 9.26784C15.5169 11.2107 13.9419 12.7857 11.9991 12.7857C10.0563 12.7857 8.48126 11.2107 8.48126 9.26784Z" fill="currentColor"></path></svg>',

            'task' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.75586 5.50098C7.75586 5.08676 8.09165 4.75098 8.50586 4.75098H18.4985C18.9127 4.75098 19.2485 5.08676 19.2485 5.50098L19.2485 15.4956C19.2485 15.9098 18.9127 16.2456 18.4985 16.2456H8.50586C8.09165 16.2456 7.75586 15.9098 7.75586 15.4956V5.50098ZM8.50586 3.25098C7.26322 3.25098 6.25586 4.25834 6.25586 5.50098V6.26318H5.50195C4.25931 6.26318 3.25195 7.27054 3.25195 8.51318V18.4995C3.25195 19.7422 4.25931 20.7495 5.50195 20.7495H15.4883C16.7309 20.7495 17.7383 19.7421 17.7383 18.4995L17.7383 17.7456H18.4985C19.7411 17.7456 20.7485 16.7382 20.7485 15.4956L20.7485 5.50097C20.7485 4.25833 19.7411 3.25098 18.4985 3.25098H8.50586ZM16.2383 17.7456H8.50586C7.26322 17.7456 6.25586 16.7382 6.25586 15.4956V7.76318H5.50195C5.08774 7.76318 4.75195 8.09897 4.75195 8.51318V18.4995C4.75195 18.9137 5.08774 19.2495 5.50195 19.2495H15.4883C15.9025 19.2495 16.2383 18.9137 16.2383 18.4995L16.2383 17.7456Z" fill="currentColor"></path></svg>',

            'forms' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H18.5001C19.7427 20.75 20.7501 19.7426 20.7501 18.5V5.5C20.7501 4.25736 19.7427 3.25 18.5001 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H18.5001C18.9143 4.75 19.2501 5.08579 19.2501 5.5V18.5C19.2501 18.9142 18.9143 19.25 18.5001 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V5.5ZM6.25005 9.7143C6.25005 9.30008 6.58583 8.9643 7.00005 8.9643L17 8.96429C17.4143 8.96429 17.75 9.30008 17.75 9.71429C17.75 10.1285 17.4143 10.4643 17 10.4643L7.00005 10.4643C6.58583 10.4643 6.25005 10.1285 6.25005 9.7143ZM6.25005 14.2857C6.25005 13.8715 6.58583 13.5357 7.00005 13.5357H17C17.4143 13.5357 17.75 13.8715 17.75 14.2857C17.75 14.6999 17.4143 15.0357 17 15.0357H7.00005C6.58583 15.0357 6.25005 14.6999 6.25005 14.2857Z" fill="currentColor"></path></svg>',

            'tables' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.25 5.5C3.25 4.25736 4.25736 3.25 5.5 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V18.5C20.75 19.7426 19.7426 20.75 18.5 20.75H5.5C4.25736 20.75 3.25 19.7426 3.25 18.5V5.5ZM5.5 4.75C5.08579 4.75 4.75 5.08579 4.75 5.5V8.58325L19.25 8.58325V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H5.5ZM19.25 10.0833H15.416V13.9165H19.25V10.0833ZM13.916 10.0833L10.083 10.0833V13.9165L13.916 13.9165V10.0833ZM8.58301 10.0833H4.75V13.9165H8.58301V10.0833ZM4.75 18.5V15.4165H8.58301V19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5ZM10.083 19.25V15.4165L13.916 15.4165V19.25H10.083ZM15.416 19.25V15.4165H19.25V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15.416Z" fill="currentColor"></path></svg>',

            'pages' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.50391 4.25C8.50391 3.83579 8.83969 3.5 9.25391 3.5H15.2777C15.4766 3.5 15.6674 3.57902 15.8081 3.71967L18.2807 6.19234C18.4214 6.333 18.5004 6.52376 18.5004 6.72268V16.75C18.5004 17.1642 18.1646 17.5 17.7504 17.5H16.248V17.4993H14.748V17.5H9.25391C8.83969 17.5 8.50391 17.1642 8.50391 16.75V4.25ZM14.748 19H9.25391C8.01126 19 7.00391 17.9926 7.00391 16.75V6.49854H6.24805C5.83383 6.49854 5.49805 6.83432 5.49805 7.24854V19.75C5.49805 20.1642 5.83383 20.5 6.24805 20.5H13.998C14.4123 20.5 14.748 20.1642 14.748 19.75L14.748 19ZM7.00391 4.99854V4.25C7.00391 3.00736 8.01127 2 9.25391 2H15.2777C15.8745 2 16.4468 2.23705 16.8687 2.659L19.3414 5.13168C19.7634 5.55364 20.0004 6.12594 20.0004 6.72268V16.75C20.0004 17.9926 18.9931 19 17.7504 19H16.248L16.248 19.75C16.248 20.9926 15.2407 22 13.998 22H6.24805C5.00541 22 3.99805 20.9926 3.99805 19.75V7.24854C3.99805 6.00589 5.00541 4.99854 6.24805 4.99854H7.00391Z" fill="currentColor"></path></svg>',

            'charts' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M4.00002 12.0957C4.00002 7.67742 7.58174 4.0957 12 4.0957C16.4183 4.0957 20 7.67742 20 12.0957C20 16.514 16.4183 20.0957 12 20.0957H5.06068L6.34317 18.8132C6.48382 18.6726 6.56284 18.4818 6.56284 18.2829C6.56284 18.084 6.48382 17.8932 6.34317 17.7526C4.89463 16.304 4.00002 14.305 4.00002 12.0957ZM12 2.5957C6.75332 2.5957 2.50002 6.849 2.50002 12.0957C2.50002 14.4488 3.35633 16.603 4.77303 18.262L2.71969 20.3154C2.50519 20.5299 2.44103 20.8525 2.55711 21.1327C2.6732 21.413 2.94668 21.5957 3.25002 21.5957H12C17.2467 21.5957 21.5 17.3424 21.5 12.0957C21.5 6.849 17.2467 2.5957 12 2.5957ZM7.62502 10.8467C6.93467 10.8467 6.37502 11.4063 6.37502 12.0967C6.37502 12.787 6.93467 13.3467 7.62502 13.3467H7.62512C8.31548 13.3467 8.87512 12.787 8.87512 12.0967C8.87512 11.4063 8.31548 10.8467 7.62512 10.8467H7.62502ZM10.75 12.0967C10.75 11.4063 11.3097 10.8467 12 10.8467H12.0001C12.6905 10.8467 13.2501 11.4063 13.2501 12.0967C13.2501 12.787 12.6905 13.3467 12.0001 13.3467H12C11.3097 13.3467 10.75 12.787 10.75 12.0967ZM16.375 10.8467C15.6847 10.8467 15.125 11.4063 15.125 12.0967C15.125 12.787 15.6847 13.3467 16.375 13.3467H16.3751C17.0655 13.3467 17.6251 12.787 17.6251 12.0967C17.6251 11.4063 17.0655 10.8467 16.3751 10.8467H16.375Z" fill="currentColor"></path></svg>',

            'ui-elements' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.665 3.75618C11.8762 3.65061 12.1247 3.65061 12.3358 3.75618L18.7807 6.97853L12.3358 10.2009C12.1247 10.3064 11.8762 10.3064 11.665 10.2009L5.22014 6.97853L11.665 3.75618ZM4.29297 8.19199V16.0946C4.29297 16.3787 4.45347 16.6384 4.70757 16.7654L11.25 20.0365V11.6512C11.1631 11.6205 11.0777 11.5843 10.9942 11.5425L4.29297 8.19199ZM12.75 20.037L19.2933 16.7654C19.5474 16.6384 19.7079 16.3787 19.7079 16.0946V8.19199L13.0066 11.5425C12.9229 11.5844 12.8372 11.6207 12.75 11.6515V20.037ZM13.0066 2.41453C12.3732 2.09783 11.6277 2.09783 10.9942 2.41453L4.03676 5.89316C3.27449 6.27429 2.79297 7.05339 2.79297 7.90563V16.0946C2.79297 16.9468 3.27448 17.7259 4.03676 18.1071L10.9942 21.5857L11.3296 20.9149L10.9942 21.5857C11.6277 21.9024 12.3732 21.9024 13.0066 21.5857L19.9641 18.1071C20.7264 17.7259 21.2079 16.9468 21.2079 16.0946V7.90563C21.2079 7.05339 20.7264 6.27429 19.9641 5.89316L13.0066 2.41453Z" fill="currentColor"></path></svg>',

            'authentication' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M14 2.75C14 2.33579 14.3358 2 14.75 2C15.1642 2 15.5 2.33579 15.5 2.75V5.73291L17.75 5.73291H19C19.4142 5.73291 19.75 6.0687 19.75 6.48291C19.75 6.89712 19.4142 7.23291 19 7.23291H18.5L18.5 12.2329C18.5 15.5691 15.9866 18.3183 12.75 18.6901V21.25C12.75 21.6642 12.4142 22 12 22C11.5858 22 11.25 21.6642 11.25 21.25V18.6901C8.01342 18.3183 5.5 15.5691 5.5 12.2329L5.5 7.23291H5C4.58579 7.23291 4.25 6.89712 4.25 6.48291C4.25 6.0687 4.58579 5.73291 5 5.73291L6.25 5.73291L8.5 5.73291L8.5 2.75C8.5 2.33579 8.83579 2 9.25 2C9.66421 2 10 2.33579 10 2.75L10 5.73291L14 5.73291V2.75ZM7 7.23291L7 12.2329C7 14.9943 9.23858 17.2329 12 17.2329C14.7614 17.2329 17 14.9943 17 12.2329L17 7.23291L7 7.23291Z" fill="currentColor"></path></svg>',

            'chat' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M4.00002 12.0957C4.00002 7.67742 7.58174 4.0957 12 4.0957C16.4183 4.0957 20 7.67742 20 12.0957C20 16.514 16.4183 20.0957 12 20.0957H5.06068L6.34317 18.8132C6.48382 18.6726 6.56284 18.4818 6.56284 18.2829C6.56284 18.084 6.48382 17.8932 6.34317 17.7526C4.89463 16.304 4.00002 14.305 4.00002 12.0957ZM12 2.5957C6.75332 2.5957 2.50002 6.849 2.50002 12.0957C2.50002 14.4488 3.35633 16.603 4.77303 18.262L2.71969 20.3154C2.50519 20.5299 2.44103 20.8525 2.55711 21.1327C2.6732 21.413 2.94668 21.5957 3.25002 21.5957H12C17.2467 21.5957 21.5 17.3424 21.5 12.0957C21.5 6.849 17.2467 2.5957 12 2.5957ZM7.62502 10.8467C6.93467 10.8467 6.37502 11.4063 6.37502 12.0967C6.37502 12.787 6.93467 13.3467 7.62502 13.3467H7.62512C8.31548 13.3467 8.87512 12.787 8.87512 12.0967C8.87512 11.4063 8.31548 10.8467 7.62512 10.8467H7.62502ZM10.75 12.0967C10.75 11.4063 11.3097 10.8467 12 10.8467H12.0001C12.6905 10.8467 13.2501 11.4063 13.2501 12.0967C13.2501 12.787 12.6905 13.3467 12.0001 13.3467H12C11.3097 13.3467 10.75 12.787 10.75 12.0967ZM16.375 10.8467C15.6847 10.8467 15.125 11.4063 15.125 12.0967C15.125 12.787 15.6847 13.3467 16.375 13.3467H16.3751C17.0655 13.3467 17.6251 12.787 17.6251 12.0967C17.6251 11.4063 17.0655 10.8467 16.3751 10.8467H16.375Z" fill="currentColor"></path></svg>',

            'support-ticket' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 17.0518V12C20 7.58174 16.4183 4 12 4C7.58168 4 3.99994 7.58174 3.99994 12V17.0518M19.9998 14.041V19.75C19.9998 20.5784 19.3282 21.25 18.4998 21.25H13.9998M6.5 18.75H5.5C4.67157 18.75 4 18.0784 4 17.25V13.75C4 12.9216 4.67157 12.25 5.5 12.25H6.5C7.32843 12.25 8 12.9216 8 13.75V17.25C8 18.0784 7.32843 18.75 6.5 18.75ZM17.4999 18.75H18.4999C19.3284 18.75 19.9999 18.0784 19.9999 17.25V13.75C19.9999 12.9216 19.3284 12.25 18.4999 12.25H17.4999C16.6715 12.25 15.9999 12.9216 15.9999 13.75V17.25C15.9999 18.0784 16.6715 18.75 17.4999 18.75Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>',

            'email' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.5 8.187V17.25C3.5 17.6642 3.83579 18 4.25 18H19.75C20.1642 18 20.5 17.6642 20.5 17.25V8.18747L13.2873 13.2171C12.5141 13.7563 11.4866 13.7563 10.7134 13.2171L3.5 8.187ZM20.5 6.2286C20.5 6.23039 20.5 6.23218 20.5 6.23398V6.24336C20.4976 6.31753 20.4604 6.38643 20.3992 6.42905L12.4293 11.9867C12.1716 12.1664 11.8291 12.1664 11.5713 11.9867L3.60116 6.42885C3.538 6.38481 3.50035 6.31268 3.50032 6.23568C3.50028 6.10553 3.60577 6 3.73592 6H20.2644C20.3922 6 20.4963 6.10171 20.5 6.2286ZM22 6.25648V17.25C22 18.4926 20.9926 19.5 19.75 19.5H4.25C3.00736 19.5 2 18.4926 2 17.25V6.23398C2 6.22371 2.00021 6.2135 2.00061 6.20333C2.01781 5.25971 2.78812 4.5 3.73592 4.5H20.2644C21.2229 4.5 22 5.27697 22.0001 6.23549C22.0001 6.24249 22.0001 6.24949 22 6.25648Z" fill="currentColor"></path></svg>',

            'wallet' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21 18V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V5C3 3.89543 3.89543 3 5 3H19C20.1046 3 21 3.89543 21 5V6M12 12L16 12M12 12L10 14M16 12L18 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'box' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 7V4C20 2.89543 19.1046 2 18 2H6C4.89543 2 4 2.89543 4 4V7M20 7V19C20 20.1046 19.1046 21 18 21H6C4.89543 21 4 20.1046 4 19V7M20 7H4M10 12H10.01M14 12H14.01M8 12H8.01M16 12H16.01M12 12H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'shopping-cart' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 22C9.55228 22 10 21.5523 10 21C10 20.4477 9.55228 20 9 20C8.44772 20 8 20.4477 8 21C8 21.5523 8.44772 22 9 22Z" stroke="currentColor" stroke-width="2"/><path d="M20 22C20.5523 22 21 21.5523 21 21C21 20.4477 20.5523 20 20 20C19.4477 20 19 20.4477 19 21C19 21.5523 19.4477 22 20 22Z" stroke="currentColor" stroke-width="2"/><path d="M1 1H5L7.68 14.39C7.77144 14.8504 8.02191 15.264 8.38755 15.5583C8.75318 15.8526 9.2107 16.009 9.68 16H19.4C19.8693 16.009 20.3268 15.8526 20.6925 15.5583C21.0581 15.264 21.3086 14.8504 21.4 14.39L23 6H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'users' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17 21V19C17 16.7909 15.2091 15 13 15H5C2.79086 15 1 16.7909 1 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 11C11.2091 11 13 9.20914 13 7C13 4.79086 11.2091 3 9 3C6.79086 3 5 4.79086 5 7C5 9.20914 6.79086 11 9 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M23 21V19C22.9993 17.5456 22.4593 16.1415 21.4899 15.0325C20.5205 13.9234 19.1859 13.1829 17.7589 12.9524" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 3.26005C16.6744 3.41726 17.3069 3.70068 17.8709 4.09105C18.6986 4.68721 19.3608 5.49109 19.7959 6.42209" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'inventory' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21 7L13.5355 2.46447C12.6717 1.91666 11.3283 1.91666 10.4645 2.46447L3 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 7V15C3 16.1046 3.89543 17 5 17H7.66667M3 7H19M21 7H16.3333M7.66667 17V21M12 17V21M16.3333 17V21M12 17H16.3333M7.66667 17H12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M7.66667 13H12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'document' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 13H8M16 17H8M10 9H9H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'chart' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 20V10M12 20V4M6 20V14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'settings' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M19.4 15.0001C19.2569 15.5301 18.9804 16.0164 18.6004 16.4281C18.1804 16.8801 17.6554 17.2221 17.0704 17.4381L17.0504 17.4441C16.4904 17.6861 15.9404 17.9641 15.5304 18.3901C15.1639 18.7791 14.9715 19.2891 14.9804 19.8281C14.9804 20.6881 14.5304 21.4601 13.7604 21.8201C13.1804 22.0601 12.5404 22.0001 11.9604 21.7501C11.4604 21.4601 11.0904 20.9401 10.9604 20.3401C10.9604 20.3001 10.9604 20.2601 10.9604 20.2201C10.9703 19.7011 10.8804 19.1851 10.7004 18.6921C10.5104 18.1781 10.2204 17.7101 9.84041 17.3261C9.43041 16.8981 8.85041 16.6341 8.23041 16.4901L8.21041 16.4851C7.63041 16.3391 7.06041 16.0621 6.68041 15.6761C6.35041 15.3441 6.08041 14.9481 5.88041 14.5001L5.84041 14.4501C5.60041 13.9601 5.53041 13.4101 5.64041 12.8701L5.67041 12.8101C5.86041 12.2001 6.23041 11.6801 6.73041 11.3301C7.23041 10.9801 7.83041 10.8301 8.42041 10.9101L8.48041 10.9201C9.00041 10.9801 9.51041 11.1201 9.97041 11.3501L10.0104 11.3701C10.2604 11.5101 10.4904 11.6801 10.7004 11.8801C10.9004 12.0701 11.0604 12.2901 11.1804 12.5301L11.2004 12.5701C11.3204 12.8101 11.4004 13.0601 11.4404 13.3201L11.4504 13.3801C11.4804 13.6301 11.4704 13.8801 11.4204 14.1301L11.4104 14.1901C11.3704 14.3701 11.2904 14.5401 11.1904 14.6901L11.1704 14.7201C11.0604 14.8801 10.9204 15.0201 10.7604 15.1401L10.7204 15.1601C10.5704 15.2801 10.4004 15.3601 10.2204 15.4101L10.1604 15.4201C9.91041 15.4701 9.66041 15.4601 9.42041 15.4101L9.36041 15.4001C9.14041 15.3501 8.94041 15.2501 8.77041 15.1101L8.73041 15.0901C8.56041 14.9401 8.43041 14.7601 8.34041 14.5601L8.32041 14.5201C8.23041 14.3201 8.18041 14.1001 8.17041 13.8801L8.16041 13.8201C8.15041 13.6001 8.18041 13.3901 8.25041 13.1801L8.27041 13.1401C8.34041 12.9301 8.45041 12.7401 8.59041 12.5701L8.61041 12.5501C8.76041 12.3701 8.94041 12.2201 9.14041 12.1101L9.26041 12.0401C9.48041 11.9101 9.72041 11.8301 9.97041 11.8001L10.0304 11.7901C10.2804 11.7601 10.5304 11.7801 10.7704 11.8501L10.8104 11.8701C11.0504 11.9501 11.2604 12.0801 11.4404 12.2601L11.4604 12.2801C11.6404 12.4401 11.7704 12.6401 11.8504 12.8601L11.8704 12.9201C11.9504 13.1401 11.9804 13.3801 11.9604 13.6201L11.9504 13.6801C11.9204 13.8501 11.8604 14.0101 11.7704 14.1601L11.7504 14.2001C11.6604 14.3501 11.5404 14.4801 11.4004 14.5801L11.3804 14.6001C11.2504 14.6801 11.1004 14.7401 10.9404 14.7701L10.8804 14.7801C10.7204 14.8001 10.5604 14.8001 10.4004 14.7801L10.3404 14.7701C10.1604 14.7401 9.99041 14.6801 9.84041 14.5901L9.80041 14.5701C9.64041 14.4701 9.50041 14.3501 9.38041 14.2001L9.36041 14.1601C9.24041 14.0001 9.15041 13.8201 9.09041 13.6301L9.08041 13.5701C9.02041 13.3701 9.00041 13.1601 9.01041 12.9501L9.02041 12.8901C9.04041 12.6901 9.09041 12.5001 9.17041 12.3201L9.19041 12.2801C9.27041 12.1001 9.38041 11.9401 9.51041 11.8001L9.53041 11.7801C9.67041 11.6201 9.83041 11.4901 10.0004 11.3901L10.0404 11.3701C10.2104 11.2501 10.4004 11.1701 10.6004 11.1301L10.6604 11.1201C10.8604 11.0801 11.0704 11.0801 11.2704 11.1301L11.3304 11.1401C11.5304 11.1901 11.7104 11.2801 11.8704 11.4101L11.9104 11.4301C12.0704 11.5601 12.2004 11.7201 12.3004 11.9001L12.3204 11.9401C12.4204 12.1201 12.4804 12.3201 12.5004 12.5301L12.5104 12.5901C12.5304 12.7901 12.5104 12.9901 12.4604 13.1801L12.4504 13.2401C12.4004 13.4201 12.3204 13.5801 12.2104 13.7201L12.1904 13.7601C12.0804 13.8801 11.9404 13.9801 11.7804 14.0501L11.7204 14.0701C11.5604 14.1301 11.3904 14.1501 11.2204 14.1501V15.0001Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        ];

        return $icons[$iconName] ?? '<svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="currentColor"/></svg>';
    }
}
