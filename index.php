<?php
session_start();

if (!isset($_SESSION["balance"])) {
    $_SESSION["balance"] = 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["button1"])) {
        $_SESSION["balance"] += 100;
    } elseif (isset($_POST["button2"])) {
        $_SESSION["balance"] += 200;
    } elseif (isset($_POST["button3"])) {
        $_SESSION["balance"] += 300;
    } elseif (isset($_POST["delete"])) {
        $_SESSION["balance"] = 0;
    } elseif (isset($_POST["custom_deposit"])) {
        $amount = intval($_POST["custom_amount"]);
        if($amount > 0 ) {
            $_SESSION["balance"] += $amount;
        }
    }

    header("Location: /");
    exit();
}

$balance = $_SESSION["balance"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balance Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body class="bg-light">
    <div class="container py-5">
        <h1 class="mb-4 text-center">ATM</h1>

        <form method="post" class="mb-4">
            <div class="input-group atm-custom-deposit">
                <input type="number" name="custom_amount" class="form-control" placeholder="Enter custom amount" min="1" required>
                <button type="submit" name="custom_deposit" class="btn btn-outline-primary">Deposit</button>
            </div>
        </form>


        <form method="post">
            <div class="card p-4 shadow-sm">
                <div class="mb-3">
                    <input type="text" id="balance" class="form-control text-center fw-bold" value="<?php echo $balance; ?>$" disabled>
                </div>

                <div class="d-flex justify-content-between mb-3">
                    <input type="submit" value="Add 100$" name="button1" class="btn btn-primary">
                    <input type="submit" value="Add 200$" name="button2" class="btn btn-success">
                    <input type="submit" value="Add 300$" name="button3" class="btn btn-warning">
                </div>

                <div class="text-center">
                    <input type="submit" value="Withdraw all" name="delete" class="btn btn-danger" onclick="customFunction()">
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
        $("form").on("submit", function () {
            const $balance = $("#balance");

            $balance
                .css({ backgroundColor: "#d1e7dd" })
                .animate({ fontSize: "2.5rem" }, 200)
                .animate({ fontSize: "2rem" }, 200, function () {
                    $balance.css({ backgroundColor: "" });
                });
        });
    });
    </script>
    <script>
        $(document).ready(function () {
        $('input[name="delete"]').hover(function () {
            $(this).css("background-color", "#dc3545");
        });

        $('input[name="custom_amount"]').on("input", function () {
            console.log("Customer just entered: " + $(this).val() + "$");
        });
    });
    </script>
    <script>
        function customFunction() {
            alert("All money was withdrawed");
        }
    </script>
</body>

</html>