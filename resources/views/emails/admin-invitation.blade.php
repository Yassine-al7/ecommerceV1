<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invitation Administrateur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #1f2937;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .credentials {
            background-color: #f3f4f6;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #3b82f6;
        }
        .footer {
            background-color: #f9fafb;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Invitation Administrateur</h1>
    </div>

    <p>Bonjour,</p>

    <p>Vous avez été invité à rejoindre l'équipe d'administration de <strong>{{ config('app.name') }}</strong>.</p>

    <div class="credentials">
        <h3>Vos identifiants de connexion :</h3>
        <p><strong>Email :</strong> {{ $email }}</p>
        <p><strong>Mot de passe :</strong> {{ $password }}</p>
    </div>

    <div class="warning">
        <p><strong>⚠️ Important :</strong></p>
        <ul>
            <li>Conservez ces identifiants en lieu sûr</li>
            <li>Changez votre mot de passe après votre première connexion</li>
            <li>Ne partagez jamais vos identifiants</li>
        </ul>
    </div>

    <p>Vous pouvez maintenant vous connecter à l'application avec ces identifiants et accéder au panel d'administration.</p>

    <p>Cordialement,<br>
    L'équipe {{ config('app.name') }}</p>

    <div class="footer">
        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
    </div>
</body>
</html>
