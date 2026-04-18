<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>2ndIncome | Sign In</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
      overflow: hidden;
    }

    body::before, body::after {
      content: '';
      position: fixed;
      border-radius: 50%;
      filter: blur(80px);
      opacity: 0.35;
      animation: floatOrb 8s ease-in-out infinite alternate;
      pointer-events: none;
    }
    body::before {
      width: 500px; height: 500px;
      background: radial-gradient(circle, #6c63ff, #3b3b98);
      top: -100px; left: -100px;
    }
    body::after {
      width: 400px; height: 400px;
      background: radial-gradient(circle, #f093fb, #f5576c);
      bottom: -80px; right: -80px;
      animation-delay: -4s;
    }
    @keyframes floatOrb {
      from { transform: translateY(0) scale(1); }
      to   { transform: translateY(40px) scale(1.05); }
    }

    .login-wrapper {
      position: relative;
      z-index: 10;
      width: 100%;
      max-width: 420px;
      padding: 16px;
    }

    .login-card {
      background: rgba(255,255,255,0.07);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(255,255,255,0.15);
      border-radius: 20px;
      padding: 44px 40px 36px;
      box-shadow: 0 25px 60px rgba(0,0,0,0.5);
    }

    .login-brand {
      text-align: center;
      margin-bottom: 32px;
    }
    .login-brand .brand-icon {
      width: 58px; height: 58px;
      background: linear-gradient(135deg, #6c63ff, #f5576c);
      border-radius: 16px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 26px;
      color: #fff;
      margin-bottom: 14px;
      box-shadow: 0 8px 24px rgba(108,99,255,0.4);
    }
    .login-brand h1 {
      font-size: 22px;
      font-weight: 700;
      color: #fff;
      letter-spacing: -0.3px;
    }
    .login-brand h1 sup {
      font-size: 13px;
      color: #a89ef5;
    }
    .login-brand p {
      font-size: 13px;
      color: rgba(255,255,255,0.5);
      margin-top: 4px;
    }

    .alert-error {
      background: rgba(245,87,108,0.15);
      border: 1px solid rgba(245,87,108,0.4);
      color: #f9aab5;
      border-radius: 10px;
      padding: 10px 14px;
      font-size: 13px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .form-label {
      display: block;
      font-size: 12px;
      font-weight: 600;
      color: rgba(255,255,255,0.6);
      letter-spacing: 0.6px;
      text-transform: uppercase;
      margin-bottom: 7px;
    }

    .input-wrap {
      margin-bottom: 20px;
    }
    .input-field {
      position: relative;
    }
    .input-wrap .field-icon {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: rgba(255,255,255,0.35);
      font-size: 14px;
      pointer-events: none;
    }
    .input-wrap input {
      width: 100%;
      background: rgba(255,255,255,0.08);
      border: 1px solid rgba(255,255,255,0.15);
      border-radius: 10px;
      padding: 12px 14px 12px 40px;
      color: #fff;
      font-size: 14px;
      font-family: 'Inter', sans-serif;
      outline: none;
      transition: border-color 0.25s, background 0.25s, box-shadow 0.25s;
    }
    .input-wrap input::placeholder { color: rgba(255,255,255,0.3); }
    .input-wrap input:focus {
      background: rgba(255,255,255,0.12);
      border-color: #6c63ff;
      box-shadow: 0 0 0 3px rgba(108,99,255,0.25);
    }

    .form-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
    }
    .remember-label {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 13px;
      color: rgba(255,255,255,0.55);
      cursor: pointer;
      user-select: none;
    }
    .remember-label input[type="checkbox"] {
      accent-color: #6c63ff;
      width: 15px; height: 15px;
      cursor: pointer;
    }

    .btn-signin {
      width: 100%;
      padding: 13px;
      background: linear-gradient(135deg, #6c63ff, #a855f7);
      border: none;
      border-radius: 10px;
      color: #fff;
      font-size: 15px;
      font-weight: 600;
      font-family: 'Inter', sans-serif;
      letter-spacing: 0.3px;
      cursor: pointer;
      transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
      box-shadow: 0 8px 24px rgba(108,99,255,0.4);
    }
    .btn-signin:hover {
      opacity: 0.92;
      transform: translateY(-1px);
      box-shadow: 0 12px 30px rgba(108,99,255,0.5);
    }
    .btn-signin:active { transform: translateY(0); }

    .login-footer {
      text-align: center;
      margin-top: 24px;
      font-size: 12px;
      color: rgba(255,255,255,0.25);
    }
  </style>
</head>
<body>

<div class="login-wrapper">
  <div class="login-card">

    <div class="login-brand">
      <div class="brand-icon"><i class="fas fa-coins"></i></div>
      <h1><b>2<sup>nd</sup></b>Income</h1>
      <p>Finance Management System</p>
    </div>

    <?php if(isset($_REQUEST['error']) && $_REQUEST['error'] == 1): ?>
    <div class="alert-error">
      <i class="fas fa-exclamation-circle"></i>
      Invalid username or password. Please try again.
    </div>
    <?php endif; ?>

    <form action="login.php" method="post">
      <div class="input-wrap">
        <label class="form-label" for="uname">Username</label>
        <div class="input-field">
          <i class="fas fa-user field-icon"></i>
          <input type="text" name="uname" id="uname" placeholder="Enter your username" required autofocus>
        </div>
      </div>
      <div class="input-wrap">
        <label class="form-label" for="upass">Password</label>
        <div class="input-field">
          <i class="fas fa-lock field-icon"></i>
          <input type="password" name="upass" id="upass" placeholder="Enter your password" required>
        </div>
      </div>
      <div class="form-footer">
        <label class="remember-label">
          <input type="checkbox" id="remember" name="remember"> Remember me
        </label>
      </div>
      <button type="submit" class="btn-signin">Sign In &nbsp;<i class="fas fa-arrow-right"></i></button>
    </form>

  </div>
  <div class="login-footer">&copy; <?php echo date('Y'); ?> 2ndIncome &mdash; All rights reserved</div>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>