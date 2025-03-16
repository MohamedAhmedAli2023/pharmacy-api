
# Pharmacy API

## Project Overview
The **Pharmacy API** is a RESTful API built with Laravel to manage pharmacy operations. It supports inventory management for medicines and order processing, with secure access for pharmacists and customers.

## What I’ve Done
- **Medicine Management**: Implemented features to add, update, delete, and view medicines, including categorization.
- **Order Management**: Built functionality to create orders, view order history, update statuses, and confirm orders.
- **Authentication**: Added JWT-based authentication to secure API endpoints.
- **Stock Management**: Set up automatic stock reduction when orders are placed.
- **Role-based Access**: Used middleware to limit sensitive actions (e.g., updating orders) to pharmacists.
- **Cart Functionality**: Let users add medicines to a cart before ordering.
- **Payment Integration**: Add a payment system for order processing.
## Setup Instructions
1. **Clone the Repository**:
   ```bash
   git clone <repository-url>
   cd pharmacy-api
   ```

2. **Install Dependencies**:
   ```bash
   composer install
   ```

3. **Configure Environment**:
   - Copy `.env.example` to `.env` and fill in your database details.
   - Generate an app key:
     ```bash
     php artisan key:generate
     ```

4. **Run Migrations**:
   ```bash
   php artisan migrate
   ```

5. **Start the Server**:
   ```bash
   php artisan serve
   ```

6. **Test the API**:
   - Use a tool like Postman.
   - Log in with `POST /api/auth/login` to get a JWT token, then include it in the `Authorization` header for protected routes.

## API Endpoints
### Authentication
- `POST /api/auth/login`: Log in and receive a JWT token.

### Medicines
- `POST /api/medicines`: Add a medicine (pharmacist-only).
- `GET /api/medicines`: List all medicines.
- `GET /api/medicines/{id}`: View a specific medicine.
- `PUT /api/medicines/{id}`: Update a medicine (pharmacist-only).
- `DELETE /api/medicines/{id}`: Delete a medicine (pharmacist-only).

### Orders
- `POST /api/orders`: Create a new order.
- `GET /api/orders`: List orders for the authenticated user.
- `GET /api/orders/{id}`: View a specific order.
- `PUT /api/orders/{id}`: Update order status (pharmacist-only).
- `DELETE /api/orders/{id}`: Cancel a pending order.
- `POST /api/orders/{id}/confirm`: Confirm a pending order.
- `POST /api/orders/from-cart`: Create an order from the user's cart amd initiate payment.

### Carts
- `GET /api/carts`: List all items in the user’s cart.
- `POST /api/carts`: Add a medicine to the cart.
- `GET /api/carts/{id}`: View a specific cart item.
- `PUT /api/carts/{id}`: Update the quantity of a cart item.
- `DELETE /api/carts/{id}`: Remove an item from the cart.

### Payments
- `POST /api/payments/{order_id}`: Initiate payment for an order.
- `GET /api/payments/{id}`: View payment status.
- `POST /api/payments/{id}/refund`: Refund a completed payment.

## What I’ve Done
- Added Stripe payment integration with a separate `PaymentController`.

## What I Will Do Next

- **Order Tracking**: Enable real-time status updates for orders.
- **Reporting**: Create reports on sales, stock, and user activity.
- **Notifications**: Add email or SMS alerts for order updates.

---

This README summarizes the current state of the Pharmacy API and outlines my next steps. Let me know if you’d like me to tweak anything!
