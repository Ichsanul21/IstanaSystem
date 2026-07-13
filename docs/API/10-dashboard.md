# API: Dashboard

## GET /api/v1/dashboard/summary

Dashboard summary data. **Query:** `?period=today|this_week|this_month|date_from=&date_to=&branch_id=`

**Response:**
```json
{
    "data": {
        "metrics": {
            "today_revenue": 1240000,
            "today_orders": 28,
            "active_production": 45,
            "low_stock_items": 2
        },
        "revenue_trend": {
            "labels": ["Sen", "Sel", "Rab", "Kam", "Jum", "Sab", "Min"],
            "datasets": [
                { "label": "Pendapatan", "data": [500000, 750000, 600000, ...] }
            ]
        },
        "order_status": {
            "labels": ["Proses", "Selesai", "Diambil"],
            "data": [45, 28, 15]
        },
        "top_services": {
            "labels": ["Cuci Kering", "Setrika", "Cuci + Setrika"],
            "data": [45, 30, 20]
        }
    }
}
```

## GET /api/v1/dashboard/revenue

Revenue detail. Same query params.

**Response:** Revenue by service, by branch, by day.

## GET /api/v1/dashboard/operational

Operational data: order count, avg value, peak hours, top customers.

## GET /api/v1/dashboard/production

Production data: queue by status, avg processing time, workshop performance.

## GET /api/v1/dashboard/finance

Financial data: revenue vs expense, profit margin, monthly trend.

## GET /api/v1/dashboard/inventory

Inventory data: stock value, low stock alerts, movement by category.
