# Pharmacy API

## Project Overview

The **Pharmacy API** is a RESTful API built with Laravel to manage pharmacy operations. It supports inventory management for medicines and order processing, with secure access for pharmacists and customers.

## What I’ve Done

-   **Medicine Management**: Implemented features to add, update, delete, and view medicines, including categorization.
-   **Order Management**: Built functionality to create orders, view order history, update statuses, and confirm orders.
-   **Authentication**: Added JWT-based authentication to secure API endpoints.
-   **Stock Management**: Set up automatic stock reduction when orders are placed.
-   **Role-based Access**: Used middleware to limit sensitive actions (e.g., updating orders) to pharmacists.
-   **Cart Functionality**: Let users add medicines to a cart before ordering.
-   **Payment Integration**: Add a payment system for order processing.
-   **Notifications**:  Gmail SMTP email notifications for order status updates.
-   **Reporting**: Create reports on sales, stock, and user activity.
## API Endpoints

### Authentication

-   `POST /api/auth/register`:register a new account
-   `POST /api/auth/login`: Log in and receive a JWT token.
-   `POST /api/auth/logout`:Log out and invalidate the token.

### Medicines

-   `POST /api/medicines`: Add a medicine (pharmacist-only).
-   `GET /api/medicines`: List all medicines.
-   `GET /api/medicines/{id}`: View a specific medicine.
-   `PUT /api/medicines/{id}`: Update a medicine (pharmacist-only).
-   `DELETE /api/medicines/{id}`: Delete a medicine (pharmacist-only).

### Orders

-   `POST /api/orders`: Create a new order.
-   `GET /api/orders`: List orders for the authenticated user.
-   `GET /api/orders/{id}`: View a specific order.
-   `PUT /api/orders/{id}`: Update order status (pharmacist-only).
-   `DELETE /api/orders/{id}`: Cancel a pending order.
-   `POST /api/orders/{id}/confirm`: Confirm a pending order.
-   `POST /api/orders/from-cart`: Create an order from the user's cart amd initiate payment.

### Order Tracking

-   `PUT /api/orders/{id}/status`: Update order status (pharmacist only).
-   `GET /api/orders/{id}/track`: View order tracking history.

### Carts

-   `GET /api/carts`: List all items in the user’s cart.
-   `POST /api/carts`: Add a medicine to the cart.
-   `GET /api/carts/{id}`: View a specific cart item.
-   `PUT /api/carts/{id}`: Update the quantity of a cart item.
-   `DELETE /api/carts/{id}`: Remove an item from the cart.

### Payments

-   `POST /api/payments/{order_id}`: Initiate payment for an order.
-   `GET /api/payments/{id}`: View payment status.
-   `POST /api/payments/{id}/refund`: Refund a completed payment.

### Reporting
- `GET /api/reports/sales?start_date=YYYY-MM-DD&end_date=YYYY-MM-DD`: Generate a sales report for the specified date range (pharmacist only). 

