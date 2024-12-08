<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Manager - Your Money, Simplified</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fffdf5;
            margin: 0;
            padding: 0;
            color: #2c3e50;
            scroll-behavior: smooth; /* Smooth scrolling */
        }

        /* Header Styles */
        header {
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.8);
            color:  #fffdf5;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: background 0.3s;
        }
        header .logo {
            font-size: 2.0rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Navigation Bar */
        nav {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .nav-links {
            display: flex;
        }
        .nav-links a {
            text-decoration: none;
            color: #5a4f4e;
            margin: 0 15px;
            font-size: 1.1rem;
            font-weight: bold;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color:  #5a4f22;
        }

        /* Hero Section with Image Slider */
        .hero {
            position: relative;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            flex-direction: column;
        }

        .hero .slider {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
            transition: opacity 1s ease-in-out;
            background-size: cover; /* Ensures the image covers the entire area */
            background-position: center; /* Ensures the image is centered */
            margin-bottom: 0;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: bold;
            color: #000435;
        }

        .hero p {
            font-size: 1.2rem;
            margin-top: 10px;
            max-width: 600px;
            font-weight: bold;
            color: #000435;
        }

        .cta-button {
            margin-top: 20px;
            background-color: #000435;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .cta-button:hover {
            background-color: #002D7F;
        }

        /* Why Choose Us Section */
        .choose-us {
            display: flex;
            justify-content: space-between;
            padding: 60px 20px;
            background-color: #ecf0f1;
            text-align: left;
        }

        .choose-us .content {
            flex: 1;
            padding: 20px;
            max-width: 50%;
        }

        .choose-us h2 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .choose-us p {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #7f8c8d;
        }

        /* Alternating Content/Image Layout */
        .content-image-wrapper {
            display: flex;
            justify-content: space-between;
            margin: 40px 0;
            align-items: center;
            padding-top: 0;
            margin-top:0;
        }

        .content-image-wrapper:nth-child(even) {
            flex-direction: row-reverse; /* Alternate direction for even rows */
        }

        .content-image-wrapper .content {
            flex: 1; 
            padding: 20px;  /* Adjust padding inside content for better spacing */
            max-width: 600px;  /* Limit content width to keep it balanced */
            text-align: left;
            box-sizing: border-box;  /* Ensure padding is included in width calculations */
            margin-top:0;
        }

        .content-image-wrapper .content h3 {
            font-size: 2rem;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .content-image-wrapper .content p {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #7f8c8d;
        }

        .content-image-wrapper .image {
            flex: 1;
            max-width: 50%;  /* Limit image width */
            margin-top: 0;  /* Remove any unwanted margins around the image */
        }

        .content-image-wrapper .image img {
            width: 100%;  /* Ensure image fills its container without stretching */
            height: auto;  /* Maintain image aspect ratio */
            border-radius: 3px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Footer */
        footer {
            background-color: #5a4f4e;
            color: white;
            text-align: center;
            padding: 10px 0;
        }

        /* Parallax Effect */
        .parallax {
            background-image: url('images/taxbg6.webp');
            height: 400px;
            background-attachment: fixed;
            background-size: cover;
            background-position: center;
        }

        /* On Scroll Effects */
        header.scrolled {
            background: rgba(255, 255, 255, 1);
        }

    </style>
</head>
<body>

    <!-- Header -->
    <header id="header">
    <div class="logo">
        <img src="images\ftlogo.png" alt="Financial Tracker Logo" style="height: 80px; width: 110px;">
    </div>
    <nav>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="tax.php">Tax Calculator</a>
            <a href="track_expenses.php">Track Expenses</a>
            <a href="tax_graph.php">Reports</a>
        </div>
        <div class="nav-links">
            <a href="registration.php">Create Account</a>
            <a href="login.php">Login</a>
        </div>
    </nav>
</header>

    <!-- Hero Section with Slider -->
    <section class="hero" id="hero">
        <div class="slider" id="slider"></div>
        <h1>Welcome to Finance Manager</h1>
        <p>Take control of your finances. Manage your taxes, track expenses, plan savings, and make informed financial decisions—all in one place.</p>
        <a href="registration.php" class="cta-button">Get Started</a>
    </section>

    <!-- Parallax Section -->
    <div class="parallax"></div>


    <!-- Alternating Content/Image Section 1 -->
    <div class="content-image-wrapper">
        <div class="content">
            <h3>Your Personal Finance Hub</h3>
            <p>Whether you're tracking expenses, calculating taxes, or planning your savings, Financial Tracker is your all-in-one financial companion. Our intuitive tools are designed to help you make informed decisions, understand your spending habits, and optimize your savings potential. With Financial Tracker, financial freedom is within your reach.</p>
        </div>
        <div class="image">
            <img src="images/taxbg11.jpeg" alt="Track Taxes">
        </div>
    </div>

    <!-- Alternating Content/Image Section 2 -->
    <div class="content-image-wrapper">
        <div class="content">
            <h3>Financial Reports at Your Fingertips</h3>
            <p>From Tax Calculators to Expense Trackers, we offer a range of features to simplify your daily financial tasks. Need help with tax planning? Our accurate and easy-to-use tax calculator gives you the insights you need. Track your expenses seamlessly and stay on top of your budget effortlessly.</p>
        </div>
        <div class="image">
            <img src="images/taxbg12.jpeg" alt="Financial Reports">
        </div>
    </div>

    <!-- Why Choose Us Section -->
    <div class="choose-us">
        <div class="content">
            <h2>Why Choose Us?</h2>
            <p><strong>User-Centric Design:</strong> Our platform is built with you in mind—simple, user-friendly, and designed to give you real-time insights into your financial situation.</p>
            <p><strong>Secure and Private:</strong> We take your privacy seriously. All your data is protected with the highest levels of security.</p>
            <p><strong>Accessible Anywhere:</strong> Whether you're at home, at the office, or on the go, Finance Manager is accessible across devices, so you can manage your finances wherever life takes you.</p>
            <p><strong>Real-Time Insights:</strong> Get live updates on your financial data, allowing you to make smart, timely decisions.</p>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Finance Manager. All rights reserved.</p>
    </footer>

    <!-- JavaScript for Slider -->
    <script>
        let currentIndex = 0;
        const images = [
            'images/taxbg13.jpg',
            'images/taxbg3.jpg',
            'images/taxbg4.jpeg'
        ];

        const slider = document.getElementById('slider');

        function changeSliderImage() {
            slider.style.backgroundImage = `url(${images[currentIndex]})`;
            slider.style.opacity = 1;
            currentIndex = (currentIndex + 1) % images.length;
        }

        setInterval(() => {
            slider.style.opacity = 0; // Fade out the current image
            setTimeout(changeSliderImage, 1000); // Change the image after fade-out
        }, 3000); // Change every 3 seconds

        // Initialize the slider
        changeSliderImage();
    </script>
</body>
</html>
