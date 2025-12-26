<?php
// ------------------------------------------------------------
// DATABASE CONNECTION
// ------------------------------------------------------------
$host = "elvisdb";
$user = "xx";
$pass = " ******** ";   
$db   = "xx";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Smart Parking – Final Project (Sheena Patel)</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
        }

        .page-wrapper {
            max-width: 1100px;
            margin: 0 auto;
            padding: 25px 15px 40px;
        }

        header {
            background: #3949ab;
            color: white;
            padding: 20px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            margin-bottom: 20px;
        }

        header h1 {
            margin: 0 0 5px 0;
            font-size: 26px;
        }

        header p {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }

        .nav-bar {
            margin: 15px 0 25px;
            padding: 10px 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            font-size: 14px;
        }

        .nav-bar span {
            font-weight: bold;
            margin-right: 10px;
        }

        .nav-bar a {
            margin-right: 10px;
            color: #3949ab;
            text-decoration: none;
        }

        .nav-bar a:hover {
            text-decoration: underline;
        }

        h2 {
            background:#263238;
            color:white;
            padding:10px 15px;
            margin:-15px -15px 15px -15px;
            border-radius:8px 8px 0 0;
            font-size:18px;
        }

        .box {
            background:white;
            padding:15px;
            margin-bottom:20px;
            border-radius:8px;
            box-shadow:0 3px 8px rgba(0,0,0,0.08);
        }

        button {
            padding:8px 14px;
            margin:5px 5px 0 0;
            cursor:pointer;
            border-radius:6px;
            border:1px solid #3949ab;
            background:#3949ab;
            color:white;
            font-size: 13px;
        }

        button:hover {
            background:#5c6bc0;
        }

        input, select {
            padding:7px;
            width:260px;
            max-width:100%;
            border-radius:5px;
            border:1px solid #ccc;
            font-size: 13px;
        }

        table {
            border-collapse: collapse;
            width:100%;
            margin-top:10px;
            background:white;
            font-size: 13px;
        }

        td, th {
            border:1px solid #ddd;
            padding:8px;
            text-align:left;
        }

        th {
            background:#f5f5f5;
        }

        p {
            margin-top:8px;
        }

        small {
            color:#e0e0e0;
        }
    </style>
</head>

<body>
<div class="page-wrapper">

<header>
    <h1>Smart Parking Management </h1>
    <p>By: <b>Sheena Patel</b></p>
</header>

<div class="nav-bar">
    <span>Quick Navigation:</span>
    <a href="#view-slots">View Slots</a>
    <a href="#add-slot">Add Slot</a>
    <a href="#update-slot">Update Slot</a>
    <a href="#delete-slot">Delete Slot</a>
    <a href="#views">Views</a>
    <a href="#functions">Functions</a>
    <a href="#procedures">Stored Procedures</a>
    <a href="#trigger-rating">Rating Trigger</a>
</div>

<!-- ----------------------------------------------------------
     SELECT PARKING SLOTS
----------------------------------------------------------- -->
<div class="box" id="view-slots">
    <h2>View All Parking Slots</h2>
    <form method="post">
        <button name="showSlots">Show Parking Slots</button>
    </form>

    <?php
    if (isset($_POST['showSlots'])) {
        $result = mysqli_query($conn, "SELECT * FROM PARKINGSLOT");
        if ($result) {
            echo "<table><tr>
                    <th>SlotID</th>
                    <th>LotID</th>
                    <th>SlotNumber</th>
                    <th>Status</th>
                    <th>SlotType</th>
                  </tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['SlotID']}</td>
                        <td>{$row['LotID']}</td>
                        <td>{$row['SlotNumber']}</td>
                        <td>{$row['Status']}</td>
                        <td>{$row['SlotType']}</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Error: " . mysqli_error($conn) . "</p>";
        }
    }
    ?>
</div>

<!-- ----------------------------------------------------------
     INSERT NEW SLOT
----------------------------------------------------------- -->
<div class="box" id="add-slot">
    <h2>Add Parking Slot</h2>

    <form method="post">
        LotID: <br>
        <input type="number" name="lotid" required><br><br>

        Slot Number: <br>
        <input type="text" name="slotnum" required><br><br>

        Slot Type: <br>
        <select name="slottype">
            <option value="Compact">Compact</option>
            <option value="Large">Large</option>
            <option value="EV">EV</option>
            <option value="Handicapped">Handicapped</option>
        </select><br><br>

        <button name="insertSlot">Insert Slot</button>
    </form>

    <?php
    if (isset($_POST['insertSlot'])) {
        $lot  = (int)$_POST['lotid'];
        $num  = mysqli_real_escape_string($conn, $_POST['slotnum']);
        $type = mysqli_real_escape_string($conn, $_POST['slottype']);

        $query = "INSERT INTO PARKINGSLOT (LotID, SlotNumber, Status, SlotType)
                  VALUES ($lot, '$num', 'Available', '$type')";
        
        if (mysqli_query($conn, $query)) {
            echo "<p><b>Slot inserted successfully!</b></p>";
        } else {
            echo "<p>Error: " . mysqli_error($conn) . "</p>";
        }
    }
    ?>
</div>

<!-- ----------------------------------------------------------
     UPDATE SLOT STATUS  +  SHOW SENSOR (TRIGGER OUTPUT)
----------------------------------------------------------- -->
<div class="box" id="update-slot">
    <h2>Update Slot Status (Trigger 1: trg_update_sensor)</h2>

    <form method="post">
        SlotID: <br>
        <input type="number" name="usid" required><br><br>

        New Status: <br>
        <select name="newstatus">
            <option value="Available">Available</option>
            <option value="Reserved">Reserved</option>
            <option value="Occupied">Occupied</option>
            <option value="OutOfService">OutOfService</option>
        </select><br><br>

        <button name="updateSlot">Update Status</button>
    </form>

    <?php
    if (isset($_POST['updateSlot'])) {
        $slot   = (int)$_POST['usid'];
        $status = mysqli_real_escape_string($conn, $_POST['newstatus']);

        $query = "UPDATE PARKINGSLOT SET Status='$status' WHERE SlotID=$slot";
        
        if (mysqli_query($conn, $query)) {
            echo "<p><b>Status updated successfully! Trigger <code>trg_update_sensor</code> auto-updated SENSOR.</b></p>";

            // Show the SENSOR row for this slot to prove trigger fired
            $qSensor = mysqli_query($conn, "SELECT * FROM SENSOR WHERE SlotID = $slot");
            if ($qSensor && mysqli_num_rows($qSensor) > 0) {
                echo "<h4>Sensor Row After Update (Trigger Output)</h4>";
                echo "<table><tr>
                        <th>SensorID</th>
                        <th>SlotID</th>
                        <th>Status</th>
                        <th>LastUpdate</th>
                      </tr>";
                while ($s = mysqli_fetch_assoc($qSensor)) {
                    echo "<tr>
                            <td>{$s['SensorID']}</td>
                            <td>{$s['SlotID']}</td>
                            <td>{$s['Status']}</td>
                            <td>{$s['LastUpdate']}</td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No SENSOR row found for this SlotID (check sample data).</p>";
            }

        } else {
            echo "<p>Error: " . mysqli_error($conn) . "</p>";
        }
    }
    ?>
</div>

<!-- ----------------------------------------------------------
     DELETE SLOT
----------------------------------------------------------- -->
<div class="box" id="delete-slot">
    <h2>Delete Parking Slot</h2>

    <form method="post">
        SlotID: <br>
        <input type="number" name="delid" required><br><br>
        <button name="deleteSlot">Delete Slot</button>
    </form>

    <?php
    if (isset($_POST['deleteSlot'])) {
    $del = (int)$_POST['delid'];

    // 1) Delete from SENSOR first (because of FK constraint)
    $qSensor = "DELETE FROM SENSOR WHERE SlotID = $del";
    if (!mysqli_query($conn, $qSensor)) {
        echo "<p>Error deleting SENSOR row: " . mysqli_error($conn) . "</p>";
    }

    // 2) Now delete from PARKINGSLOT
    $qSlot = "DELETE FROM PARKINGSLOT WHERE SlotID = $del";
    if (mysqli_query($conn, $qSlot)) {
        if (mysqli_affected_rows($conn) > 0) {
            echo "<p><b>Slot deleted successfully!</b></p>";
        } else {
            echo "<p>No slot found with that SlotID.</p>";
        }
    } else {
        echo "<p>Error deleting slot: " . mysqli_error($conn) . "</p>";
    }
}

    ?>
</div>

<!-- ----------------------------------------------------------
     SHOW VIEWS
----------------------------------------------------------- -->
<div class="box" id="views">
    <h2>Views Output</h2>
    <form method="post">
        <button name="view1">Reservation Summary (vw_reservation_summary)</button>
        <button name="view2">Lot Usage (vw_lot_usage)</button>
    </form>

    <?php
    if (isset($_POST['view1'])) {
        $result = mysqli_query($conn, "SELECT * FROM vw_reservation_summary");
        if ($result) {
            echo "<table><tr>
                    <th>ReservationID</th>
                    <th>UserName</th>
                    <th>LicensePlate</th>
                    <th>StartTime</th>
                    <th>EndTime</th>
                    <th>Status</th>
                  </tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['ReservationID']}</td>
                        <td>{$row['UserName']}</td>
                        <td>{$row['LicensePlate']}</td>
                        <td>{$row['StartTime']}</td>
                        <td>{$row['EndTime']}</td>
                        <td>{$row['Status']}</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Error: " . mysqli_error($conn) . "</p>";
        }
    }

    if (isset($_POST['view2'])) {
        $result = mysqli_query($conn, "SELECT * FROM vw_lot_usage");
        if ($result) {
            echo "<table><tr>
                    <th>ParkingLot</th>
                    <th>TotalSlots</th>
                    <th>SlotsConfigured</th>
                    <th>OccupiedSlots</th>
                    <th>AvailableSlots</th>
                  </tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['ParkingLot']}</td>
                        <td>{$row['TotalSlots']}</td>
                        <td>{$row['SlotsConfigured']}</td>
                        <td>{$row['OccupiedSlots']}</td>
                        <td>{$row['AvailableSlots']}</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Error: " . mysqli_error($conn) . "</p>";
        }
    }
    ?>
</div>

<!-- ----------------------------------------------------------
     FUNCTIONS
----------------------------------------------------------- -->
<div class="box" id="functions">
    <h2>Functions Output</h2>
    <form method="post">
        ReservationID: <br>
        <input type="number" name="resIDf" required><br><br>
        <button name="func1">Get Payment Amount (fn_reservation_payment)</button>
        <button name="func2">Get Reservation Hours (fn_reservation_hours)</button>
    </form>

    <?php
    if (isset($_POST['func1'])) {
        $id = (int)$_POST['resIDf'];
        $q  = mysqli_query($conn, "SELECT fn_reservation_payment($id) AS result");
        if ($q) {
            $r = mysqli_fetch_assoc($q);
            echo "<p><b>Payment Amount:</b> {$r['result']}</p>";
        } else {
            echo "<p>Error: " . mysqli_error($conn) . "</p>";
        }
    }

    if (isset($_POST['func2'])) {
        $id = (int)$_POST['resIDf'];
        $q  = mysqli_query($conn, "SELECT fn_reservation_hours($id) AS result");
        if ($q) {
            $r = mysqli_fetch_assoc($q);
            echo "<p><b>Total Hours:</b> {$r['result']}</p>";
        } else {
            echo "<p>Error: " . mysqli_error($conn) . "</p>";
        }
    }
    ?>
</div>

<!-- ----------------------------------------------------------
     STORED PROCEDURES
----------------------------------------------------------- -->
<div class="box" id="procedures">
    <h2>Stored Procedures</h2>

    <form method="post">
        <h3>Create Reservation + Payment (sp_create_reservation_with_payment)</h3>
        SlotID: <br>
        <input type="number" name="sp_slot" required><br><br>

        UserID: <br>
        <input type="number" name="sp_user" required><br><br>

        VehicleID: <br>
        <input type="number" name="sp_vehicle" required><br><br>

        Start (YYYY-MM-DD HH:MM): <br>
        <input type="datetime-local" name="sp_start" required><br><br>

        End (YYYY-MM-DD HH:MM): <br>
        <input type="datetime-local" name="sp_end" required><br><br>

        Amount: <br>
        <input type="number" step="0.01" name="sp_amt" required><br><br>

        <button name="sp1">Run SP – Create Reservation</button>
    </form>

  <?php
if (isset($_POST['sp1'])) {
    $slot  = (int)$_POST['sp_slot'];
    $user  = (int)$_POST['sp_user'];
    $veh   = (int)$_POST['sp_vehicle'];

    // Convert datetime-local (2025-12-07T19:30) to MySQL DATETIME (2025-12-07 19:30:00)
    $startRaw = $_POST['sp_start'];
    $endRaw   = $_POST['sp_end'];

    $start = str_replace('T', ' ', $startRaw) . ':00';
    $end   = str_replace('T', ' ', $endRaw) . ':00';

    $amt   = (float)$_POST['sp_amt'];

    // 1) Run the stored procedure
    $call = "CALL sp_create_reservation_with_payment($slot,$user,$veh,'$start','$end',$amt)";

    if (mysqli_query($conn, $call)) {
        echo "<p style='color:green;'><b>Reservation & Payment created successfully by stored procedure!</b></p>";

      
        while (mysqli_more_results($conn) && mysqli_next_result($conn)) {;}

        // 2) Get the latest reservation for this user
        $q1 = mysqli_query($conn,
            "SELECT * FROM RESERVATION
             WHERE UserID = $user
             ORDER BY ReservationID DESC
             LIMIT 1");

        if ($q1 && $res = mysqli_fetch_assoc($q1)) {
            echo "<h4>New Reservation Created:</h4>";
            echo "<table>
                    <tr>
                        <th>ReservationID</th>
                        <th>SlotID</th>
                        <th>UserID</th>
                        <th>VehicleID</th>
                        <th>StartTime</th>
                        <th>EndTime</th>
                        <th>Status</th>
                    </tr>
                    <tr>
                        <td>{$res['ReservationID']}</td>
                        <td>{$res['SlotID']}</td>
                        <td>{$res['UserID']}</td>
                        <td>{$res['VehicleID']}</td>
                        <td>{$res['StartTime']}</td>
                        <td>{$res['EndTime']}</td>
                        <td>{$res['Status']}</td>
                    </tr>
                  </table>";

            $newResID = (int)$res['ReservationID'];

            // 3) Get the payment row for that reservation
            $q2 = mysqli_query($conn,
                "SELECT * FROM PAYMENT
                 WHERE ReservationID = $newResID");

            if ($q2 && $pay = mysqli_fetch_assoc($q2)) {
                echo "<h4>Payment Created:</h4>";
                echo "<table>
                        <tr>
                            <th>PaymentID</th>
                            <th>ReservationID</th>
                            <th>Amount</th>
                            <th>PaymentMethod</th>
                            <th>PaymentStatus</th>
                            <th>TransactionDate</th>
                        </tr>
                        <tr>
                            <td>{$pay['PaymentID']}</td>
                            <td>{$pay['ReservationID']}</td>
                            <td>{$pay['Amount']}</td>
                            <td>{$pay['PaymentMethod']}</td>
                            <td>{$pay['PaymentStatus']}</td>
                            <td>{$pay['TransactionDate']}</td>
                        </tr>
                      </table>";
            } else {
                echo "<p>No PAYMENT row found for the new reservation (check data).</p>";
            }

        } else {
            echo "<p>Could not find the new reservation (check UserID / data).</p>";
        }

    } else {
        echo "<p style='color:red;'><b>Error running stored procedure:</b> " . mysqli_error($conn) . "</p>";
    }
}
?>


    <form method="post">
        <h3>Show All Payments (sp_get_payments)</h3>
        <button name="sp2">Run SP – Get Payments</button>
    </form>

    <?php
    if (isset($_POST['sp2'])) {
        $result = mysqli_query($conn, "CALL sp_get_payments()");
        if ($result) {
            echo "<table><tr>
                    <th>PaymentID</th>
                    <th>ReservationID</th>
                    <th>Amount</th>
                    <th>PaymentStatus</th>
                  </tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['PaymentID']}</td>
                        <td>{$row['ReservationID']}</td>
                        <td>{$row['Amount']}</td>
                        <td>{$row['PaymentStatus']}</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Error: " . mysqli_error($conn) . "</p>";
        }
    }
    ?>
</div>

<!-- ----------------------------------------------------------
     TRIGGER DEMO – VALIDATE RATING
----------------------------------------------------------- -->
<div class="box" id="trigger-rating">
    <h2>Trigger 2: Validate Rating (trg_validate_rating)</h2>

    <p>
         This form inserts into <code>FEEDBACK</code>. Trigger <code>trg_validate_rating</code>
         ensures ratings are between 1 and 5.
    </p>

    <form method="post">
        UserID: <br>
        <input type="number" name="fb_user" required><br><br>

        LotID: <br>
        <input type="number" name="fb_lot" required><br><br>

        Rating (1–5): <br>
        <input type="number" name="fb_rating" required><br><br>

        Comment: <br>
        <input type="text" name="fb_comment"><br><br>

        <button name="insertFeedback">Insert Feedback</button>
    </form>

    <?php
    if (isset($_POST['insertFeedback'])) {
        $u = (int)$_POST['fb_user'];
        $l = (int)$_POST['fb_lot'];
        $r = (int)$_POST['fb_rating'];
        $c = mysqli_real_escape_string($conn, $_POST['fb_comment']);
        $today = date('Y-m-d');

        
        if ($r < 1 || $r > 5) {
            echo "<p style='color:red;'><b>Rating invalid.</b> Please rate from 1 to 5.</p>";
        } else {
            
            $sql = "INSERT INTO FEEDBACK (UserID, LotID, Rating, Comment, FeedbackDate)
                    VALUES ($u, $l, $r, '$c', '$today')";

            if (mysqli_query($conn, $sql)) {
                echo "<p style='color:green;'><b>Thank you for your feedback!</b></p>";
            } else {
                $err = mysqli_error($conn);

                // Trigger failure detection
                if (strpos($err, 'Invalid Rating') !== false) {
                    echo "<p style='color:red;'><b>Rating invalid.</b> Please rate from 1 to 5.</p>";
                } else {
                    echo "<p style='color:red;'>Database error: $err</p>";
                }
            }
        }
    }
    ?>
</div>

</div> <!-- page-wrapper -->
</body>
</html>

