<?php
session_start();
require_once 'db.php';

interface AccountInterface
{
    public function deposit(int $amount): void;
    public function withdrawAll(): void;
    public function getBalance(): int;
}

class Account implements AccountInterface
{
    private PDO $pdo;
    private int $userId;
    private string $sessionKey = "balance";

    public function __construct(PDO $pdo, int $userId) {
    $this->pdo = $pdo;
    $this->userId = $userId;

    $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM accounts WHERE user_id = :id");
    $stmt->execute(['id' => $userId]);
    $exists = $stmt->fetchColumn();

    if (!$exists) {
        $stmt = $this->pdo->prepare("INSERT INTO accounts (user_id, balance) VALUES (:id, 0)");
        $stmt->execute(['id' => $userId]);
    }
}

    public function deposit(int $amount): void
    {
        if ($amount > 0) {
            $currentBalance = $this->getBalance();
            $newBalance = $currentBalance + $amount;
            $stmt = $this->pdo->prepare("UPDATE accounts SET balance = :balance WHERE user_id = :id");
            $stmt->execute(['id' => $this->userId, 'balance' => $newBalance]);
        }
    }

    public function withdrawAll(): void
    {
        $stmt = $this->pdo->prepare("UPDATE accounts SET balance = 0 WHERE user_id = :id");
        $stmt->execute(['id' => $this->userId]);
        $_SESSION[$this->sessionKey] = 0;
    }

    public function getBalance(): int
    {
        $stmt = $this->pdo->prepare("SELECT balance FROM accounts WHERE user_id = :id");
        $stmt->execute(['id' => $this->userId]);
        $result = $stmt->fetch();
        if ($result && isset($result['balance'])) {
            return (int)$result['balance'];
        }
        return 0;
    }

    public function getData() {
        $stmt = $this->pdo->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

$account = new Account($pdo, 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["button1"])) {
        $account->deposit(100);
    } elseif (isset($_POST["button2"])) {
        $account->deposit(200);
    } elseif (isset($_POST["button3"])) {
        $account->deposit(300);
    } elseif (isset($_POST["custom_deposit"])) {
        $amount = intval($_POST["custom_amount"]);
        $account->deposit($amount);
    } elseif (isset($_POST["delete"])) {
        $account->withdrawAll();
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$balance = $account->getBalance();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balance Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="index-D1Y7Om-l.css">
</head>

<body class="bg-light">
    <div class="container py-5">
        <h1 class="mb-4 text-center">ATM</h1>

        <form method="post" class="mb-4">
            <div class="input-group atm-custom-deposit">
                <input type="number" name="custom_amount" class="form-control" placeholder="Enter custom amount" min="1"
                    required>
                <button type="submit" name="custom_deposit" class="btn btn-outline-primary depos">Deposit</button>
            </div>
        </form>


        <form class="forma" method="post">
            <div class="card p-4 shadow-sm forms">
                <div class="mb-3">
                    <input type="text" id="balance" class="form-control text-center fw-bold"
                        value="<?php echo $balance; ?>$" disabled>
                </div>

                <div class="d-flex justify-content-between mb-3 buttons">
                    <input type="submit" value="Add 100$" name="button1" class="btn btn-primary add">
                    <input type="submit" value="Add 200$" name="button2" class="btn btn-success add">
                    <input type="submit" value="Add 300$" name="button3" class="btn btn-warning add">
                </div>

                <div class="text-center">
                    <input type="submit" value="Withdraw all" name="delete" class="btn btn-danger"
                        onclick="customFunction()">
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        $("form").on("submit", function() {
            const $balance = $("#balance");

            $balance
                .css({
                    backgroundColor: "#d1e7dd"
                })
                .animate({
                    fontSize: "2.5rem"
                }, 200)
                .animate({
                    fontSize: "2rem"
                }, 200, function() {
                    $balance.css({
                        backgroundColor: ""
                    });
                });
        });
    });
    </script>
    <script>
    $(document).ready(function() {
        $('input[name="delete"]').hover(function() {
            $(this).css("background-color", "#dc3545");
        });

        $('input[name="custom_amount"]').on("input", function() {
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