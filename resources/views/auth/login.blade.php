<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WFA Scheduler</title>
    <link rel="icon" href="/logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #090f0b;
            --card-bg: #111a14;
            --emerald: #10b981;
            --emerald-hover: #059669;
            --text-main: #ecf3f0;
            --text-dim: #94a3b8;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: 'Outfit', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .login-card {
            background: var(--card-bg);
            padding: 2.5rem;
            border-radius: 16px;
            width: 100%;
            max-width: 400px;
            border: 1px solid rgba(255,255,255,0.05);
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            color: var(--emerald);
            margin: 0 0 0.5rem 0;
            font-size: 2rem;
        }

        .login-header p {
            color: var(--text-dim);
            margin: 0;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dim);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: white;
            font-family: inherit;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--emerald);
        }

        .btn-login {
            width: 100%;
            padding: 0.85rem;
            background: var(--emerald);
            color: #000;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn-login:hover {
            background: var(--emerald-hover);
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <img src="/logo.png" alt="WFA Logo" style="height: 80px; border-radius: 50%; padding: 5px; background: white; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2); margin-bottom: 1rem;">
            <h1>Login</h1>
            <p>Akses WFA Scheduler</p>
        </div>

        @if($errors->any())
            <div class="error-message">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('error'))
            <div class="error-message">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn-login">Masuk</button>
        </form>
    </div>

</body>
</html>
