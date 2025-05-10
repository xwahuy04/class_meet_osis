<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Meeting Registration System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #fffbeb; /* amber-50 */
            min-height: 100vh;
        }
        .header-bg {
            background-color: #f59e0b; /* amber-500 */
        }
        .card-custom {
            border-color: #fde68a; /* amber-200 */
            background-color: white;
        }
        .btn-amber {
            background-color: #f59e0b; /* amber-500 */
            color: white;
        }
        .btn-amber:hover {
            background-color: #d97706; /* amber-600 */
            color: white;
        }
        .footer-bg {
            background-color: #f59e0b; /* amber-500 */
        }
        .text-amber-800 {
            color: #92400e; /* amber-800 */
        }
    </style>
</head>
<body>
    <header class="header-bg p-5 shadow">
        <div class="container">
            <h1 class="text-white font-weight-bold">Class Meeting Registration System</h1>
        </div>
    </header>

    <main class="container py-5 px-4">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card card-custom shadow-lg">
                    <div class="card-body p-4">
                        <h2 class="text-amber-800 mb-3 font-weight-bold">Register for Class Meeting</h2>
                        <p class="text-secondary mb-4">
                            Fill out the registration form for your class to participate in the class meeting events.
                        </p>
                        <a href="views/auth/register.php" class="btn btn-amber btn-block">Register Now</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card card-custom shadow-lg">
                    <div class="card-body p-4">
                        <h2 class="text-amber-800 mb-3 font-weight-bold">Admin Dashboard</h2>
                        <p class="text-secondary mb-4">
                            For administrators to track registration status and manage class meeting events.
                        </p>
                        <a href="admin/auth/login.php" class="btn btn-amber btn-block">Admin Login</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer-bg p-4 text-center text-white">
        <p>© <?php echo date('Y'); ?> Class Meeting Registration System</p>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="js/bootstrap.min.js"></script>
</body>
</html>