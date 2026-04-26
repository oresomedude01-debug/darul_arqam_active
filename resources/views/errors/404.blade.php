<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - 404</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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

        .error-code {
            font-size: 5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #f5576c, #fd6b78);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
            line-height: 1;
        }

        .error-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            margin: 1rem 0;
        }

        .error-icon {
            font-size: 6rem;
            margin: 1rem 0;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
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
            color: #f5576c;
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
    <div class="error-container">
        <div class="error-icon">
            🔍
        </div>

        <h1 class="error-code">404</h1>
        <h2 class="error-title">Page Not Found</h2>

        <p class="error-message">
            The page you're looking for doesn't exist or has been moved.
        </p>

        <div class="error-details">
            <h4>What happened?</h4>
            <p>
                We couldn't find the requested page. It may have been deleted, moved, or the link might be incorrect.
            </p>
        </div>

        <div class="error-details">
            <h4>What can you do?</h4>
            <p>
                Try returning to the homepage or use the navigation menu to find what you're looking for.
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
