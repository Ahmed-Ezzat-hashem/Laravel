<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Restting</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            overflow-x: hidden;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .logo {
            display: block;
            max-width: 200px;
            margin: 0 auto 20px;
        }
        h2 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #333;
            text-align: center;
        }
        p {
            font-size: 16px;
            margin-bottom: 10px;
            color: #666;
            text-align: center;
        }
        .otp {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 20px;
            text-align: center;
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
        <img src="https://github.com/Ahmed-Ezzat-hashem/my-images/blob/main/medieye-high-resolution-logo-black.png?raw=true" alt="MediEye Logo" class="logo">
        <h2>Hello {{ $user->user_name }},</h2>
        <p>Use the below code for rest your password</p>
        <p class="otp">{{ $otp }}</p>
        {{-- <p>&copy; {{ date('Y') }} Your Company. All rights reserved.</p> --}}
    </div>
</body>
</html>
