<!DOCTYPE html>  
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงชื่อยินยอมเข้าร่วมการแพทย์ทางไกล<br>ศูนย์โทรเวชฯ งานผู้ป่วยนอก</title>
    <style>
        /* สไตล์ตามเดิม */
        body {
            font-family: Arial, sans-serif;
            background-color: #FFFFFF;
            color: #333333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            position: relative;
        }

        .form-container {
            background-color: #FFFFFF;
            padding: 20px;
            width: 100%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .form-container h2 {
            font-size: 24px;
            color: #003333;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            color: #003300;
        }

        input[type="text"], input[type="tel"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        canvas {
            border: 1px solid #ddd;
            width: 100%;
            height: 150px;
            margin-top: 10px;
        }

        input[type="submit"] {
            background-color: #006600;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
        }

        input[type="submit"]:hover {
            background-color: #004d00;
        }

        .clear-btn {
            background-color: #FF0033;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .form-group.signature {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .owner-name {
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 14px;
            color: #003333;
            background-color: rgba(255, 255, 255, 0.7);
            padding: 5px 10px;
            border-radius: 5px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* CSS สำหรับให้โลโก้เป็นวงกลม */
        .logo {
            width: 60px;
            height: 60px;
            border-radius: 50%; /* ทำให้เป็นวงกลม */
            object-fit: cover; /* ทำให้ภาพไม่ยืดหรือหดเกินไป */
            margin-bottom: 15px; /* เพิ่มช่องว่างระหว่างโลโก้กับข้อความ */
        }
    </style>
</head>
<body>

<div class="form-container">
    <!-- แก้ไข src เป็น URL ของภาพที่ต้องการแสดง -->
    <img src="https://i.imgur.com/BBQ2xts.jpg" class="logo">
    <h2>ลงชื่อยินยอมเข้าร่วมการแพทย์ทางไกล<br>ศูนย์โทรเวชฯ งานผู้ป่วยนอก</h2>

    <form action="submit_consent.php" method="post">
        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="form-group">
            <label for="name">ชื่อผู้ป่วย:</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="tel">เบอร์ติดต่อ:</label>
            <input type="tel" id="tel" name="tel" required>
        </div>

        <div class="form-group">
            <label for="consent">ข้อความยินยอม:</label>
            <textarea id="consent" name="consent" rows="4" required>ข้าพเจ้า(ผู้รับบริการ) ยินยอมรับการรักษาการแพทย์ทางไกล</textarea>
        </div>

        <div class="form-group signature">
            <label for="signature">ลายเซ็นต์:</label>
            <canvas id="signature-pad"></canvas>
            <div id="signature-name" style="margin-top: 10px; font-style: italic;">(ชื่อผู้ป่วย)</div>
            <button type="button" class="clear-btn" onclick="clearSignature()">ลบลายเซ็นต์</button>
            <input type="hidden" id="signature" name="signature">
        </div>

        <input type="submit" value="ยืนยันการยินยอม" onclick="saveSignature()">
    </form>
</div>

<script>
    const canvas = document.getElementById("signature-pad");
    const ctx = canvas.getContext("2d");
    let isDrawing = false;

    canvas.addEventListener("mousedown", () => { isDrawing = true; });
    canvas.addEventListener("mouseup", () => { isDrawing = false; ctx.beginPath(); });
    canvas.addEventListener("mousemove", draw);

    function draw(event) {
        if (!isDrawing) return;
        ctx.lineWidth = 2;
        ctx.lineCap = "round";
        ctx.strokeStyle = "#000000";
        ctx.lineTo(event.clientX - canvas.offsetLeft, event.clientY - canvas.offsetTop);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(event.clientX - canvas.offsetLeft, event.clientY - canvas.offsetTop);
    }

    function clearSignature() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        document.getElementById("signature").value = "";
    }

    function saveSignature() {
        const dataURL = canvas.toDataURL();
        if (dataURL) {
            document.getElementById("signature").value = dataURL;
        }
    }

    // อัปเดตชื่อผู้ป่วยในวงเล็บแบบเรียลไทม์
    const nameInput = document.getElementById("name");
    const signatureName = document.getElementById("signature-name");

    nameInput.addEventListener("input", () => {
        const name = nameInput.value.trim();
        if (name) {
            signatureName.textContent = `(${name})`;
        } else {
            signatureName.textContent = "(ชื่อผู้ป่วย)";
        }
    });
</script>

<!-- by -->
<div class="owner-name">
    &copy; Naruesa Chumnirat_TT
</div>

</body>
</html>
