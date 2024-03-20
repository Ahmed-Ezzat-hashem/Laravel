<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Medieye API Documentation</title>
    <!-- Styles -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .title {
            font-size: 2rem;
            color: #343a40;
            margin-bottom: 20px;
        }

        .route {
            margin-bottom: 20px;
            padding: 41px;
            background-color: #fff;
            border: 11px solid #dee2e6;
            border-radius: 37px;
        }

        .route h3 {
            font-size: 1.5rem;
            color: #007bff;
            margin-bottom: 10px;
        }

        .route p {
            font-size: 1.1rem;
            color: #343a40;
            margin-bottom: 10px;
        }

        .route ul {
            list-style-type: disc;
            margin-left: 10px;
        }

        .route li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="title">
            Medieye API Documentation
        </div>

        <!-- Public Routes -->
        <div class="route">
            <h3>Public Routes</h3>
            <p>These routes do not require authentication.</p>
            <ul>
                <li><strong>POST /register-user:</strong> Register a new user.</li>
                <li><strong>POST /register-pharmacy:</strong> Register a new pharmacy.</li>
                <li><strong>POST /login-username:</strong> Login using username and password.</li>
                <li><strong>POST /login-email:</strong> Login using email and password.</li>
                <li><strong>POST /password/forgot-password-sms:</strong> Reset password by sending OTP via SMS.</li>
                <li><strong>POST /password/reset-sms:</strong> Reset password via SMS.</li>
                <li><strong>POST /password/forgot-password:</strong> Reset password by sending OTP via email.</li>
                <li><strong>POST /password/reset:</strong> Reset password via email.</li>
                <li><strong>GET /login-google:</strong> Login with Google.</li>
                <li><strong>GET /auth/google/callback:</strong> Google authentication callback.</li>
                <li><strong>GET /login-facebook:</strong> Login with Facebook.</li>
                <li><strong>GET /auth/facebook/callback:</strong> Facebook authentication callback.</li>
                <li><strong>GET /project-status:</strong> Check project status.</li>
            </ul>
        </div>

        <!-- register Routes -->
        <div class="route">
            <h3>Register Routes</h3>
            <ul>
                <li><strong>POST /register-user:</strong> Register a new user with username, email, and password.</li>
                <li><strong>POST /register-pharmacy:</strong> Register a new pharmacy with pharmacy details.</li>
            </ul>
        </div>

        <!-- Login Routes -->
        <div class="route">
            <h3>Login Routes</h3>
            <ul>
                <li><strong>POST /login-username:</strong> Login using username and password.</li>
                <li><strong>POST /login-email:</strong> Login using email and password.</li>
                <li><strong>GET /login-google:</strong> Login with Google OAuth.</li>
                <li><strong>GET /auth/google/callback:</strong> Google authentication callback.</li>
                <li><strong>GET /login-facebook:</strong> Login with Facebook OAuth.</li>
                <li><strong>GET /auth/facebook/callback:</strong> Facebook authentication callback.</li>
            </ul>
        </div>

        <!-- Forgot Password Routes -->
        <div class="route">
            <h3>Forgot Password Routes</h3>
            <ul>
                <li><strong>POST /password/forgot-password-sms:</strong> Request to reset password by sending OTP via SMS.</li>
                <li><strong>POST /password/reset-sms:</strong> Reset password by providing OTP received via SMS.</li>
                <li><strong>POST /password/forgot-password:</strong> Request to reset password by sending OTP via email.</li>
                <li><strong>POST /password/reset:</strong> Reset password by providing OTP received via email.</li>
            </ul>
        </div>

        <!-- Protected Routes -->
        <div class="route">
            <h3>Protected Routes</h3>
            <p>These routes require authentication via API token.</p>
            <ul>
                <li><strong>GET /user:</strong> Get authenticated user information.</li>
                <li><strong>GET /users:</strong> Get all users (admin only).</li>
                <li><strong>GET /user/{id}:</strong> Get user by ID (admin only).</li>
                <li><strong>POST /user/edit/{id}:</strong> Edit user by ID (admin only).</li>
                <li><strong>POST /user/add:</strong> Add a new user (admin only).</li>
                <li><strong>DELETE /user/{id}:</strong> Delete user by ID (admin only).</li>
                <li><strong>POST /category/edit/{id}:</strong> Edit category by ID (product manager only).</li>
                <li><strong>POST /category/add:</strong> Add a new category (product manager only).</li>
                <li><strong>DELETE /category/{id}:</strong> Delete category by ID (product manager only).</li>
                <li><strong>GET /categories:</strong> Get all categories.</li>
                <li><strong>GET /category/{id}:</strong> Get category by ID.</li>
                <li><strong>POST /product/edit/{id}:</strong> Edit product by ID (product manager only).</li>
                <li><strong>POST /product/add:</strong> Add a new product (product manager only).</li>
                <li><strong>DELETE /product/{id}:</strong> Delete product by ID (product manager only).</li>
                <li><strong>GET /products:</strong> Get all products.</li>
                <li><strong>GET /product/{id}:</strong> Get product by ID.</li>
                <li><strong>POST /search-by-product-code:</strong> Search product by code.</li>
                <li><strong>POST /search-by-color-and-shape:</strong> Search product by color and shape.</li>
                <li><strong>POST /product-img/add:</strong> Add product image (product manager only).</li>
                <li><strong>DELETE /product-img/{id}:</strong> Delete product image by ID (product manager only).</li>
                <li><strong>GET /logout:</strong> Logout.</li>
            </ul>
        </div>

        <!-- Users Developer -->
        <div class="route">
            <h3>Users</h3>
            <ul>
                <li><strong>GET /user:</strong> Get authenticated user information.</li>
                <li><strong>GET /users:</strong> Get all users (admin only).</li>
                <li><strong>GET /user/{id}:</strong> Get user by ID (admin only).</li>
                <li><strong>POST /user/edit/{id}:</strong> Edit user by ID (admin only).</li>
                <li><strong>POST /user/add:</strong> Add a new user (admin only).</li>
                <li><strong>DELETE /user/{id}:</strong> Delete user by ID (admin only).</li>
            </ul>
        </div>

        <!-- Categories Developer -->
        <div class="route">
            <h3>Categories</h3>
            <ul>
                <li><strong>POST /category/edit/{id}:</strong> Edit category by ID (product manager only).</li>
                <li><strong>POST /category/add:</strong> Add a new category (product manager only).</li>
                <li><strong>DELETE /category/{id}:</strong> Delete category by ID (product manager only).</li>
                <li><strong>GET /categories:</strong> Get all categories.</li>
                <li><strong>GET /category/{id}:</strong> Get category by ID.</li>
            </ul>
        </div>

        <!-- Products Developer -->
        <div class="route">
            <h3>Products</h3>
            <ul>
                <li><strong>POST /product/edit/{id}:</strong> Edit product by ID (product manager only).</li>
                <li><strong>POST /product/add:</strong> Add a new product (product manager only).</li>
                <li><strong>DELETE /product/{id}:</strong> Delete product by ID (product manager only).</li>
                <li><strong>GET /products:</strong> Get all products.</li>
                <li><strong>GET /product/{id}:</strong> Get product by ID.</li>
                <li><strong>POST /search-by-product-code:</strong> Search product by code.</li>
                <li><strong>POST /search-by-color-and-shape:</strong> Search product by color and shape.</li>
                <li><strong>POST /product-img/add:</strong> Add product image (product manager only).</li>
                <li><strong>DELETE /product-img/{id}:</strong> Delete product image by ID (product manager only).</li>
            </ul>
        </div>

        <!-- Cart Routes -->
        <div class="route">
            <h3>Cart</h3>
            <ul>
                <!-- Cart routes go here -->
                <li><strong>POST /cart/add:</strong> Add a product to the cart.</li>
                <li><strong>GET /cart:</strong> Get the items in the cart.</li>
                <li><strong>PUT /cart/update/{id}:</strong> Update quantity of a product in the cart.</li>
                <li><strong>DELETE /cart/remove/{id}:</strong> Remove a product from the cart.</li>
                <li><strong>DELETE /cart/clear:</strong> Clear the entire cart.</li>
            </ul>
        </div>

        <!-- Orders Routes -->
        <div class="route">
            <h3>Orders</h3>
            <ul>
                <!-- Orders routes go here -->
                <li><strong>POST /orders/place:</strong> Place a new order.</li>
                <li><strong>GET /orders:</strong> Get all orders.</li>
                <li><strong>GET /orders/{id}:</strong> Get order by ID.</li>
                <li><strong>PUT /orders/update/{id}:</strong> Update order status by ID.</li>
                <li><strong>DELETE /orders/cancel/{id}:</strong> Cancel order by ID.</li>
            </ul>
        </div>

        <!-- Prescription Routes -->
        <div class="route">
            <h3>Prescription</h3>
            <ul>
                <!-- Prescription routes go here -->
                <li><strong>POST /prescription/add:</strong> Add a new prescription.</li>
                <li><strong>GET /prescription:</strong> Get all prescriptions.</li>
                <li><strong>GET /prescription/{id}:</strong> Get prescription by ID.</li>
                <li><strong>PUT /prescription/update/{id}:</strong> Update prescription by ID.</li>
                <li><strong>DELETE /prescription/delete/{id}:</strong> Delete prescription by ID.</li>
            </ul>
        </div>
    </div>
</body>
</html>
