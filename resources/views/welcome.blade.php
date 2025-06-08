<!-- filepath: d:\docs_pelajaran\KK1web\larv_12\lat_1\resources\views\welcome.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Documentation</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: #fafafa;
            color: #3c4043;
            line-height: 1.6;
        }

        .topbar {
            background: #ffffff;
            border-bottom: 1px solid #e8eaed;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .topbar-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #4285f4, #34a853);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }

        .logo-text {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a73e8;
        }

        .version-badge {
            background: #e8f0fe;
            color: #1a73e8;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            margin-bottom: 2rem;
        }

        .title {
            font-size: 2rem;
            font-weight: 700;
            color: #1a73e8;
            margin-bottom: 0.5rem;
        }

        .description {
            font-size: 1rem;
            color: #5f6368;
            margin-bottom: 1.5rem;
        }

        .server-info {
            background: #ffffff;
            border: 1px solid #e8eaed;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .server-title {
            font-size: 1rem;
            font-weight: 600;
            color: #3c4043;
            margin-bottom: 1rem;
        }

        .server-url {
            background: #f8f9fa;
            border: 1px solid #e8eaed;
            border-radius: 4px;
            padding: 0.75rem 1rem;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .status-indicator {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #34a853;
            animation: pulse 2s infinite;
        }

        .status-dot.offline {
            background: #ea4335;
            animation: none;
        }

        .status-text {
            font-size: 0.75rem;
            font-weight: 500;
            color: #34a853;
        }

        .status-text.offline {
            color: #ea4335;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .endpoints-section {
            background: #ffffff;
            border: 1px solid #e8eaed;
            border-radius: 8px;
            overflow: hidden;
        }

        .section-header {
            background: #f8f9fa;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e8eaed;
            font-size: 1.125rem;
            font-weight: 600;
            color: #3c4043;
        }

        .endpoint-group {
            border-bottom: 1px solid #f1f3f4;
        }

        .endpoint-group:last-child {
            border-bottom: none;
        }

        .group-title {
            background: #f8f9fa;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #5f6368;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .endpoint {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f1f3f4;
            transition: background-color 0.2s ease;
        }

        .endpoint:hover {
            background: #f8f9fa;
        }

        .endpoint:last-child {
            border-bottom: none;
        }

        .endpoint-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }

        .method-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            min-width: 60px;
            text-align: center;
        }

        .method-get {
            background: #e8f5e8;
            color: #2e7d2e;
            border: 1px solid #81c784;
        }

        .method-post {
            background: #fff3e0;
            color: #f57c00;
            border: 1px solid #ffb74d;
        }

        .method-put {
            background: #e3f2fd;
            color: #1976d2;
            border: 1px solid #64b5f6;
        }

        .method-delete {
            background: #ffebee;
            color: #d32f2f;
            border: 1px solid #ef5350;
        }

        .endpoint-path {
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            color: #3c4043;
            font-weight: 500;
        }

        .endpoint-auth {
            background: #fff8e1;
            color: #ff8f00;
            padding: 0.125rem 0.5rem;
            border-radius: 3px;
            font-size: 0.7rem;
            font-weight: 500;
            margin-left: auto;
        }

        .endpoint-description {
            font-size: 0.875rem;
            color: #5f6368;
            margin-left: 76px;
        }

        .try-it-section {
            background: #ffffff;
            border: 1px solid #e8eaed;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .try-it-title {
            font-size: 1rem;
            font-weight: 600;
            color: #3c4043;
            margin-bottom: 1rem;
        }

        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .quick-link {
            display: block;
            padding: 0.75rem 1rem;
            background: #f8f9fa;
            border: 1px solid #e8eaed;
            border-radius: 4px;
            text-decoration: none;
            color: #1a73e8;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .quick-link:hover {
            background: #e8f0fe;
            border-color: #1a73e8;
        }

        .footer {
            text-align: center;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #e8eaed;
            color: #5f6368;
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .topbar-content {
                padding: 0 1rem;
            }

            .endpoint-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .endpoint-description {
                margin-left: 0;
            }

            .quick-links {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>


    <div class="container">
        <div class="header">

        </div>

        <div class="server-info">
            <div class="server-title">Server</div>
            <div class="server-url">
                <span>{{ url('/api') }}</span>
                <div class="status-indicator">
                    <span class="status-dot" id="statusDot"></span>
                    <span class="status-text" id="statusText">Checking...</span>
                </div>
            </div>
        </div>

        <div class="endpoints-section">
            <div class="section-header">API Endpoints</div>

            <!-- Authentication Endpoints -->
            <div class="endpoint-group">
                <div class="group-title">Authentication</div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method-badge method-post">POST</span>
                        <span class="endpoint-path">/api/register</span>
                    </div>
                    <div class="endpoint-description">Register a new user account</div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method-badge method-post">POST</span>
                        <span class="endpoint-path">/api/login</span>
                    </div>
                    <div class="endpoint-description">Authenticate user and get access token</div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method-badge method-post">POST</span>
                        <span class="endpoint-path">/api/password/forgot</span>
                    </div>
                    <div class="endpoint-description">Send password reset token to email</div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method-badge method-post">POST</span>
                        <span class="endpoint-path">/api/password/reset</span>
                    </div>
                    <div class="endpoint-description">Reset password using token</div>
                </div>
            </div>

            <!-- Films Endpoints -->
            <div class="endpoint-group">
                <div class="group-title">Films</div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method-badge method-get">GET</span>
                        <span class="endpoint-path">/api/films</span>
                    </div>
                    <div class="endpoint-description">Get list of active films</div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method-badge method-get">GET</span>
                        <span class="endpoint-path">/api/films/{slug}</span>
                    </div>
                    <div class="endpoint-description">Get film details by slug</div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method-badge method-post">POST</span>
                        <span class="endpoint-path">/api/films</span>
                        <span class="endpoint-auth">Admin Only</span>
                    </div>
                    <div class="endpoint-description">Create a new film</div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method-badge method-put">PUT</span>
                        <span class="endpoint-path">/api/films/{slug}</span>
                        <span class="endpoint-auth">Admin Only</span>
                    </div>
                    <div class="endpoint-description">Update film by slug</div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method-badge method-delete">DELETE</span>
                        <span class="endpoint-path">/api/films/{slug}</span>
                        <span class="endpoint-auth">Admin Only</span>
                    </div>
                    <div class="endpoint-description">Delete film by slug</div>
                </div>
            </div>

            <!-- Bookings Endpoints -->
            <div class="endpoint-group">
                <div class="group-title">Bookings</div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method-badge method-get">GET</span>
                        <span class="endpoint-path">/api/pemesanan</span>
                        <span class="endpoint-auth">Auth Required</span>
                    </div>
                    <div class="endpoint-description">Get user's bookings</div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method-badge method-post">POST</span>
                        <span class="endpoint-path">/api/pemesanan</span>
                        <span class="endpoint-auth">Auth Required</span>
                    </div>
                    <div class="endpoint-description">Create a new booking</div>
                </div>
            </div>
        </div>

        <!-- <div class="try-it-section">
            <div class="try-it-title">Quick Links</div>
            <div class="quick-links">
                @if(config('app.env') === 'local')
                <a href="/password-tokens" class="quick-link">🔧 Debug Password Tokens</a>
                @endif
                <a href="{{ url('/api/films') }}" class="quick-link" target="_blank">📋 View Films JSON</a>
                <a href="#" class="quick-link" onclick="testApiStatus()">🔍 Test API Status</a>
                <a href="https://www.postman.com/" class="quick-link" target="_blank">📮 Download Postman</a>
            </div>
        </div> -->

        <div class="footer">
            <p>
                Laravel {{ app()->version() }} | PHP {{ PHP_VERSION }} |
                Environment: <strong>{{ config('app.env') }}</strong>
            </p>
            <p style="margin-top: 0.5rem;">
                Built with Fiqri agustriawan for LKS Web Programming 2025
            </p>
        </div>
    </div>

    <script>
        // Check API status
        function checkApiStatus() {
            const statusDot = document.getElementById('statusDot');
            const statusText = document.getElementById('statusText');

            fetch('/api/films')
                .then(response => {
                    if (response.ok) {
                        statusDot.classList.remove('offline');
                        statusText.textContent = 'Online';
                        statusText.className = 'status-text';
                    } else {
                        throw new Error('API Error');
                    }
                })
                .catch(error => {
                    statusDot.classList.add('offline');
                    statusText.textContent = 'Offline';
                    statusText.className = 'status-text offline';
                });
        }

        function testApiStatus() {
            checkApiStatus();
            alert('API status checked! Check the server indicator above.');
        }

        // Check status on page load
        checkApiStatus();

        // Recheck every 30 seconds
        setInterval(checkApiStatus, 30000);

        // Console info
        console.log('🎬 Cinema Ticket API Documentation');
        console.log('📍 Base URL:', '{{ url("/api") }}');
        console.log('🔧 Environment:', '{{ config("app.env") }}');
        console.log('📚 Available endpoints loaded');
    </script>
</body>

</html>