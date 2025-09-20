<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Travel 360</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
        }

        /* Background animation */
        .background {
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(120deg, #00c6ff, #0072ff);
            overflow: hidden;
            z-index: -1;
        }

        .background span {
            position: absolute;
            display: block;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.3);
            animation: float 20s linear infinite;
            border-radius: 50%;
        }

        @keyframes float {
            0% { transform: translateY(100vh) translateX(0); opacity: 0; }
            50% { opacity: 1; }
            100% { transform: translateY(-10vh) translateX(50vw); opacity: 0; }
        }

        /* Center content */
        .container {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
            color: #fff;
            padding: 0 20px;
        }

        h1 {
            font-size: 4rem;
            animation: slideIn 2s ease-out forwards;
            opacity: 0;
        }

        @keyframes slideIn {
            0% { transform: translateY(-50px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        p {
            font-size: 1.5rem;
            margin-top: 20px;
            animation: fadeIn 3s ease forwards;
            opacity: 0;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Button */
        .btn {
            margin-top: 30px;
            padding: 15px 40px;
            background: #ff7e5f;
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            border: none;
            border-radius: 50px;
            color: #fff;
            font-size: 1.2rem;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .btn:hover {
            transform: scale(1.1);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

    </style>
</head>
<body>
    <div class="background">
        <!-- floating dots -->
        <span style="left:10%; width:15px; height:15px; animation-duration:25s;"></span>
        <span style="left:20%; width:25px; height:25px; animation-duration:30s;"></span>
        <span style="left:30%; width:20px; height:20px; animation-duration:22s;"></span>
        <span style="left:40%; width:18px; height:18px; animation-duration:28s;"></span>
        <span style="left:50%; width:22px; height:22px; animation-duration:26s;"></span>
        <span style="left:60%; width:15px; height:15px; animation-duration:24s;"></span>
        <span style="left:70%; width:30px; height:30px; animation-duration:32s;"></span>
        <span style="left:80%; width:18px; height:18px; animation-duration:27s;"></span>
        <span style="left:90%; width:20px; height:20px; animation-duration:29s;"></span>
    </div>

    <div class="container">
        <h1>Welcome to Travel 360</h1>
        <p>Explore the world like never before</p>
        <button class="btn">Get Started</button>
    </div>
</body>
</html>
