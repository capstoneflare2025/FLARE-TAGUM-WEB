<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Login Page</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet" />
  <style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:Arial,sans-serif;}
    body{display:flex;justify-content:center;align-items:center;height:100vh;background:#f4f4f4;overflow:hidden;}
    .video-bg{position:fixed;top:0;left:0;width:100%;height:100%;object-fit:cover;z-index:-2;filter:blur(4px);transform:scale(1.1);}
    .overlay{position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:-1;}
    .container{position:relative;width:500px;height:550px;display:flex;margin-left:400px;align-items:center;}
    .welcome-box{width:100%;height:100%;background:#E00024;border-radius:20px;display:flex;flex-direction:column;justify-content:center;align-items:center;color:#fff;text-align:center;padding:20px;}
    .welcome-box .logo{width:600px;height:auto;margin-bottom:15px;}
    .login-box{position:absolute;background:#fff;width:400px;height:470px;padding:35px;margin-left:-380px;border-radius:10px;box-shadow:0 5px 15px rgba(0,0,0,0.2);left:30px;}
    .login-box h3{text-align:center;margin-bottom:40px;color:#E00024;}
    .input-group{display:flex;background:#F1F1F1;padding:10px;border-radius:5px;margin-top:20px;height:50px;margin-bottom:20px;align-items:center;}
    .input-group i{margin-right:10px;color:gray;}
    .input-group input{border:none;outline:none;background:transparent;flex:1;font-size:14px;}
    .login-btn{width:50%;background:#E00024;color:white;border:none;padding:10px;margin-left:80px;margin-top:20px;cursor:pointer;font-weight:bold;border-radius:10px;}
    .login-btn:hover{background:#E87F2E;}
    button.toggle-password{background:transparent;border:none;padding:0;cursor:pointer;}
    .forgot-wrap{text-align:center;margin-top:10px;}
    .forgot-link{color:#0A1F7C;text-decoration:underline;cursor:pointer;font-size:14px;}
    .forgot-link:hover{color:#00A9FF;}
    /* Modal */
    .modal{position:fixed;inset:0;background:rgba(0,0,0,0.6);display:none;align-items:center;justify-content:center;z-index:1000;}
    .modal-content{background:#fff;padding:25px;border-radius:10px;width:90%;max-width:400px;text-align:center;box-shadow:0 5px 20px rgba(0,0,0,0.3);}
    .modal h4{margin-bottom:15px;color:#E00024;}
    .modal input{width:100%;padding:10px;border:1px solid #ccc;border-radius:8px;margin-bottom:10px;}
    .modal button{padding:10px 20px;margin-top:5px;border:none;border-radius:8px;cursor:pointer;font-weight:bold;}
    .close-btn{background:#ccc;margin-right:10px;}
    .send-btn{background:#E00024;color:#fff;}
    .send-btn[disabled]{opacity:.6;cursor:not-allowed;}
    #resetMsg{margin-top:8px;font-size:14px;min-height:18px;}

    /* ---------------------- RESPONSIVE FIXES ---------------------- */
    @media (max-width: 992px) {
      body {
        flex-direction: column;
        overflow:auto;
        height:auto;
        padding:20px 0;
      }

      .container {
        flex-direction: column;
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
        height: auto;
        align-items: center;
      }

      .welcome-box {
        width: 100%;
        height: auto;
        margin-bottom: 20px;
        border-radius: 15px;
      }

      .welcome-box .logo {
        width: 80%;
        max-width: 280px;
      }

      .login-box {
        position: relative;
        width: 90%;
        max-width: 400px;
        height: auto;
        margin: 0 auto;
        left: 0;
        padding: 25px 20px;
      }

      .login-btn {
        width: 100%;
        margin: 20px 0 0 0;
      }
    }

    @media (max-width: 576px) {
      .welcome-box {
        padding: 15px;
      }

      .welcome-box h2 {
        font-size: 1.2rem;
      }

      .login-box {
        padding: 20px 15px;
      }

      .input-group {
        height: 45px;
      }

      .input-group input {
        font-size: 13px;
      }

      .login-btn {
        font-size: 14px;
        padding: 10px;
      }

      .forgot-link {
        font-size: 13px;
      }
    }
</style>

</head>
<body>
  <video autoplay muted loop playsinline class="video-bg">
    <source src="{{ asset('videos/background.mp4') }}" type="video/mp4" />
  </video>
  <div class="overlay"></div>

  <div class="container">
    <div class="welcome-box">
      <img src="{{ asset('images/flabfp.png') }}" alt="Logo" class="logo" />
      <h2>ADMIN DASHBOARD</h2>
    </div>

    <div class="login-box">
      <h3>Login Please</h3>
      @if($errors->any())
        <div style="color:red;text-align:center;">{{ $errors->first('login') }}</div>
      @endif

      <form method="POST" action="{{ route('login.submit') }}">
        @csrf
        <div class="input-group">
          <i class="fa fa-envelope"></i>
          <input type="email" id="email" name="email" placeholder="Input your Email" required value="{{ old('email') }}" />
        </div>
        <div class="input-group">
          <i class="fa fa-lock"></i>
          <input type="password" id="password" name="password" placeholder="Input your password" required />
          <button type="button" class="toggle-password"><i class="bi bi-eye-slash"></i></button>
        </div>
        <button type="submit" class="login-btn">LOG IN</button>
        <div class="forgot-wrap">
          <span class="forgot-link" id="forgotLink">Forgot password?</span>
        </div>
      </form>
    </div>
  </div>

  <!-- Forgot Password Modal -->
  <div class="modal" id="forgotModal">
    <div class="modal-content">
      <h4>Reset Password</h4>
      <input type="email" id="forgotEmail" placeholder="Enter your registered email" />
      <div>
        <button class="close-btn" id="closeModal">Cancel</button>
        <button class="send-btn" id="sendReset">Send link</button>
      </div>
      <p id="resetMsg"></p>
    </div>
  </div>

  <!-- Firebase SDK -->
  <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-auth.js"></script>

  <script>
    /* ---------- Firebase init ---------- */
    const firebaseConfig = {
      apiKey: "AIzaSyCrjSyOI-qzCaJptEkWiRfEuaG28ugTmdE",
      authDomain: "capstone-flare-2025.firebaseapp.com",
      projectId: "capstone-flare-2025",
      storageBucket: "capstone-flare-2025.firebasestorage.app",
      messagingSenderId: "685814202928",
      appId: "1:685814202928:web:9b484f04625e5870c9a3f5",
    };
    if (!firebase.apps.length) firebase.initializeApp(firebaseConfig);
    const auth = firebase.auth();

    /* ---------- Password show/hide ---------- */
    const togglePasswordBtn = document.querySelector('.toggle-password');
    const passwordInput = document.querySelector('#password');
    togglePasswordBtn.addEventListener('click', function () {
      const type = passwordInput.type === 'password' ? 'text' : 'password';
      passwordInput.type = type;
      const icon = this.querySelector('i');
      icon.classList.toggle('bi-eye');
      icon.classList.toggle('bi-eye-slash');
    });

    /* ---------- Forgot password modal ---------- */
    const COOLDOWN_SECONDS = 60; // change to taste
    let cooldownTimer = null;
    let cooldownEnd = 0;

    const forgotLink = document.getElementById('forgotLink');
    const modal = document.getElementById('forgotModal');
    const closeModal = document.getElementById('closeModal');
    const sendReset = document.getElementById('sendReset');
    const forgotEmail = document.getElementById('forgotEmail');
    const resetMsg = document.getElementById('resetMsg');

    function openModal() {
      modal.style.display = 'flex';
      resetMsg.textContent = '';
      resetMsg.style.color = '#333';
      // prefill from login form, if any
      const loginEmail = document.getElementById('email').value.trim();
      if (loginEmail) forgotEmail.value = loginEmail;
      // If still cooling down, keep disabled
      if (cooldownEnd > Date.now()) {
        sendReset.disabled = true;
        tickCooldown();
      } else {
        sendReset.disabled = false;
        sendReset.textContent = 'Send link';
      }
    }
    function closeTheModal() { modal.style.display = 'none'; }

    forgotLink.onclick = openModal;
    closeModal.onclick = closeTheModal;
    window.onclick = (e) => { if (e.target === modal) closeTheModal(); };

    /* ---------- Cooldown helpers ---------- */
    function fmtTimeLeft(sec) {
      sec = Math.max(0, sec|0);
      const m = Math.floor(sec / 60);
      const s = sec % 60;
      return m ? `${m}m ${String(s).padStart(2,'0')}s` : `${s}s`;
    }
    function tickCooldown() {
      if (!cooldownEnd) return;
      const remaining = Math.ceil((cooldownEnd - Date.now()) / 1000);
      if (remaining > 0) {
        sendReset.textContent = `Resend in ${fmtTimeLeft(remaining)}`;
        sendReset.disabled = true;
        cooldownTimer = setTimeout(tickCooldown, 1000);
      } else {
        sendReset.textContent = 'Resend link';
        sendReset.disabled = false;
        cooldownTimer && clearTimeout(cooldownTimer);
        cooldownTimer = null;
        cooldownEnd = 0;
      }
    }
    function startCooldown() {
      cooldownEnd = Date.now() + COOLDOWN_SECONDS * 1000;
      tickCooldown();
    }

    /* ---------- Send / Resend link ---------- */
    sendReset.onclick = () => {
      const email = forgotEmail.value.trim();
      resetMsg.style.color = '#333';
      resetMsg.textContent = '';
      if (!email) { resetMsg.textContent = 'Please enter your email.'; return; }
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { resetMsg.textContent = 'Invalid email format.'; return; }

      sendReset.disabled = true;
      sendReset.textContent = 'Sending…';

      auth.sendPasswordResetEmail(email)
        .then(() => {
          resetMsg.style.color = 'green';
          resetMsg.textContent = 'Password reset link sent to ' + email;
          startCooldown(); // start cooldown only on success
        })
        .catch((error) => {
          resetMsg.style.color = 'red';
          resetMsg.textContent = 'Error: ' + (error.message || 'Unable to send email');
          // Re-enable so user can correct typos immediately
          sendReset.disabled = false;
          sendReset.textContent = 'Send link';
        });
    };
  </script>
</body>
</html>
