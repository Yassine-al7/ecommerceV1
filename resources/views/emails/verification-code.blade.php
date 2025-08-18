<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de Vérification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 10px;
        }
        .code-container {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background-color: #f8fafc;
            border-radius: 8px;
            border: 2px dashed #3b82f6;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #3b82f6;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
        }
        .instructions {
            background-color: #eff6ff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #3b82f6;
        }
        .warning {
            background-color: #fef3c7;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #f59e0b;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6b7280;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ config('app.name', 'E-commerce App') }}</div>
            <h1>Code de Vérification</h1>
        </div>

        <p>Bonjour,</p>

        <p>Vous avez demandé à créer un compte vendeur sur <strong>{{ config('app.name', 'E-commerce App') }}</strong>.</p>

        <div class="code-container">
            <p style="margin-bottom: 15px; color: #6b7280;">Votre code de vérification :</p>
            <div class="code">{{ $verificationCode }}</div>
        </div>

        <div class="instructions">
            <h3>Instructions :</h3>
            <ol>
                <li>Copiez le code ci-dessus</li>
                <li>Retournez sur la page de vérification</li>
                <li>Collez le code dans le champ approprié</li>
                <li>Cliquez sur "Vérifier le Code"</li>
            </ol>
        </div>

        <div class="warning">
            <p><strong>⚠️ Important :</strong></p>
            <ul>
                <li>Ce code est valide pendant <strong>15 minutes</strong></li>
                <li>Ne partagez jamais ce code avec qui que ce soit</li>
                <li>Si vous n'avez pas demandé ce code, ignorez cet email</li>
            </ul>
        </div>

        <p>Une fois le code vérifié, votre compte sera créé et vous pourrez vous connecter immédiatement.</p>

        <div class="footer">
            <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'E-commerce App') }}. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
