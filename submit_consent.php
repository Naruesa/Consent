<?php
// ตรวจสอบว่าเป็นคำขอแบบ POST หรือไม่
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("วิธีการส่งข้อมูลไม่ถูกต้อง");
}

// รับข้อมูลจากฟอร์มและตรวจสอบความปลอดภัย
$name = htmlspecialchars($_POST['name']);
$tel = htmlspecialchars($_POST['tel']);
$consent = htmlspecialchars($_POST['consent']);
$signature = $_POST['signature'];

// ตรวจสอบว่าไม่มีช่องว่างในข้อมูลสำคัญ
if (empty($name) || empty($tel) || empty($consent) || empty($signature)) {
    die("ข้อมูลไม่ครบถ้วน");
}

// ตรวจสอบรูปแบบเบอร์โทร (ตัวเลข 10 หลัก)
if (!preg_match('/^[0-9]{10}$/', $tel)) {
    die("เบอร์โทรไม่ถูกต้อง");
}

// ตรวจสอบลายเซ็น (เป็น Base64 หรือไม่)
if (!preg_match('/^data:image\/(png|jpeg);base64,/', $signature)) {
    die("ลายเซ็นไม่ถูกต้อง");
}

// ข้อมูลสำหรับการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "consent";

// สร้างการเชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// SQL สำหรับบันทึกข้อมูลลงฐานข้อมูล (ใช้ Prepared Statement เพื่อป้องกัน SQL Injection)
$stmt = $conn->prepare("INSERT INTO consent_forms (name, tel, consent, signature) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $tel, $consent, $signature);

if ($stmt->execute()) {
    $success = true;
} else {
    $success = false;
    $error_message = "เกิดข้อผิดพลาด: " . $stmt->error;
}

// ปิดการเชื่อมต่อ
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บันทึกข้อมูลสำเร็จ!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f9f9f9;
        }
        .popup {
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .popup-content {
            background-color: #d4edda;
            color: #155724;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            font-size: 1.2rem;
            width: 80%; /* ลดความกว้างเพื่อให้หน้าต่างไม่เกินขอบ */
            max-width: 500px;
        }
        .popup-error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .details-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            text-align: left;
        }
        .details-container h3 {
            font-size: 1.5rem;
            color: #003333;
        }
        .details-container p {
            font-size: 1rem;
            color: #333333;
        }
        .signature-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin-top: 20px;
        }
        .signature-row {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .signature-container img {
            width: 200px;
            margin-left: 10px;
        }
        .print-btn {
            background-color: #006600;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            float: right; /* ทำให้ปุ่มอยู่ฝั่งขวา */
        }
        .print-btn:hover {
            background-color: #004d00;
        }

        /* สไตล์สำหรับโลโก้ */
        .logo {
            width: 80px; /* ขนาดโลโก้ */
            height: 80px;
            border-radius: 50%; /* ให้เป็นวงกลม */
            object-fit: cover; /* ให้ภาพไม่ยืดหรือหดเกินไป */
            margin-bottom: 20px; /* ระยะห่างจากโลโก้ */
        }
    </style>
</head>
<body>
    <?php if (isset($success) && $success): ?>
        <div class="popup">
            <div class="popup-content">
                <!-- เพิ่มโลโก้ใน popup -->
                <img src="https://i.imgur.com/BBQ2xts.jpg" class="logo" alt="Logo">
                <p>บันทึกข้อมูลยินยอมรับการรักษาสำเร็จ</p>
                <div class="details-container">
                    <h3>ข้อมูลที่บันทึก:</h3>
                    <p><strong>ชื่อผู้ป่วย:</strong> <?php echo htmlspecialchars($name); ?></p>
                    <p><strong>เบอร์ติดต่อ (ไม่ต้องใส่ - ):</strong> <?php echo htmlspecialchars($tel); ?></p>
                    <p><strong>ข้อความยินยอม:</strong> <?php echo htmlspecialchars($consent); ?></p>
                    
                    <!-- จัดตำแหน่งลายเซ็นต์และชื่อในวงเล็บ -->
                    <div class="signature-container">
                        <div class="signature-row">
                            <p><strong>ลงชื่อ:</strong></p>
                            <img src="<?php echo htmlspecialchars($signature); ?>" alt="Signature Image">
                        </div>
                        <p>(<?php echo htmlspecialchars($name); ?>)</p> <!-- ชื่อในวงเล็บ -->
                    </div>
                    
                    <button class="print-btn" onclick="window.print()">พิมพ์ข้อมูล</button>
                </div>
            </div>
        </div>
    <?php elseif (isset($success) && !$success): ?>
        <div class="popup">
            <div class="popup-content popup-error">
                <?php echo $error_message; ?>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>
