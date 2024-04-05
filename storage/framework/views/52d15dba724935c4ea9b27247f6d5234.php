<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            overflow-x: hidden; /* Add this line to remove downscroller */
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .logo {
            display: block;
            max-width: 150px;
            margin: 0 auto 20px;
        }
        h2 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }
        p {
            font-size: 16px;
            margin-bottom: 10px;
            color: #666;
        }
        .otp {
            font-size: 20px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://i.ibb.co/6bNf67K/logo-search-grid-1x.png"  alt="Logo" class="logo">
        <h2>Hello <?php echo e($user->user_name); ?>,</h2>
        <p>Use the following code to verify your email address:</p>
        <p class="otp"><?php echo e($otp); ?></p>
        
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\back-end\resources\views/emails/custom_email.blade.php ENDPATH**/ ?>