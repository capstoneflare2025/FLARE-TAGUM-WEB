<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login Page</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
  *{margin:0;padding:0;box-sizing:border-box;font-family:Arial, sans-serif}
  body{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#f4f4f4;
    overflow:hidden;
  }

  .video-bg{
    position:fixed;inset:0;width:100%;height:100%;
    object-fit:cover;z-index:-2;filter:blur(4px);transform:scale(1.1);
  }
  .overlay{
    position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:-1;
  }

  /* Layout */
  .container{
    width:100%;
    max-width:1100px;
    padding:16px;
    display:grid;
    grid-template-columns:1fr;
    gap:16px;
  }

  .welcome-box,
  .login-box{
    background:#fff;
    border-radius:16px;
    box-shadow:0 5px 15px rgba(0,0,0,.18);
  }

  /* Welcome panel */
  .welcome-box{
    background:#E00024;
    color:#fff;
    padding:24px;
    text-align:center;
  }
  .welcome-box .logo{
    width:100%;
    max-width:360px;       /* scales on mobile */
    height:auto;
    margin:0 auto 12px;
    display:block;
  }
  .welcome-box h2{font-size:clamp(18px,3.5vw,28px);}

  /* Login panel */
  .login-box{
    padding:28px 22px;
  }
  .login-box h3{
    text-align:center;margin-bottom:20px;color:#E00024;
    font-size:clamp(18px,4vw,24px);
  }
  .input-group{
    display:flex;align-items:center;
    background:#F1F1F1;border-radius:8px;
    padding:10px 12px;margin-top:14px;
  }
  .input-group i{margin-right:10px;color:gray}
  .input-group input{
    border:none;outline:none;background:transparent;flex:1;font-size:16px;
  }
  button.toggle-password{background:transparent;border:0;cursor:pointer}

  .login-btn,.register-btn{
    width:100%;max-width:320px;
    display:block;margin:18px auto 0;
    background:#E00024;color:#fff;border:0;border-radius:10px;padding:12px;
    font-weight:bold;cursor:pointer;
  }
  .login-btn:hover{background:#E87F2E}
  .register-btn{background:#0A1F7C}.register-btn:hover{background:#00A9FF}

  /* ≥768px: two columns, equal height */
  @media (min-width:768px){
    .container{
      grid-template-columns:1fr 1fr;
      align-items:stretch;
    }
    .welcome-box,.login-box{height:auto}
  }

  /* ≥1024px: tighten max width */
  @media (min-width:1024px){
    .container{max-width:1000px}
  }
</style>

</head>
<body>
    <!-- 🎥 Video Background -->
    <video autoplay muted loop playsinline class="video-bg">
        <source src="{{ asset('videos/background.mp4') }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <!-- 🔴 Transparent Overlay -->
    <div class="overlay"></div>

    <div class="container">
        <div class="welcome-box">
            <!-- ✅ Logo above welcome -->
            <img src="{{ asset('images/flabfp.png') }}" alt="Logo" class="logo">
            <h2>ADMIN DASHBOARD</h2>
        </div>
        <div class="login-box">
            <h3 class="login">Login please</h3>

            <!-- Display login errors -->
            @if($errors->any())
                <div style="color: red; text-align: center;">
                    {{ $errors->first('login') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="input-group">
                    <i class="fa fa-envelope"></i>
                    <input type="email" name="email" placeholder="Input your Email" required value="{{ old('email') }}">
                </div>
                <div class="input-group">
                    <i class="fa fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Input your password" required>
                    <button type="button" class="toggle-password">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
                <button type="submit" class="login-btn">LOG IN</button>
            </form>
        </div>
    </div>

    <script>
        const togglePasswordBtn = document.querySelector('.toggle-password');
        const passwordInput = document.querySelector('#password');

        togglePasswordBtn.addEventListener('click', function () {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            const icon = this.querySelector('i');
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>
