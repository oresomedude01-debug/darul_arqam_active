<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - 403</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-container {
            text-align: center;
            padding: 2rem;
            max-width: 600px;
            width: 100%;
        }

        .error-icon {
            font-size: 6rem;
            margin-bottom: 1.5rem;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .error-code {
            font-size: 5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
            line-height: 1;
            text-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .error-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            margin: 1rem 0;
            letter-spacing: -1px;
        }

        .error-message {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
            margin: 1.5rem 0 2rem;
            line-height: 1.6;
        }

        .error-details {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 2rem 0;
            text-align: left;
            color: rgba(255, 255, 255, 0.8);
        }

        .error-details h4 {
            margin: 0 0 0.5rem;
            color: white;
            font-size: 0.95rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .error-details p {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .error-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2.5rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn-home, .btn-back {
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-home {
            background: white;
            color: #667eea;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.5);
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: white;
        }

        .security-icon {
            display: inline-block;
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.1);
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 3.5rem;
        }

        .background-pattern {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.1;
            pointer-events: none;
            background-image:
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.3) 0.5px, transparent 0.5px),
                radial-gradient(circle at 60% 70%, rgba(255, 255, 255, 0.3) 0.5px, transparent 0.5px);
            background-size: 50px 50px;
        }

        @media (max-width: 768px) {
            .error-code {
                font-size: 3.5rem;
            }

            .error-title {
                font-size: 1.8rem;
            }

            .error-message {
                font-size: 1rem;
            }

            .error-actions {
                flex-direction: column;
            }

            .btn-home, .btn-back {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="background-pattern"></div>

    <div class="error-container">
        <div class="security-icon">
            🔒
        </div>

        <h1 class="error-code">403</h1>
        <h2 class="error-title">Access Denied</h2>

        <p class="error-message">
            You don't have permission to access this resource. Your current role or permissions do not allow this action.
        </p>

        <div class="error-details">
            <h4>What happened?</h4>
            <p>
                @if ($exception->getMessage())
                    {{ $exception->getMessage() }}
                @else
                    You attempted to perform an action that requires higher privileges or specific permissions.
                @endif
            </p>
        </div>

        <div class="error-details">
            <h4>Need Help?</h4>
            <p>
                If you believe this is a mistake, please contact your administrator or check your account permissions.
            </p>
        </div>

        <div class="error-actions">
            <a href="{{ route('dashboard') }}" class="btn-home">
                <span>← Go to Dashboard</span>
            </a>
            <a href="javascript:history.back()" class="btn-back">
                Go Back
            </a>
        </div>
    </div>
</body>
</html>
