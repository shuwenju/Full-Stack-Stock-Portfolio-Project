<?php include 'header.php';

include "dbconnection.php";
if (isset($_GET['delid'])) {
    $transid = $_GET['delid'];
    $sql = mysqli_query($connection, "delete from user_financial_info where user_stock_id='$transid'");
    echo "<script>alert('Data deleted');</script>";
    echo "<script>window.location.href = 'portfolio.php'</script>";
}


//ADDING A STOCK
if (isset($_POST['addStock'])) {
    $bDateErr = $sIniPriceErr = $quantity = "";
    $sIniPrice = $sName = $quantityErr = "";

    $sql = "SELECT stock_name FROM stock_info WHERE stock_ticker=?";
    $stmt = mysqli_prepare($connection, $sql);
    $stmt->bind_param("s", $sTicker);
    $sTicker = $_POST['stock_ticker'];
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $sName = $row['stock_name'];
    }

    //Validate date:
    // Check if date is more than 50 years prior to today
    $minDate = new DateTime('-50 years');

    if (empty($_POST["buying_date"])) {
        $bDateErr = "Please enter bought date";
    } else if (strtotime($_POST["buying_date"]) > time()) {
        // Date is in the future
        $bDateErr = 'Date cannot be in the future';
    } else if (date('N', strtotime($_POST["buying_date"])) >= 6) {
        // Date is a weekend
        $bDateErr = 'Date can only be a week day';
    } else if (new DateTime($_POST["buying_date"]) < $minDate) {
        $bDateErr = 'Date cannot be more than 50 years prior to today';
    } else {
        $bDate = filter_input(INPUT_POST, 'buying_date', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    //Validate price:
    if (empty(trim($_POST["stock_initial_price"]))) {
        $sIniPriceErr = "Please enter bought price";
    } else if ($_POST['stock_initial_price'] > 2000 || $_POST['stock_initial_price'] <= 0) {
        $sIniPriceErr = "Only allowed 0-2000";
    } else {
        $sIniPrice = filter_input(INPUT_POST, 'stock_initial_price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND);
    }

    //Validate quantity:
    if (empty(trim($_POST["quantity"]))) {
        $quantityErr = "Please enter bought quantity";
    } else if ($_POST['quantity'] > 1000 || $_POST['quantity'] <= 0) {
        $quantityErr = "Only allowed 0-1000";
    } else {
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND);
    }

    $userID = $_SESSION['user_id'];

    // Query for data insertion
    if (empty($sIniPriceErr) && empty($bDateErr) && empty($quantityErr)) {
        $query = mysqli_query($connection, "insert into user_financial_info(user_id, stock_ticker, stock_name, stock_initial_price, buying_date, quantity) values ($userID, '$sTicker', '$sName', $sIniPrice, '$bDate', $quantity)");
        if ($query) {
            echo "<script>alert('You have successfully inserted the data');</script>";
            echo "<script type='text/javascript'> document.location ='portfolio.php'; </script>";
        }
    } else {
        echo "<script>alert('Invalid inputs. Please try again');</script>";
    }
}

//EDIT STOCK
if (isset($_POST['savechanges'])) {
    $editDate = $editPrice = $editQuan = "";
    $editDateErr = $editPriceErr = $editQuanErr = "";

    //validate date:
    // Check if date is more than 50 years prior to today
    $minDate = new DateTime('-50 years');

    if (empty($_POST["editDate"])) {
        $editDateErr = "Please enter bought date";
    } else if (strtotime($_POST["editDate"]) > time()) {
        // Date is in the future
        $editDateErr = 'Date cannot be in the future';
    } else if (date('N', strtotime($_POST["editDate"])) >= 6) {
        // Date is a weekend
        $editDateErr = 'Date can only be a week day';
    } else if (new DateTime($_POST["editDate"]) < $minDate) {
        $editDateErr = 'Date cannot be more than 50 years prior to today';
    } else {
        $editDate = filter_input(INPUT_POST, 'editDate', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }


    //validate price:
    if (empty(trim($_POST["editPrice"]))) {
        $editPriceErr = "Please enter bought price";
    } else if ($_POST['editPrice'] > 2000 || $_POST['editPrice'] <= 0) {
        $editPriceErr = "Only allowed 0-2000";
    } else {
        $editPrice = filter_input(INPUT_POST, 'editPrice', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND);
        echo $editPrice;
    }


    //validate quantity:
    if (empty(trim($_POST["editquantity"]))) {
        $editQuanErr = "Please enter bought quantity";
    } else if ($_POST['editquantity'] > 1000 || $_POST['editquantity'] <= 0) {
        $editQuanErr = "Only allowed 0-1000";
    } else {
        $editQuan = filter_input(INPUT_POST, 'editquantity', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND);
    }

    $stockTransId = $_POST['user_stock_id'];
    if (empty($editDateErr) && empty($editPriceErr) && empty($editQuanErr)) {
        $queryedit = mysqli_query($connection, "UPDATE user_financial_info SET stock_initial_price=$editPrice, buying_date='$editDate', quantity=$editQuan WHERE user_stock_id=$stockTransId");
        if ($queryedit) {
            echo "<script>alert('You have successfully updated the data');</script>";
            echo "<script type='text/javascript'> document.location ='portfolio.php'; </script>";
        }
    } else {
        echo "<script>alert('Invalid inputs. Please try again');</script>";
    }
} ?>


<body>
    <!-- Charts -->
    <div class="container" style="margin-top: 10rem;">
        <div class="row">
            <div style=" min-height: 300px;" class="col-6">
                <canvas id="barChart"></canvas>
            </div>
            <div style="min-height: 300px; min-width: 300px; max-height: 300px; display: flex; justify-content: center; align-items: center;"
                class="col-6">
                <canvas id="donutChart"></canvas>
            </div>
        </div>
    </div>
    <div class="container" style="margin-top: 3rem;">
        <div class="row"
            style="display: flex; flex-direction: row; justify-content: center; align-items: center; text-align: center;">
            <div class="col"
                style="display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
                <h1 class="currentValueTitle">The total value:</h1>
                <h1 id="currentValue" style="background-color: grey; padding: 0.5rem 1rem; border-radius: 8px;">0</h1>
            </div>
            <div class="col"
                style="display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
                <h1 class="totalValueTitle">The Original Value</h1>
                <h1 id="totalValue" style="background-color: grey; padding: 0.5rem 1rem; border-radius: 8px;">0</h1>
            </div>
        </div>
    </div>

    <!-- Database Table -->
    <div class="row justify-content-center" style="margin-top: 5rem;">
        <div class="col-md-11 2 p-4 table-responsive">
            <table class="table table-hover py-5" style="  
            background: rgba(255, 255, 255, 1);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(0px);
            -webkit-backdrop-filter: blur(0px);
            margin-bottom: 16rem;
            ">
                <thead>
                    <tr>
                        <th scope="col">Stock ticker</th>
                        <th scope="col">Stock name</th>
                        <th scope="col">Bought price</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Total Value</th>
                        <th scope="col">Bought date</th>
                        <th scope="col">Current Value</th>
                        <th scope="col">W/L</th>
                        <th scope="col">% Difference</th>
                        <th scope="col"><button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#exampleModal">Add a stock</button></th>
                    </tr>

                    <!-- Modal to add a stock -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Add a stock</h5>
                                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                                        <div class="form-group">
                                            <!-- Dropdown list -->
                                            <select class="form-select" name="stock_ticker"
                                                aria-label="Default select example" required>
                                                <option selected></option>
                                                <?php $sql = "SELECT stock_ticker, stock_name FROM stock_info ORDER BY stock_ticker ASC";
                                                $result = mysqli_query($connection, $sql);
                                                if (mysqli_num_rows($result) > 0) {
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        echo '<option value="' . $row['stock_ticker'] . '">' . $row['stock_ticker'] .
                                                            ' - ' . $row['stock_name'] . '</option>';
                                                    }
                                                } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="stock_initial_price">Price:</label>
                                            <input type="number"
                                                class="form-control <?php echo (!empty($sIniPriceErr)) ? 'is-invalid' : ''; ?>"
                                                id="stock_initial_price" name="stock_initial_price" step=".01" required>
                                            <small <?php echo (!empty($sIniPriceErr)) ? "class='invalid-feedback'" : "" ?>>
                                                <?php echo (!empty($sIniPriceErr)) ? "{$sIniPriceErr}" : "" ?>
                                            </small>
                                            <div class="form-group">
                                                <label for="buying_date">Buying Date:</label>
                                                <input type="date"
                                                    class="form-control <?php echo (!empty($bDateErr)) ? 'is-invalid' : ''; ?>"
                                                    id="buying_date" name="buying_date" required>
                                                <small <?php echo (!empty($bDateErr)) ? "class='invalid-feedback'" : "" ?>><?php
                                                       echo (!empty($bDateErr)) ? "{$bDateErr}" : "" ?>
                                                </small>
                                            </div>
                                            <div class="form-group">
                                                <label for="quantity">Quantity:</label>
                                                <input type="number" step="0.01"
                                                    class="form-control <?php echo (!empty($quantityErr)) ? 'is-invalid' : ''; ?>"
                                                    id="quantity" name="quantity" required>
                                                <small <?php echo (!empty($quantityErr)) ? "class='invalid-feedback'" : "" ?>><?php
                                                       echo (!empty($quantityErr)) ? "{$quantityErr}" : "" ?>
                                                </small>
                                            </div>
                                            <br>
                                            <br>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary" name="addStock">Add
                                                    stock</button>
                                            </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </thead>
                <tbody>
                    <?php
                    // DISPLAYING THE DATA
                    $sql = "SELECT user_stock_id, stock_ticker, stock_name, stock_initial_price, buying_date, quantity 
               FROM user_financial_info 
               WHERE user_id=?
               ORDER BY stock_ticker, buying_date DESC";
                    // Prepare the statement and bind the session variable to the parameter
                    $stmt = mysqli_prepare($connection, $sql);
                    $stmt->bind_param("i", $_SESSION['user_id']);

                    // Execute the statement
                    $stmt->execute();

                    // Get the result set
                    $result = $stmt->get_result();

                    // Initialize a variable to keep track of the previous stock ticker
                    $prevStockTicker = null;

                    // Generate the HTML for the table
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Check if the stock ticker has changed since the last row
                            if ($row["stock_ticker"] !== $prevStockTicker) {
                                // If it has, display the stock ticker header and update the previous stock ticker variable
                                echo "<tr><th colspan='10'>" . $row["stock_ticker"] . "</th></tr>";
                                $prevStockTicker = $row["stock_ticker"];
                            }

                            // Display the row data
                            echo "<tr class='realRow'>";
                            echo "<td>" . $row["stock_ticker"] . "</td>";
                            echo "<td>" . $row["stock_name"] . "</td>";
                            echo "<td>" . $row["stock_initial_price"] . "$" . "</td>";
                            echo "<td>" . $row["quantity"] . "</td>";
                            echo "<td>" . $row["quantity"] * $row['stock_initial_price'] . "$" . "</td>";
                            echo "<td>" . $row["buying_date"] . "</td>";
                            echo "<td class='currentValue'>" . "0" . "</td>";
                            echo "<td class='totalMade'>" . "0" . "</td>";
                            echo "<td class='percentageDifference'>" . "%" . "</td>";
                            echo '<td>
                              <button type="button" class="btn btn-outline-danger btn-sm" onclick="return confirm(\'Do you really want to Delete ?\');">
                                 <a href="portfolio.php?delid=' . $row['user_stock_id'] . '" style="text-decoration: none; color: red;" onmouseover="this.style.color=\'white\'" onmouseout="this.style.color=\'red\'">Delete</a>
                              </button>
                              <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#editModal' . $row['user_stock_id'] . '">
                              Edit
                              </button>
                           </td>';
                            echo "</tr>";



                            // Display edit modal:
                            echo '<div class="modal fade" id="editModal' . $row['user_stock_id'] . '" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">';
                            echo '<div class="modal-dialog" role="document">';
                            echo '<div class="modal-content">';
                            echo '<div class="modal-header">';
                            echo '<h5 class="modal-title" id="editModalLabel">Edit Stock</h5>';
                            echo '<button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>';
                            echo '</div>';
                            echo '<div class="modal-body">';
                            // form editing the stock
                            echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '" method="post">';
                            echo '<input type="hidden" name="user_stock_id" value="' . $row['user_stock_id'] . '">';

                            // add input here, should we only allow user to change price, date and quantity? and leave the ticker alone
                            echo '<div class="form-group">';
                            echo '<label for="stock_ticker">Stock ticker:</label>';
                            echo '<input type="text" class="form-control" id="stock_ticker" name="stock_ticker" value="' . $row['stock_ticker'] . '" readonly>';
                            echo '</div>';

                            echo '<div class="form-group">';
                            echo '<label for="editPrice">Price:</label>';
                            echo '<input type="number" step="0.01" class="form-control ' . (!empty($editPriceErr) ? 'is-invalid' : '') . '" id="editPrice" name="editPrice" value="' . $row['stock_initial_price'] . '" required>';
                            echo '<small ' . (!empty($editPriceErr) ? 'class="invalid-feedback"' : '') . '>' . (!empty($editPriceErr) ? "{$editPriceErr}" : "") . '</small>';
                            echo '</div>';

                            echo '<div class="form-group">';
                            echo '<label for="editDate">Buying Date:</label>';
                            echo '<input type="date" class="form-control ' . (!empty($editDateErr) ? 'is-invalid' : '') . '" id="editDate" name="editDate" value="' . $row['buying_date'] . '" required>';
                            echo '<small ' . (!empty($editDateErr) ? 'class="invalid-feedback"' : '') . '>' . (!empty($editDateErr) ? "{$editDateErr}" : "") . '</small>';
                            echo '</div>';

                            echo '<div class="form-group">';
                            echo '<label for="editquantity">Quantity:</label>';
                            echo '<input type="number" step="0.01" class="form-control ' . (!empty($editQuanErr) ? 'is-invalid' : '') . '" id="editquantity" name="editquantity" value="' . $row['quantity'] . '" required>';
                            echo '<small ' . (!empty($editQuanErr) ? 'class="invalid-feedback"' : '') . '>' . (!empty($editQuanErr) ? "{$editQuanErr}" : "") . '</small>';
                            echo '</div>';

                            echo '</div>';
                            echo '<div class="modal-footer">';
                            echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> ';
                            echo '<button type="submit" name="savechanges" class="btn btn-primary">Save changes</button>';
                            echo '</div>';
                            echo '</form>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo "<tr><td colspan='5'>No stocks found.</td></tr>";
                    }

                    // Close the database connection
                    mysqli_stmt_close($stmt); ?>

                </tbody>
            </table>
        </div>
    </div>



    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
        </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
        </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
        </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        <?php
        $sql = "SELECT stock_ticker, SUM(quantity), SUM(stock_initial_price * quantity) AS total_price 
            FROM user_financial_info WHERE user_id = ? 
            GROUP BY stock_ticker ORDER BY buying_date DESC;";

        $stmt = mysqli_prepare($connection, $sql);
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetching the result into an array
        $labels = array(); // ticker
        $quantity = array(); // quantity
        $data = array(); // total_price 
        while ($row = $result->fetch_assoc()) {
            array_push($labels, $row['stock_ticker']);
            array_push($quantity, $row['SUM(quantity)']);
            array_push($data, $row['total_price']);
        }

        $numLabels = count($labels);
        ?>
        document.addEventListener('DOMContentLoaded', function () {
            let date = new Date();
            let dateDay = date.getDay();
            let dateDelay;
            switch (dateDay) {
                case 0: // if day is sunday, set it to friday's data (-2 days)
                    dateDelay = 2;
                    break;
                case 1: // if day is monday, set it to previous friday's data (-3 days)
                    dateDelay = 3;
                    break;
                default: // otherwise just display previous days data
                    dateDelay = 1;
                    break;
            }

            date.setDate(date
                .getDate() - dateDelay
            );
            // api restrictions, we are only able to get real time data but from a few days ago, lets assume this is today (if we paid)
            const options = {
                timeZone: 'America/New_York',
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            };
            const [month, day, year] = date.toLocaleDateString('en-US', options).split('/');
            const estDateString = `${year}-${month}-${day}`;
            const labels = <?php echo json_encode($labels); ?>;
            const quantity = <?php echo json_encode($quantity); ?>;
            fetch(
                `https://api.polygon.io/v2/aggs/grouped/locale/us/market/stocks/${estDateString}?adjusted=true&apiKey=s1CiYkbEYtGhioKpTfEqk34nvER1yo7P`
            )
                .then(response => response.json())
                .then(data => {

                    let extractedValues = new Array(labels.length).fill(
                        0); // set new array same length as labels
                    for (let i = 0; i < data.results.length; i++) {
                        const ticker = data.results[i]
                            .T; // extract all the tickers from the json result set
                        const index = labels.indexOf(
                            ticker
                        ); // assign value to index with the value of the ticker, if ticker is not found, index would be -1
                        if (index !== -1) {
                            extractedValues[index] = data.results[i].c;
                        }
                    }
                    const multipliedValues = quantity.map((value, index) => value * extractedValues[
                        index]);
                    // 2 arguments passed, value of quantity and index of quantity, 
                    // const multipliedValues = quantity.map(function(value, index) { return value * extractedValues[index]};
                    // console.log(labels);
                    // console.log(extractedValues);
                    // console.log(quantity);


                    const ctx = document.getElementById('barChart');
                    const DATA_COUNT = <?php echo json_encode($numLabels); ?>;
                    const NUMBER_CFG = {
                        count: DATA_COUNT,
                        min: 0,
                        max: 200
                    };
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Bought price',
                                data: <?php echo json_encode($data); ?>,
                                borderColor: 'rgb(255, 99, 132)',
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            },
                            {
                                label: 'Today price',
                                data: multipliedValues,
                                borderColor: 'rgb(54, 162, 235)',
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            }
                            ]
                        }
                    });
                })
                .catch(error => console.error(error));
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const rows = document.getElementsByClassName('realRow');
            let currentValueAggregate = document.getElementById('currentValue');
            let totalValueAggregate = document.getElementById('totalValue')
            let valueArray = [];
            let totalArray = [];

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const tickerCell = row.cells[0];
                const boughtPriceCell = row.cells[2];
                const quantityCell = row.cells[3];
                const totalValueCell = row.cells[4];
                const boughtDateCell = row.cells[5];
                const totalMadeCell = row.cells[7];
                const currentValueCell = row.cells[6];
                const percentDiffCell = row.cells[8];

                // Get the ticker symbol from the ticker cell
                const ticker = tickerCell.textContent;

                // Get the bought date from the bought date cell
                const boughtDate = boughtDateCell.textContent;

                // Make the API call
                const url =
                    `https://api.polygon.io/v2/aggs/grouped/locale/us/market/stocks/${boughtDate}?adjusted=true&apiKey=Z_zX56RQ6bX8J2mo3liB8VuoQmOppEnv`;
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        // Find the latest closing price for the ticker symbol
                        const latestClose = data.results.find(result => result.T === ticker).c;

                        // Get the bought price and quantity from their cells
                        const boughtPrice = boughtPriceCell.textContent.substring(0, (boughtPriceCell
                            .textContent.length - 1));
                        const quantity = Number(quantityCell.textContent);
                        totalValue = totalValueCell.textContent.substring(0, (totalValueCell.textContent
                            .length - 1));

                        // Calculate the current value and percent difference
                        const currentValue = latestClose * quantity;
                        valueArray.push(currentValue);
                        totalArray.push(Number(totalValue));
                        const percentDiff = ((latestClose - boughtPrice) / boughtPrice) * 100;
                        const dollarDiff = currentValue - totalValue;
                        let sumOriginal = totalArray.reduce((a, b) => a + b, 0);
                        totalValueAggregate.textContent = sumOriginal.toFixed(2) + '$';
                        let sumCurrent = valueArray.reduce((a, b) => a + b, 0);
                        let totalDifference = sumCurrent - sumOriginal;
                        currentValueAggregate.textContent = sumCurrent.toFixed(2) + '$' + (totalDifference > 0 ?
                            ` (+${totalDifference.toFixed(2)}$)` : `(${totalDifference.toFixed(2)}$)`);

                        // Update the current value and percent difference cells
                        currentValueCell.textContent = currentValue.toFixed(2);
                        totalMadeCell
                            .textContent = dollarDiff.toFixed(2) + "$";
                        percentDiffCell.textContent =
                            percentDiff.toFixed(1) + '%';

                        // Change the text color of the percent difference cell based on whether it's positive or negative
                        percentDiffCell.style.color = percentDiff >= 0 ? 'green' : 'red';
                        currentValueCell.style.color = currentValue >
                            totalValue ?
                            'green' :
                            'red';
                        totalMadeCell.style.color = dollarDiff > 0 ? 'green' :
                            'red';
                        currentValueAggregate.style.backgroundColor = totalDifference > 0 ? 'green' : 'red';
                        currentValueAggregate.style.color = totalDifference > 0 ? 'white' : 'white';
                        totalValueAggregate.style.color = totalDifference > 0 ? 'white' : 'white';
                    })
                    .catch(error => {
                        console.log(`Error fetching data for ${ticker}: ${error}`);
                    });
            }
        });
    </script>
    <script>
        <?php
        // include "dbconnection.php";
        $sql = "SELECT stock_ticker, SUM(stock_initial_price * quantity) AS total_price 
           FROM user_financial_info 
           WHERE user_id=? 
           GROUP BY stock_ticker
           ORDER BY buying_date DESC";

        $stmt = mysqli_prepare($connection, $sql);
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetching the result into an array
        $labels = array();
        $data = array();
        while ($row = $result->fetch_assoc()) {
            array_push($labels, $row['stock_ticker']);
            array_push($data, $row['total_price']);
        }

        // Generating random background colors for each label
        $bgColors = array();
        $numLabels = count($labels);
        for ($i = 0; $i < $numLabels; $i++) {
            $bgColors[] = 'rgb(' . rand(0, 255) . ', ' . rand(0, 255) . ', ' . rand(0, 255) . ')';
        }
        ?>

        const ctx = document.getElementById('donutChart');

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($data); ?>,
                    backgroundColor: <?php echo json_encode($bgColors); ?>,
                    hoverOffset: 4
                }]
            }
        });
    </script>
    <?php include("../HTML/footer.html"); ?>
</body>

</html>