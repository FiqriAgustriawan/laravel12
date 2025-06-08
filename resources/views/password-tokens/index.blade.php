<!-- filepath: d:\docs_pelajaran\KK1web\larv_12\lat_1\resources\views\password-tokens\index.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Tokens - Development</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }

        .tokens-grid {
            display: grid;
            gap: 20px;
            margin-top: 20px;
        }

        .token-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            background: #fff;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .token-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .token-card.expired {
            border-left: 4px solid #dc3545;
            background-color: #fff5f5;
        }

        .token-card.active {
            border-left: 4px solid #28a745;
            background-color: #f8fff9;
        }

        .email {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }

        .token-section {
            margin: 15px 0;
        }

        .token-label {
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
        }

        .token-hash {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            word-break: break-all;
            margin-bottom: 10px;
            border: 1px solid #e9ecef;
        }

        .token-plain {
            background-color: #e7f3ff;
            padding: 12px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            word-break: break-all;
            border: 2px solid #007bff;
            position: relative;
        }

        .copy-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 11px;
            margin-top: 5px;
        }

        .copy-btn:hover {
            background-color: #0056b3;
        }

        .token-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }

        .info-item {
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 4px;
            border-left: 3px solid #007bff;
        }

        .info-label {
            font-weight: bold;
            color: #495057;
            font-size: 12px;
            text-transform: uppercase;
        }

        .info-value {
            color: #333;
            margin-top: 2px;
        }

        .expired-badge {
            background-color: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .active-badge {
            background-color: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .no-tokens {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .no-tokens h3 {
            color: #495057;
            margin-bottom: 10px;
        }

        .refresh-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }

        .refresh-btn:hover {
            background-color: #0056b3;
        }

        .api-example {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
            border-left: 3px solid #28a745;
        }

        .api-example pre {
            margin: 0;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            white-space: pre-wrap;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🔑 Password Reset Tokens (Development Mode)</h1>

        <div class="alert">
            <strong>⚠️ Development Only:</strong> Halaman ini hanya tersedia di environment development untuk debugging purposes.
        </div>

        <a href="{{ url('/password-tokens') }}" class="refresh-btn">🔄 Refresh</a>

        @if($tokens->count() > 0)
        <div class="tokens-grid">
            @foreach($tokens as $token)
            <div class="token-card {{ $token['is_expired'] ? 'expired' : 'active' }}">
                <div class="email">
                    📧 {{ $token['email'] }}
                    <span class="{{ $token['is_expired'] ? 'expired-badge' : 'active-badge' }}">
                        {{ $token['is_expired'] ? 'EXPIRED' : 'ACTIVE' }}
                    </span>
                </div>

                @if($token['plain_token'])
                <div class="token-section">
                    <div class="token-label">🔐 Plain Token (untuk Testing API):</div>
                    <div class="token-plain" id="plainToken{{ $loop->index }}">{{ $token['plain_token'] }}</div>
                    <button class="copy-btn" onclick="copyToken('plainToken{{ $loop->index }}')">📋 Copy Plain Token</button>

                    <div class="api-example">
                        <strong>API Testing Example:</strong>
                        <pre>POST {{ url('/api/password/reset') }}
Content-Type: application/json

{
    "email": "{{ $token['email'] }}",
    "token": "{{ $token['plain_token'] }}",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}</pre>
                    </div>
                </div>
                @else
                <div class="token-section">
                    <div class="token-label">⚠️ Plain Token tidak tersedia</div>
                    <small style="color: #6c757d;">Token ini dibuat sebelum sistem caching diaktifkan</small>
                </div>
                @endif

                <div class="token-section">
                    <div class="token-label">🔒 Token Hash (Database):</div>
                    <div class="token-hash">{{ $token['token_hash'] }}</div>
                </div>

                <div class="token-info">
                    <div class="info-item">
                        <div class="info-label">Created At</div>
                        <div class="info-value">{{ $token['created_at']->format('Y-m-d H:i:s') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Expires At</div>
                        <div class="info-value">{{ $token['expires_at']->format('Y-m-d H:i:s') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Status</div>
                        <div class="info-value">{{ $token['time_remaining'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="no-tokens">
            <h3>🚫 No Password Reset Tokens Found</h3>
            <p>Belum ada permintaan reset password yang dibuat.</p>
            <p>Untuk membuat token baru, gunakan endpoint: <code>POST /api/password/forgot</code></p>
        </div>
        @endif
    </div>

    <script>
        function copyToken(elementId) {
            const tokenText = document.getElementById(elementId).textContent;
            navigator.clipboard.writeText(tokenText).then(function() {
                alert('Plain token copied to clipboard!');
            }, function(err) {
                // Fallback untuk browser yang tidak mendukung clipboard API
                const textArea = document.createElement('textarea');
                textArea.value = tokenText;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('Plain token copied to clipboard!');
            });
        }

        // Auto refresh setiap 30 detik
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>

</html>