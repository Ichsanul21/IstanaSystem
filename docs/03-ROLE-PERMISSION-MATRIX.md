# Role & Permission Matrix

> **Note:** Permission names in this matrix are functional descriptions. Actual permission names (dot notation) are defined in `database/seeders/RolePermissionSeeder.php`. For example, `view_users` maps to `user.read`, `create_users` to `user.create`, etc.

## Role Hierarchy

```
DEVELOPER (highest — system + business)
    │
    └── OWNER (view all, no technical)
            │
            └── SUPER ADMIN (full operational, cannot modify Owner/Developer)
                    │
                    └── BRANCH ADMIN (branch ops + finance + marketing)
                            │
                            ├── WORKSHOP ADMIN (production)
                            ├── CS (CRM + WA + order pickup)
                            ├── CASHIER (POS only)
                            └── WORKSHOP STAFF (update status only)
```

## Role Descriptions

| Role | Scope | Description |
|------|-------|-------------|
| **Developer** | Global | System configuration, technical settings, user management all levels, access all branches |
| **Owner** | Global | View all business reports, manage Super Admin & below, cannot modify system/technical settings |
| **Super Admin** | Global | Full operational control, manage Branch Admin & below, cannot modify Owner/Developer |
| **Branch Admin** | Per-Branch | Branch operations, finance, marketing, staff management |
| **Workshop Admin** | Per-Workshop | Production management, assign tasks, quality control |
| **CS** | Per-Branch or Central | CRM, WhatsApp, order pickup, customer service |
| **Cashier** | Per-Branch | POS only — create orders, receive payments, process refunds |
| **Workshop Staff** | Per-Workshop | Update production status via scanner only |

## Permission Grid

| Permission | DEV | OWN | SA | BA | WA | CS | CA | WS |
|-----------|:---:|:---:|:--:|:--:|:--:|:--:|:--:|:--:|
| **User Management** | | | | | | | | |
| view_users | ✅ | ✅ | ✅ | ✅ | | | | |
| create_users | ✅ | | ✅ | ✅ | | | | |
| edit_users | ✅ | | ✅ | ✅ | | | | |
| delete_users | ✅ | | | ✅ | | | | |
| assign_roles | ✅ | | ✅ | ✅ | | | | |
| **Branch Management** | | | | | | | | |
| view_branches | ✅ | ✅ | ✅ | ✅ | | | | |
| create_branches | ✅ | | ✅ | | | | | |
| edit_branches | ✅ | | ✅ | ✅ | | | | |
| switch_branch | ✅ | ✅ | ✅ | | | | | |
| **Master Data** | | | | | | | | |
| view_services | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | |
| create_services | ✅ | | ✅ | | | | | |
| edit_services | ✅ | | ✅ | ✅ | | | | |
| edit_service_pricing | | | | ✅ | | | | |
| **Orders** | | | | | | | | |
| view_orders | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | |
| create_orders | | | | | | | ✅ | |
| edit_orders | | | | ✅ | ✅ | | | |
| cancel_orders | | | | ✅ | ✅ | | | |
| process_refund | | | | ✅ | | | ✅ | |
| approve_refund | | | | ✅ | | | | |
| **Workshop** | | | | | | | | |
| view_production | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | | ✅ |
| update_status | | | | | ✅ | | | ✅ |
| assign_operator | | | | | ✅ | | | |
| quality_check | | | | | ✅ | | | |
| **CRM** | | | | | | | | |
| view_customers | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | |
| create_customers | | | ✅ | ✅ | | ✅ | | |
| edit_customers | | | ✅ | ✅ | | ✅ | | |
| manage_tiers | ✅ | | ✅ | | | | | |
| manage_loyalty_settings | ✅ | ✅ | ✅ | | | | | |
| send_wa_notification | | | | ✅ | ✅ | ✅ | | |
| **Promotions** | | | | | | | | |
| view_promotions | ✅ | ✅ | ✅ | ✅ | | ✅ | ✅ | |
| create_promotions | ✅ | | ✅ | | | | | |
| edit_promotions | ✅ | | ✅ | ✅ | | | | |
| toggle_promotion_branch | | | | ✅ | | | | |
| **Finance** | | | | | | | | |
| view_financial_reports | ✅ | ✅ | ✅ | ✅ | | | | |
| view_journal_entries | ✅ | ✅ | ✅ | ✅ | | | | |
| create_manual_journal | | | ✅ | ✅ | | | | |
| manage_accounting_periods | ✅ | | ✅ | | | | | |
| manage_tax_config | ✅ | ✅ | ✅ | | | | | |
| manage_expenses | | | | ✅ | | | | |
| **Inventory** | | | | | | | | |
| view_inventory | ✅ | ✅ | ✅ | ✅ | ✅ | | | |
| stock_in | | | ✅ | ✅ | | | | |
| stock_out | | | ✅ | ✅ | | | | |
| adjust_stock | | | ✅ | ✅ | | | | |
| manage_items | ✅ | | ✅ | | | | | |
| **Settings** | | | | | | | | |
| view_settings | ✅ | ✅ | ✅ | ✅ | | | | |
| edit_global_settings | ✅ | | ✅ | | | | | |
| edit_branch_settings | | | | ✅ | | | | |
| **Audit** | | | | | | | | |
| view_activity_logs | ✅ | ✅ | ✅ | | | | | |
| export_data | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | |
| **Developer Only** | | | | | | | | |
| manage_gateway_config | ✅ | | | | | | | |
| view_system_info | ✅ | | | | | | | |
| run_backup | ✅ | | ✅ | | | | | |

## Special Rules

1. **Developer account** (`is_protected = true`) cannot be deactivated or deleted
2. **Owner** cannot access system/technical settings or user management (read-only for business views)
3. **CS** can be central (`branch_id = null`, serve all branches) or per-branch (`branch_id` assigned)
4. **Branch Admin** cannot switch branch — bound to assigned branch
5. **Workshop Admin** sees all items in their workshop regardless of source branch
6. Self-edit: all roles can edit own password and photo only
