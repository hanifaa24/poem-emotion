<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🎭 Emotion Detection in Poetry</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #ffb347, #ffcc70, #ffd1dc, #e0c3fc);
            background-size: 400% 400%;
            animation: gradientMove 15s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            max-width: 700px;
            width: 90%;
            position: relative;
            margin-bottom: 30px;
        }

        .pen-icon {
            position: absolute;
            top: -40px;
            left: -40px;
            width: 70px;
            opacity: 0.4;
        }

        h1 {
            font-family: 'Great Vibes', cursive;
            text-align: center;
            font-size: 36px;
            color: #4e342e;
            margin-bottom: 30px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        textarea {
            width: 100%;
            padding: 20px;
            border: 2px dashed #ce93d8;
            border-radius: 15px;
            font-size: 16px;
            resize: vertical;
            min-height: 180px;
            background: linear-gradient(to bottom, #fff 28px, #f3e5f5 29px);
            background-size: 100% 30px;
            line-height: 30px;
            color: #4e342e;
        }

        textarea:focus {
            outline: none;
            border-color: #8e24aa;
        }

        button {
            margin-top: 25px;
            width: 100%;
            padding: 15px;
            background: #8e24aa;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s;
            box-shadow: 0 8px 20px rgba(142, 36, 170, 0.3);
        }

        button:hover {
            background: #6a1b9a;
            transform: scale(1.03);
        }

        .footer-note {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }

        .section {
            margin-bottom: 20px;
        }

        .label {
            font-weight: 600;
            color: #5d4037;
            margin-bottom: 8px;
            font-size: 16px;
        }

        .content-box {
            background: #fdfaf7;
            padding: 20px;
            border-radius: 12px;
            font-size: 16px;
            color: #4e342e;
            white-space: pre-line;
            border-left: 4px solid #ba68c8;
            box-shadow: inset 0 1px 5px rgba(0,0,0,0.05);
        }

        .emotion-result {
            font-size: 24px;
            color: #6a1b9a;
            font-weight: bold;
            text-align: center;
            margin: 25px 0;
        }

        .reset-btn {
            background: #6a1b9a;
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #fff;
            padding: 10px;
            border-radius: 10px;
            font-weight: bold;
            text-decoration: none;
        }

        .reset-btn:hover {
            background: #4a148c;
        }
    </style>
</head>
<body>
    
<?php
$hasil = '';
$confidence = '';
$puisi = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["puisi"])) {
    $puisi = $_POST["puisi"];
    $data = json_encode(["text" => $puisi]);

    $ch = curl_init("http://localhost:5050/predict");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $result = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($result === false) {
        $hasil = "❌ Gagal menghubungi Flask API: " . curl_error($ch);
    } else {
        $response = json_decode($result, true);
        if ($http_status !== 200 || !isset($response["predicted_emotion"])) {
            $hasil = $response["error"] ?? "❌ Terjadi kesalahan saat memproses.";
        } else {
            $hasil = $response["predicted_emotion"];
            $confidence = $response["confidence"] ?? "";
        }
    }

    curl_close($ch);
}
?>

<div class="container">
    <h1>🎭 Emotion Detection in Poetry</h1>

    <form method="post" action="">
        <textarea name="puisi" placeholder="🌟 Write your English poem here..."><?= htmlspecialchars($puisi) ?></textarea>
        <button type="submit">🔍 Detect Emotion</button>
    </form>

    <?php if (!empty($hasil)): ?>
        <div class="section">
            <div class="label">💡 Detected Emotion:</div>
            <div class="emotion-result"><?= htmlspecialchars($hasil) ?></div>
        </div>

        <?php if (!empty($confidence)): ?>
            <div class="section">
                <div class="label">📊 Confidence:</div>
                <div class="content-box"><?= htmlspecialchars($confidence) ?></div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="footer-note">Express your soul through words – detect emotions in your poem ✍️</div>
</div>
</body>
</html>
