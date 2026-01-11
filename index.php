<?php
// Database configuration
$host = 'localhost';
$dbname = 'gwiragwi_gwira';
$username = 'gwiragwi_gwira'; // Change this to your MySQL username
$password = 'd7c2AfgkNnx7GBR698Sa'; // Change this to your MySQL password

// $host = 'localhost';
// $dbname = 'gwira_gwira_db';
// $username = 'root'; // Change this to your MySQL username
// $password = ''; // Change this to your MySQL password

// Create connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle Review Submission
    if (isset($_POST['submit_review'])) {
        $name = htmlspecialchars($_POST['reviewerName']);
        $role = htmlspecialchars($_POST['reviewerRole']);
        $rating = intval($_POST['rating']);
        $title = trim($_POST['reviewTitle']);
        $review = trim($_POST['reviewText']);
        $event_attended = htmlspecialchars($_POST['eventAttended']);
        
        $stmt = $pdo->prepare("INSERT INTO reviews (name, role, rating, title, review, event_attended, created_at) 
                               VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $role, $rating, $title, $review, $event_attended]);

         // Redirect to avoid resubmission
        header("Location: ".$_SERVER['PHP_SELF']."?review=success");
        exit;
        
        // $review_success = "Thank you for your review! It has been submitted successfully.";
    }
    
    // Handle Registration Submission
    if (isset($_POST['submit_registration'])) {
        $reg_name = htmlspecialchars($_POST['reg_name']);
        $reg_email = htmlspecialchars($_POST['reg_email']);
        $reg_phone = htmlspecialchars($_POST['reg_phone']);
        $reg_organization = htmlspecialchars($_POST['reg_organization']);
        $reg_type = htmlspecialchars($_POST['reg_type']);
        $reg_days = htmlspecialchars($_POST['reg_days']);
        $reg_notes = htmlspecialchars($_POST['reg_notes']);
        
        $stmt = $pdo->prepare("INSERT INTO registrations (name, email, phone, organization, reg_type, attendance_days, notes, created_at) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$reg_name, $reg_email, $reg_phone, $reg_organization, $reg_type, $reg_days, $reg_notes]);
        
        // $registration_success = "Thank you for registering! We will contact you soon with more details.";
        // Redirect to avoid resubmission
        header("Location: ".$_SERVER['PHP_SELF']."?registration=success");
        exit;
    }
}

// Fetch reviews from database
$reviews_query = "SELECT * FROM reviews WHERE status = 'approved' ORDER BY created_at DESC LIMIT 10";
$reviews_stmt = $pdo->query($reviews_query);
$reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gwira Gwira Creatives Summit & Gala - Kilifi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-green: #1a5d1a;
            --light-green: #2e8b2e;
            --yellow: #ffd700;
            --dark-yellow: #e6c300;
            --black: #121212;
            --gray: #333333;
            --light-gray: #f5f5f5;
            --white: #ffffff;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            color: var(--black);
            background-color: var(--white);
            line-height: 1.6;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        /* Header & Navigation */
        header {
            background-color: var(--white);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }
        
        .logo {
            display: flex;
            align-items: center;
        }
        
        .logo-img {
            height: 60px;
            width: auto;
            margin-right: 10px;
        }
        
        .logo-text {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-green);
            margin-left: 10px;
        }
        
        .logo-text span {
            color: var(--yellow);
        }
        
        .logo-icon {
            color: var(--yellow);
            font-size: 1.8rem;
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin-left: 25px;
        }
        
        nav ul li a {
            text-decoration: none;
            color: var(--gray);
            font-weight: 600;
            font-size: 1rem;
            transition: color 0.3s;
            padding: 5px 0;
            position: relative;
        }
        
        nav ul li a:hover {
            color: var(--primary-green);
        }
        
        nav ul li a.active {
            color: var(--primary-green);
        }
        
        nav ul li a.active:after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: var(--yellow);
        }
        
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--primary-green);
            cursor: pointer;
        }
        
        /* Countdown Timer */
        .countdown-section {
            background-color: var(--primary-green);
            color: var(--white);
            padding: 40px 0;
            text-align: center;
        }
        
        .countdown-title {
            font-size: 2rem;
            margin-bottom: 20px;
            color: var(--yellow);
        }
        
        .countdown-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        
        .countdown-item {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 8px;
            min-width: 120px;
        }
        
        .countdown-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--yellow);
            margin-bottom: 5px;
        }
        
        .countdown-label {
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80');
            background-size: cover;
            background-position: center;
            color: var(--white);
            padding: 100px 0;
            text-align: center;
        }
        
        .hero h1 {
            font-size: 3.2rem;
            margin-bottom: 15px;
            color: var(--yellow);
        }
        
        .hero .tagline {
            font-size: 1.5rem;
            margin-bottom: 30px;
            color: var(--white);
        }
        
        .date-location {
            font-size: 1.3rem;
            margin-bottom: 40px;
            color: var(--white);
        }
        
        .date-location i {
            color: var(--yellow);
            margin-right: 10px;
        }
        
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            display: inline-block;
        }
        
        .btn-primary {
            background-color: var(--yellow);
            color: var(--black);
        }
        
        .btn-primary:hover {
            background-color: var(--dark-yellow);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn-secondary {
            background-color: transparent;
            color: var(--white);
            border: 2px solid var(--white);
        }
        
        .btn-secondary:hover {
            background-color: var(--white);
            color: var(--black);
            transform: translateY(-3px);
        }
        
        /* Page Sections */
        .page-section {
            padding: 80px 0;
        }
        
        .section-title {
            text-align: center;
            font-size: 2.5rem;
            color: var(--primary-green);
            margin-bottom: 50px;
            position: relative;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: var(--yellow);
        }
        
        .section-content {
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* Impact Stats */
        .impact-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .stat-card {
            background-color: var(--white);
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
            border-top: 4px solid var(--primary-green);
        }
        
        .stat-card:hover {
            transform: translateY(-10px);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-green);
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: var(--gray);
            font-size: 1rem;
        }
        
        /* Speakers Section */
        .speakers-section {
            background-color: var(--light-gray);
        }
        
        .speakers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .speaker-card {
            background-color: var(--white);
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
            border-top: 4px solid var(--primary-green);
        }
        
        .speaker-card:hover {
            transform: translateY(-10px);
        }
        
        .speaker-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .speaker-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: var(--primary-green);
            margin-right: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--yellow);
        }
        
        .speaker-info h3 {
            color: var(--primary-green);
            margin-bottom: 5px;
        }
        
        .speaker-org {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .speaker-topic {
            color: var(--light-green);
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .speaker-bio {
            margin-top: 15px;
            color: var(--gray);
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        /* Panel Section */
        .panel-section {
            background-color: var(--white);
        }
        
        .panel-card {
            background-color: var(--light-gray);
            padding: 40px;
            border-radius: 8px;
            margin-top: 40px;
            border-left: 5px solid var(--yellow);
        }
        
        .panel-title {
            color: var(--primary-green);
            font-size: 1.8rem;
            margin-bottom: 20px;
        }
        
        .panel-moderator, .panel-topics {
            margin: 20px 0;
        }
        
        .panel-moderator h4, .panel-topics h4 {
            color: var(--primary-green);
            margin-bottom: 10px;
        }
        
        /* Sponsors Tiers */
        .sponsor-tiers {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }
        
        .sponsor-tier {
            background-color: var(--light-gray);
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            transition: transform 0.3s;
        }
        
        .sponsor-tier:hover {
            transform: translateY(-5px);
        }
        
        .tier-title {
            color: var(--primary-green);
            font-size: 1.3rem;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--yellow);
        }
        
        .tier-investment {
            color: var(--light-green);
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 15px;
        }
        
        .tier-benefits {
            text-align: left;
            margin-top: 15px;
        }
        
        .tier-benefits ul {
            padding-left: 20px;
            margin-bottom: 20px;
        }
        
        .tier-benefits li {
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        
        .tier-exclusive {
            background: linear-gradient(135deg, var(--yellow), #ffed4e);
            color: var(--black);
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        /* FAQ Section */
        .faq-section {
            background-color: var(--light-gray);
        }
        
        .faq-container {
            max-width: 800px;
            margin: 40px auto 0;
        }
        
        .faq-item {
            background-color: var(--white);
            margin-bottom: 15px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }
        
        .faq-question {
            padding: 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--primary-green);
            color: var(--white);
            font-weight: 600;
        }
        
        .faq-question:hover {
            background-color: var(--light-green);
        }
        
        .faq-answer {
            padding: 20px;
            display: none;
        }
        
        .faq-item.active .faq-answer {
            display: block;
        }
        
        .faq-toggle {
            font-size: 1.2rem;
            transition: transform 0.3s;
        }
        
        .faq-item.active .faq-toggle {
            transform: rotate(45deg);
        }
        
        /* Reviews Section */
        .reviews-section {
            background-color: var(--light-gray);
        }
        
        .reviews-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .review-card {
            background-color: var(--white);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            position: relative;
        }
        
        .review-card.new-review {
            border: 2px solid var(--yellow);
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .review-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .review-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--primary-green);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.5rem;
            margin-right: 15px;
        }
        
        .reviewer-info h4 {
            color: var(--primary-green);
            margin-bottom: 5px;
        }
        
        .review-stars {
            color: var(--yellow);
            margin-bottom: 10px;
        }
        
        .review-date {
            color: var(--gray);
            font-size: 0.8rem;
            margin-top: 10px;
        }
        
        /* Submit Review Section */
        .submit-review-section {
            background-color: var(--white);
            margin-top: 60px;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .submit-review-section h3 {
            color: var(--primary-green);
            margin-bottom: 20px;
            text-align: center;
            font-size: 1.8rem;
        }
        
        .review-form {
            display: grid;
            gap: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            margin-bottom: 8px;
            color: var(--gray);
            font-weight: 600;
        }
        
        .review-form input,
        .review-form textarea,
        .review-form select {
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border 0.3s;
        }
        
        .review-form input:focus,
        .review-form textarea:focus,
        .review-form select:focus {
            outline: none;
            border-color: var(--primary-green);
        }
        
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 5px;
            margin-bottom: 10px;
        }
        
        .star-rating input {
            display: none;
        }
        
        .star-rating label {
            font-size: 1.8rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.3s;
        }
        
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: var(--yellow);
        }
        
        .review-form button {
            background-color: var(--primary-green);
            color: var(--white);
            border: none;
            padding: 14px 30px;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            margin-top: 10px;
        }
        
        .review-form button:hover {
            background-color: var(--light-green);
            transform: translateY(-2px);
        }
        
        /* Highlights */
        .highlights {
            background-color: var(--light-gray);
        }
        
        .highlight-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .highlight-card {
            background-color: var(--white);
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
            border-top: 4px solid var(--primary-green);
        }
        
        .highlight-card:hover {
            transform: translateY(-10px);
        }
        
        .highlight-icon {
            font-size: 2.5rem;
            color: var(--yellow);
            margin-bottom: 20px;
        }
        
        .highlight-card h3 {
            color: var(--primary-green);
            margin-bottom: 15px;
        }
        
        /* Program */
        .program-timeline {
            position: relative;
            max-width: 800px;
            margin: 50px auto 0;
        }
        
        .program-timeline:before {
            content: '';
            position: absolute;
            left: 30px;
            top: 0;
            height: 100%;
            width: 4px;
            background-color: var(--yellow);
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 40px;
            padding-left: 70px;
        }
        
        .timeline-date {
            position: absolute;
            left: 0;
            top: 0;
            background-color: var(--primary-green);
            color: var(--white);
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: 600;
        }
        
        .timeline-content {
            background-color: var(--light-gray);
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid var(--primary-green);
        }
        
        .timeline-content h3 {
            color: var(--primary-green);
            margin-bottom: 10px;
        }
        
        /* Partners */
        .partner-categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .partner-category {
            background-color: var(--light-gray);
            padding: 30px;
            border-radius: 8px;
        }
        
        .partner-category h3 {
            color: var(--primary-green);
            margin-bottom: 20px;
            border-bottom: 2px solid var(--yellow);
            padding-bottom: 10px;
        }
        
        /* Committee */
        .committee-members {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .committee-member {
            text-align: center;
            padding: 20px;
            background-color: var(--light-gray);
            border-radius: 8px;
        }
        
        .member-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: var(--primary-green);
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--yellow);
        }
        
        /* Contact */
        .contact-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin-top: 40px;
        }
        
        .contact-info {
            background-color: var(--light-gray);
            padding: 30px;
            border-radius: 8px;
        }
        
        .contact-info h3 {
            color: var(--primary-green);
            margin-bottom: 20px;
        }
        
        .contact-detail {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .contact-detail i {
            color: var(--yellow);
            font-size: 1.2rem;
            width: 30px;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: var(--primary-green);
            color: var(--white);
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .social-links a:hover {
            background-color: var(--yellow);
            color: var(--black);
            transform: translateY(-3px);
        }
        
        .contact-form input,
        .contact-form textarea,
        .contact-form select {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .contact-form button {
            background-color: var(--primary-green);
            color: var(--white);
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }
        
        .contact-form button:hover {
            background-color: var(--light-green);
        }
        
        /* Footer */
        footer {
            background-color: var(--black);
            color: var(--white);
            padding: 60px 0 30px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .footer-column h3 {
            color: var(--yellow);
            margin-bottom: 20px;
            font-size: 1.3rem;
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: var(--yellow);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid var(--gray);
            color: #aaa;
            font-size: 0.9rem;
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 992px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .countdown-item {
                min-width: 100px;
                padding: 15px;
            }
            
            .countdown-number {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                padding: 15px 0;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            nav {
                width: 100%;
                display: none;
            }
            
            nav.active {
                display: block;
            }
            
            nav ul {
                flex-direction: column;
                align-items: center;
            }
            
            nav ul li {
                margin: 10px 0;
            }
            
            .mobile-menu-btn {
                display: block;
                position: absolute;
                top: 20px;
                right: 20px;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero .tagline {
                font-size: 1.2rem;
            }
            
            .countdown-container {
                flex-wrap: wrap;
            }
            
            .countdown-item {
                min-width: 80px;
                padding: 10px;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 300px;
                text-align: center;
            }
            
            .program-timeline:before {
                left: 20px;
            }
            
            .timeline-item {
                padding-left: 50px;
            }
            
            .submit-review-section {
                padding: 25px;
            }
            
            .star-rating label {
                font-size: 1.5rem;
            }
        }
        
        /* Utility Classes */
        .text-center {
            text-align: center;
        }
        
        .mb-20 {
            margin-bottom: 20px;
        }
        
        .mt-40 {
            margin-top: 40px;
        }

        /* Base alert style */
.alert {
    position: relative;
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
    border: 1px solid transparent;
    border-radius: 0.375rem; /* same as Bootstrap */
    font-size: 0.95rem;
    line-height: 1.5;
}

/* Success alert */
.alert-success {
    color: #0f5132;
    background-color: #d1e7dd;
    border-color: #badbcc;
}

/* Optional fade-in effect */
.alert {
    animation: alertFade 0.3s ease-in-out;
}

@keyframes alertFade {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

    </style>
</head>
<body>
    <!-- Header & Navigation -->
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <img src="image/logo.jpeg" alt="Gwira Gwira Logo" class="logo-img">
                    <!-- <div class="logo-text">Gwira<span>Gwira</span></div> -->
                </div>
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>
                <nav id="mainNav">
                    <ul>
                        <li><a href="#home" class="active">Home</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#speakers">Speakers</a></li>
                        <li><a href="#program">Program</a></li>
                        <li><a href="#sponsors">Sponsors</a></li>
                        <li><a href="#reviews">Reviews</a></li>
                        <li><a href="#faq">FAQ</a></li>
                        <li><a href="#committee">Committee</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Home Section -->
    <section id="home" class="hero">
        <div class="container">
            <h1>Gwira Gwira Creative Summit</h1>
            <div class="tagline">Cancer Walk Awareness & Gala Night</div>
            <div class="date-location">
                <i class="fas fa-calendar-alt"></i> 28th & 29th March 2026 | 
                <i class="fas fa-map-marker-alt"></i> Pwani University , Karisa Maitha Ground & Inkaba Resort, Kilifi
            </div>
            <p class="section-content">Empowering Youth Through Culture, Creativity and Health Awareness. Join us for 2 days of creative showcases, community engagement, and celebration of youth talent.</p>
            
            <div class="cta-buttons">
                <a href="#contact" class="btn btn-primary">Register Now</a>
                <a href="#sponsors" class="btn btn-secondary">Become a Sponsor</a>
            </div>
        </div>
    </section>

    <!-- Countdown Timer -->
    <section class="countdown-section">
        <div class="container">
            <h2 class="countdown-title">Summit Countdown</h2>
            <div class="countdown-container">
                <div class="countdown-item">
                    <div class="countdown-number" id="days">00</div>
                    <div class="countdown-label">Days</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-number" id="hours">00</div>
                    <div class="countdown-label">Hours</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-number" id="minutes">00</div>
                    <div class="countdown-label">Minutes</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-number" id="seconds">00</div>
                    <div class="countdown-label">Seconds</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Impact Stats -->
    <section class="page-section">
        <div class="container">
            <h2 class="section-title">Event Impact</h2>
            <div class="impact-stats">
                <div class="stat-card">
                    <div class="stat-number">200+</div>
                    <div class="stat-label">Youth to be empowered</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Youth-led initiatives showcased</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">2</div>
                    <div class="stat-label">Days of activities</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">KES 450K</div>
                    <div class="stat-label">Estimated budget</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Speakers Section -->
    <section id="speakers" class="speakers-section page-section">
        <div class="container">
            <h2 class="section-title">Speakers & Panelists</h2>
            <p class="text-center mb-20">Meet the experts and thought leaders shaping youth development and the creative economy.</p>
            
            <!-- <div class="speakers-grid">
                <div class="speaker-card">
                    <div class="speaker-header">
                        <div class="speaker-avatar">DK</div>
                        <div class="speaker-info">
                            <h3>Dr. Kamau Wanjiru</h3>
                            <div class="speaker-org">Director, Youth Empowerment Programs</div>
                            <div class="speaker-topic">Creative Economy & Youth Development</div>
                        </div>
                    </div>
                    <div class="speaker-bio">
                        Dr. Wanjiru has over 15 years experience in youth development programs across East Africa. He specializes in creating sustainable creative economy models that empower young entrepreneurs.
                    </div>
                </div>
                
                <div class="speaker-card">
                    <div class="speaker-header">
                        <div class="speaker-avatar">MA</div>
                        <div class="speaker-info">
                            <h3>Mariam Abdallah</h3>
                            <div class="speaker-org">Founder, Kilifi Creatives Hub</div>
                            <div class="speaker-topic">Cultural Preservation Through Art</div>
                        </div>
                    </div>
                    <div class="speaker-bio">
                        Mariam is a passionate advocate for preserving Kenyan cultural heritage through modern creative expressions. She has mentored over 200 young artists in Kilifi County.
                    </div>
                </div>
                
                <div class="speaker-card">
                    <div class="speaker-header">
                        <div class="speaker-avatar">JO</div>
                        <div class="speaker-info">
                            <h3>Joseph Odhiambo</h3>
                            <div class="speaker-org">Health Awareness Initiative</div>
                            <div class="speaker-topic">Community Health & Wellness</div>
                        </div>
                    </div>
                    <div class="speaker-bio">
                        With a background in public health, Joseph leads community-based health awareness programs focusing on cancer prevention and women's health issues in coastal regions.
                    </div>
                </div>
            </div> -->
            
            <!-- Panel Discussion -->
            <!-- <div class="panel-card">
                <h3 class="panel-title">Panel Discussion: Empowering Youth Through Culture, Creativity & Health Awareness</h3>
                <div class="panel-moderator">
                    <h4>Moderator:</h4>
                    <p>Sarah Chengo - Senior Editor, Coastal Times</p>
                </div>
                <div class="panel-moderator">
                    <h4>Panelists:</h4>
                    <p>Dr. Kamau Wanjiru (Youth Development Expert), Mariam Abdallah (Cultural Advocate), Joseph Odhiambo (Health Specialist), Amina Said (Youth Entrepreneur), Prof. James Mwangi (Pwani University)</p>
                </div>
                <div class="panel-topics">
                    <h4>Topics Covered:</h4>
                    <p>Youth empowerment strategies, Creative industries development, Cultural impact on youth, Health awareness integration, Mentorship opportunities, Sustainable funding models</p>
                </div>
            </div> -->
        </div>
    </section>

    <!-- Sponsors Section -->
    <section id="sponsors" class="page-section">
        <div class="container">
            <h2 class="section-title">Sponsors & Partners</h2>
            <p class="text-center mb-20">Join our network of partners committed to youth empowerment and community development.</p>
            
            <div class="sponsor-tiers">
                <div class="sponsor-tier">
                    <div class="tier-exclusive">Exclusive</div>
                    <h3 class="tier-title">Title Sponsor</h3>
                    <div class="tier-investment">KES 1,500,000+</div>
                    <div class="tier-benefits">
                        <ul>
                            <li>Exclusive naming rights to the summit</li>
                            <li>Primary logo placement across all platforms</li>
                            <li>Speaking opportunity during summit</li>
                            <li>VIP access to all activities</li>
                            <li>Featured in all press releases</li>
                        </ul>
                    </div>
                </div>
                
                <div class="sponsor-tier">
                    <h3 class="tier-title">Platinum Sponsor</h3>
                    <div class="tier-investment">KES 1,000,000+</div>
                    <div class="tier-benefits">
                        <ul>
                            <li>Prominent logo placement</li>
                            <li>Panel participation</li>
                            <li>VIP access to all events</li>
                            <li>Recognition during opening ceremony</li>
                            <li>Limited to two partners</li>
                        </ul>
                    </div>
                </div>
                
                <div class="sponsor-tier">
                    <h3 class="tier-title">Gold Sponsor</h3>
                    <div class="tier-investment">KES 500,000+</div>
                    <div class="tier-benefits">
                        <ul>
                            <li>Logo placement on website & materials</li>
                            <li>Exhibition space at the summit</li>
                            <li>Access to summit and gala</li>
                            <li>Recognition during sessions</li>
                            <li>Limited to four partners</li>
                        </ul>
                    </div>
                </div>
                
                <div class="sponsor-tier">
                    <h3 class="tier-title">Silver Sponsor</h3>
                    <div class="tier-investment">KES 250,000+</div>
                    <div class="tier-benefits">
                        <ul>
                            <li>Logo placement and mentions</li>
                            <li>Summit access for 2 representatives</li>
                            <li>Listing in event program</li>
                            <li>Social media recognition</li>
                            <li>Limited to six partners</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-40">
                <a href="#contact" class="btn btn-primary">Become a Sponsor</a>
            </div>
        </div>
    </section>

    <!-- Reviews Section -->
    <section id="reviews" class="reviews-section page-section">
        <div class="container">
            <h2 class="section-title">Feedback & Reviews</h2>
            <p class="text-center mb-20">Hear what past participants, sponsors, and community members say about our events.</p>
            
            <?php if(isset($review_success)): ?>
                <div class="success-message">
                    <?php echo $review_success; ?>
                </div>
            <?php endif; ?>
            
            <div class="reviews-container" id="reviewsContainer">
                <?php if(empty($reviews)): ?>
                    <p style="text-align: center;">No Reviews</p>
                    <!-- Default reviews if database is empty -->
                    <!-- <di/ -->
                <?php else: ?>
                    <?php foreach($reviews as $review): ?>
                        <?php
                        // Generate star rating HTML
                        $stars = '';
                        $rating = $review['rating'];
                        $fullStars = floor($rating);
                        $hasHalfStar = ($rating - $fullStars) >= 0.5;
                        
                        for($i = 0; $i < 5; $i++) {
                            if($i < $fullStars) {
                                $stars .= '<i class="fas fa-star"></i>';
                            } elseif($i == $fullStars && $hasHalfStar) {
                                $stars .= '<i class="fas fa-star-half-alt"></i>';
                            } else {
                                $stars .= '<i class="far fa-star"></i>';
                            }
                        }
                        
                        // Format date
                        $date = date('F j, Y', strtotime($review['created_at']));
                        ?>
                        <div class="review-card">
                            <div class="review-header">
                                <div class="review-avatar"><?php echo strtoupper(substr($review['name'], 0, 1)); ?></div>
                                <div class="reviewer-info">
                                    <h4><?php echo htmlspecialchars($review['name']); ?></h4>
                                    <div class="review-stars">
                                        <?php echo $stars; ?>
                                    </div>
                                    <p><?php echo htmlspecialchars($review['role']); ?></p>
                                </div>
                            </div>
                            <h4 style="color: var(--primary-green); margin-bottom: 10px;"><?php echo htmlspecialchars($review['title']); ?></h4>
                            <p><?php echo htmlspecialchars($review['review']); ?></p>
                            <div class="review-date">Posted: <?php echo $date; ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Submit Review Form -->
            <div class="submit-review-section">
                <?php if (isset($_GET['review']) && $_GET['review'] === 'success'): ?>
                    <div class="alert alert-success">
                        Thank you for your review! It has been submitted successfully.
                    </div>
                <?php endif; ?>
                <h3>Share Your Experience</h3>
                <p class="text-center mb-20">Attended our previous events? We'd love to hear your feedback!</p>
                
                <form class="review-form" method="POST" action="#reviews">
                    <input type="hidden" name="submit_review" value="1">
                    <div class="form-group">
                        <label for="reviewerName">Your Name *</label>
                        <input type="text" id="reviewerName" name="reviewerName" placeholder="Enter your name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reviewerRole">Your Role / Profession</label>
                        <input type="text" id="reviewerRole" name="reviewerRole" placeholder="e.g., Youth Participant, Sponsor, Volunteer">
                    </div>
                    
                    <div class="form-group">
                        <label>Rating *</label>
                        <div class="star-rating">
                            <input type="radio" id="star5" name="rating" value="5" required>
                            <label for="star5" title="5 stars">★</label>
                            <input type="radio" id="star4" name="rating" value="4">
                            <label for="star4" title="4 stars">★</label>
                            <input type="radio" id="star3" name="rating" value="3">
                            <label for="star3" title="3 stars">★</label>
                            <input type="radio" id="star2" name="rating" value="2">
                            <label for="star2" title="2 stars">★</label>
                            <input type="radio" id="star1" name="rating" value="1">
                            <label for="star1" title="1 star">★</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="reviewTitle">Review Title *</label>
                        <input type="text" id="reviewTitle" name="reviewTitle" placeholder="Summarize your experience" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reviewText">Your Review *</label>
                        <textarea id="reviewText" name="reviewText" rows="5" placeholder="Share your thoughts about the event, what you enjoyed, suggestions for improvement..." required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="eventAttended">Which event did you attend?</label>
                        <select id="eventAttended" name="eventAttended">
                            <option value="">Select Event</option>
                            <option value="2024">Gwira Gwira Summit 2024</option>
                            <option value="2023">Gwira Gwira Summit 2023</option>
                            <option value="other">Other Community Event</option>
                            <option value="first">This will be my first time</option>
                        </select>
                    </div>
                    
                    <button type="submit">Submit Review</button>
                </form>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="faq-section page-section">
        <div class="container">
            <h2 class="section-title">Frequently Asked Questions</h2>
            <div class="faq-container">
                <div class="faq-item">
                    <div class="faq-question">
                        Who can attend the summit?
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        Youth (15-35 years), creative practitioners, government officials, partners, media representatives, and anyone interested in youth empowerment and community development.
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        What is included with registration?
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        Access to all summit sessions, the Cancer Awareness Walk, Gala Night networking, workshop materials, meals during event hours, and a participation certificate.
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        How do I become a sponsor or partner?
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        Visit the Sponsors section above for partnership tiers, then contact us through the registration form or email partnerships@gwiragwira.org for detailed information.
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        Where is the venue and how do I get there?
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        The event will be held at Pwani University, Karisa Maitha Ground, and Inkaba Resort in Kilifi County. Detailed directions and transportation options will be provided upon registration.
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        Is accommodation available nearby?
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        Yes, we have partnered with several hotels and guesthouses in Kilifi offering discounted rates for summit attendees. A list will be provided upon registration.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="page-section">
        <div class="container">
            <h2 class="section-title">About the Event</h2>
            <div class="section-content">
                <h3 class="mb-20">Executive Summary</h3>
                <p class="mb-20">The Gwira Gwira Creative Summit, Cancer Walk Awareness & Gala Night is a 2-day event designed to empower youth through culture, creative activities and social impact initiatives.</p>
                
                <h3 class="mb-20">Objectives</h3>
                <ul class="mb-20" style="padding-left: 20px;">
                    <li>Empower youth through culture, arts, and creativity</li>
                    <li>Showcase youth entrepreneurship, innovation, and talent</li>
                    <li>Facilitate mentorship, networking, and potential funding opportunities</li>
                    <li>Align with government initiatives on youth empowerment</li>
                </ul>
                
                <h3 class="mb-20">Theme</h3>
                <p>"Empowering Youth Through Culture, Creativity and Health Awareness"</p>
            </div>
        </div>
    </section>

    <!-- Program Section -->
    <section id="program" class="page-section" style="background-color: var(--light-gray);">
        <div class="container">
            <h2 class="section-title">Program</h2>
            <div class="program-timeline">
                <div class="timeline-item">
                    <div class="timeline-date">Day 1</div>
                    <div class="timeline-content">
                        <h3>Cancer Walk Awareness Summit</h3>
                        <p>Community fun walk, interactive workshops along the walk (music, art, wellness), live performances from the youth, and networking opportunities.</p>
                        <p><strong>Time:</strong> 8:00 AM - 12:00 Noon</p>
                        <p><strong>Venue:</strong> Karisa Maitha Ground</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">Day 2</div>
                    <div class="timeline-content">
                        <h3>Youth Showcasing</h3>
                        <p>Youth exhibitions, workshops & mentorship, talent showcase, panel discussion with youth leaders, creatives, and health experts.</p>
                        <p><strong>Time:</strong> 10:00 AM - 4:00 PM</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">Day 2 Evening</div>
                    <div class="timeline-content">
                        <h3>Gala Night</h3>
                        <p>VIP reception, keynote speech by PS Youth & Creative Economy, performances, networking dinner, and awards ceremony.</p>
                        <p><strong>Time:</strong> 6:00 PM - 10:00 PM</p>
                        <p><strong>Venue:</strong> Inkaba Resort</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Committee Section -->
    <section id="committee" class="page-section" style="background-color: var(--light-gray);">
        <div class="container">
            <h2 class="section-title">Organizing Committee</h2>
            <div class="committee-members">
                <div class="committee-member">
                    <div class="member-avatar"><i class="fas fa-user-tie"></i></div>
                    <h3>Nomad Tours</h3>
                    <p>Event Organizer</p>
                    <p>Co-Prepared the Proposal</p>
                </div>
                <div class="committee-member">
                    <div class="member-avatar"><i class="fas fa-user-tie"></i></div>
                    <h3>Gwira Gwira</h3>
                    <p>Event Organizer</p>
                    <p>Co-Prepared the Proposal</p>
                </div>
                <div class="committee-member">
                    <div class="member-avatar"><i class="fas fa-user-tie"></i></div>
                    <h3>County Officials</h3>
                    <p>Government Liaison</p>
                    <p>Kilifi County Representatives</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="page-section">
        <div class="container">
            <h2 class="section-title">Contact & Registration</h2>

            <?php if (isset($_GET['registration']) && $_GET['registration'] === 'success'): ?>
                <div class="alert alert-success">
                    Thank you for registering! We will contact you soon with more details.
                </div>
            <?php endif; ?>
            
            <?php if(isset($registration_success)): ?>
                <div class="success-message">
                    <?php echo $registration_success; ?>
                </div>
            <?php endif; ?>
            
            <div class="contact-content">
                <div class="contact-info">
                    <h3>Event Details</h3>
                    <div class="contact-detail">
                        <i class="fas fa-calendar-alt"></i>
                        <div>
                            <strong>Dates:</strong><br>
                            28th & 29th March 2026
                        </div>
                    </div>
                    <div class="contact-detail">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Venues:</strong><br>
                            Pwani University <br>
                            Karisa Maitha Ground &<br>
                            Inkaba Resort, Kilifi
                        </div>
                    </div>
                    <div class="contact-detail">
                        <i class="fas fa-users"></i>
                        <div>
                            <strong>Organizers:</strong><br>
                            Nomad Tours & Gwira Gwira
                        </div>
                    </div>
                    
                    <h3 style="margin-top: 30px;">Social Media</h3>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="contact-form">
                    <h3>Register for the Summit</h3>
                    <p>Complete the form below to secure your participation.</p>
                    <form method="POST" action="#contact">
                        <input type="hidden" name="submit_registration" value="1">
                        <input type="text" name="reg_name" placeholder="Your Name" required>
                        <input type="email" name="reg_email" placeholder="Your Email" required>
                        <input type="tel" name="reg_phone" placeholder="Phone Number" required>
                        <input type="text" name="reg_organization" placeholder="Organization (if applicable)">
                        <select name="reg_type" required>
                            <option value="">Select Registration Type</option>
                            <option value="youth">Youth Participant</option>
                            <option value="volunteer">Volunteer</option>
                            <option value="partner">Partner Organization</option>
                            <option value="media">Media/Press</option>
                            <option value="sponsor">Sponsor</option>
                        </select>
                        <select name="reg_days" required>
                            <option value="">Select Attendance Days</option>
                            <option value="both">Both Days (Full Summit)</option>
                            <option value="day1">Day 1 Only (Cancer Walk)</option>
                            <option value="day2">Day 2 Only (Youth Showcase)</option>
                            <option value="gala">Gala Night Only</option>
                        </select>
                        <textarea name="reg_notes" rows="4" placeholder="Additional Notes or Special Requirements"></textarea>
                        <button type="submit">Register Now</button>
                    </form>
                    <p class="text-center mt-20">
                        <a href="#" style="color: var(--primary-green); text-decoration: none;">
                            <i class="fas fa-download"></i> Download Summit Brochure
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#speakers">Speakers</a></li>
                        <li><a href="#program">Program</a></li>
                        <li><a href="#sponsors">Sponsors</a></li>
                        <li><a href="#reviews">Reviews</a></li>
                        <li><a href="#faq">FAQ</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Downloads</h3>
                    <ul class="footer-links">
                        <li><a href="#">Event Program</a></li>
                        <li><a href="#">Sponsorship Package</a></li>
                        <li><a href="#">Volunteer Form</a></li>
                        <li><a href="#">Summit Brochure</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Legal</h3>
                    <ul class="footer-links">
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms & Conditions</a></li>
                        <li><a href="#">Code of Conduct</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Organizer Details</h3>
                    <p>Gwira Gwira Creative Summit, Cancer Walk Awareness & Gala Night is organized by Nomad Tours in partnership with Gwira Gwira.</p>
                    <p>Email: info@gwiragwirasummit.co.ke</p>
                    <p>Phone: +254 795 092 139</p>
                    <p>© 2026 Gwira Gwira Summit. All rights reserved.</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>Empowering Youth Through Culture, Creativity and Health Awareness</p>
                <p>#GwiraGwiraYouth #KilifiYouth #EmpoweringYouth</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            const nav = document.getElementById('mainNav');
            nav.classList.toggle('active');
            this.innerHTML = nav.classList.contains('active') ? 
                '<i class="fas fa-times"></i>' : 
                '<i class="fas fa-bars"></i>';
        });
        
        // Countdown Timer
        function updateCountdown() {
            const summitDate = new Date('March 28, 2026 08:00:00').getTime();
            const now = new Date().getTime();
            const timeLeft = summitDate - now;
            
            if (timeLeft > 0) {
                const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                
                document.getElementById('days').textContent = days.toString().padStart(2, '0');
                document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
                document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
                document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
            } else {
                document.querySelector('.countdown-title').textContent = "Summit Has Started!";
                document.querySelector('.countdown-container').innerHTML = '<p style="font-size: 1.2rem;">Join us now at the venues!</p>';
            }
        }
        
        // Update countdown every second
        setInterval(updateCountdown, 1000);
        updateCountdown(); // Initial call
        
        // FAQ Toggle
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const item = question.parentElement;
                item.classList.toggle('active');
                
                const toggle = question.querySelector('.faq-toggle');
                toggle.textContent = item.classList.contains('active') ? '−' : '+';
            });
        });
        
        // Smooth Scrolling for Navigation Links
        document.querySelectorAll('nav a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                // Close mobile menu if open
                if(window.innerWidth <= 768) {
                    document.getElementById('mainNav').classList.remove('active');
                    document.getElementById('mobileMenuBtn').innerHTML = '<i class="fas fa-bars"></i>';
                }
                
                // Update active nav link
                document.querySelectorAll('nav a').forEach(navLink => {
                    navLink.classList.remove('active');
                });
                this.classList.add('active');
                
                // Scroll to target
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            });
        });
        
        // Highlight active section on scroll
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('nav a');
            
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if(pageYOffset >= (sectionTop - 150)) {
                    current = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if(link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>