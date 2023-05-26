<?php include "header.php"; ?>

<!-- Header-->
<header class="bg-dark py-5">
    <div class="container px-5">
        <div class="row gx-5 justify-content-center">
            <div class="col-lg-6">
                <div class="text-center my-5">
                    <h1 class="display-5 fw-bolder text-white mb-2">Stockinging: analyzing stock is never easier</h1>
                    <p class="lead text-white-50 mb-4">See what you own, analyze and compare with today's real time
                        market data!</p>
                    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                        <a class="btn btn-primary btn-lg px-4 me-sm-3" href="login.php">Get Started</a>
                        <a class="btn btn-outline-light btn-lg px-4" href="#features">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Features section-->
<section class="py-5 border-bottom" id="features">
    <div class="container px-5 my-5">
        <div class="row gx-5">
            <div class="col-lg-4 mb-5 mb-lg-0">
                <div class="feature bg-primary bg-gradient text-white rounded-3 mb-3"><i
                        class="bi bi-bar-chart-line"></i></div>
                <h2 class="h4 fw-bolder">View live market data</h2>
                <p>I dont know what I'm talking about, I guess I will tell a joke on the next one. Please support by
                    signing up to populate our mock database, so that i dont have to put dummy data manually.</p>
                <a class="text-decoration-none" href="login.php">
                    See more <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="col-lg-4 mb-5 mb-lg-0">
                <div class="feature bg-primary bg-gradient text-white rounded-3 mb-3"><i class="bi bi-building"></i>
                </div>
                <h2 class="h4 fw-bolder">Making smart decisions</h2>
                <p>Why did the stock market refuse to move? Because it was afraid of taking a stock.</p>
                <a class="text-decoration-none" href="login.php">
                    See more
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="col-lg-4">
                <div class="feature bg-primary bg-gradient text-white rounded-3 mb-3"><i class="bi bi-cash-stack"></i>
                </div>
                <h2 class="h4 fw-bolder">Stock management</h2>
                <p>Do you stock green tea? What about stocking every imaginable type of rolling paper?</p>
                <a class="text-decoration-none" href="login.php">
                    See more
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>


<!-- Image element - set the background image for the header in the line below-->
<div class="py-5 bg-image-full"
    style="background-image: url('../stockpage/hero.jpg'); background-size: cover; background-position: center;">
    <div style="height: 30rem"></div>
</div>


<!-- Cards -->
<div class="py-5">
    <div class="container text-center">
        <h1 class="display-4 fw-bold">Stocks we offer</h1>
        <p class="lead mb-4">Discover stocks we offer & so much more</p>
        <div class="row hidden-md-up" style="margin-bottom: 50px; margin-top: 50px;">
            <!-- Displaying random stocks from database -->
            <?php include "dbconnection.php";
            $sql = "SELECT stock_ticker, stock_name FROM stock_info ORDER BY RAND() LIMIT 12";
            $result = mysqli_query($connection, $sql);

            // Check if any rows were returned
            if (mysqli_num_rows($result) > 0) {
                // Loop through each row and retrieve the data
                while ($row = mysqli_fetch_assoc($result)) {
                    // Populate the data into the h4 tag in HTML
                    echo
                        '<div class="col-md-3" style="margin-bottom: 20px;">
                     <div class="card" style="padding: 5px;">
                     <div class="card-block">
                    <h4 class="card-title">' . $row["stock_ticker"] . '</h4>
                    <h6 class="card-subtitle text-muted">' . $row["stock_name"] . '</h6>
                    </div>
                    </div>
                    </div>';
                }
            } else
                echo "No results found.";

            // Close the database connection
            mysqli_close($connection);
            ?>
        </div>
    </div>
</div>
<hr style="width: 100%;">

<!-- Testimonials section-->
<section class="py-5 border-bottom">
    <div class="container px-5 my-5 px-5">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">Customer testimonials</h2>
            <p class="lead mb-0">Our customers love working with us</p>
        </div>
        <div class="row gx-5 justify-content-center">
            <div class="col-lg-6">
                <!-- Testimonial 1-->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <div class="flex-shrink-0"><i class="bi bi-chat-right-quote-fill text-primary fs-1"></i>
                            </div>
                            <div class="ms-4">
                                <p class="mb-1">Thank you for putting together such a great product. We love charts,
                                    especially pie charts. Real pie is much better</p>
                                <div class="small text-muted">- Client Name, Location</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Testimonial 2-->
                <div class="card">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <div class="flex-shrink-0"><i class="bi bi-chat-right-quote-fill text-primary fs-1"></i>
                            </div>
                            <div class="ms-4">
                                <p class="mb-1">The whole team was a huge help with putting things together, I used this
                                    website to see my stocks every 857 seconds</p>
                                <div class="small text-muted">- Client Name, Location</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include("../HTML/footer.html");
?>