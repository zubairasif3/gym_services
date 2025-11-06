<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conferma Email - FitScout</title>
</head>
<body style="font-family: 'DM Sans', Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #ffffff; padding: 30px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="{{ asset('web/images/logo.png') }}" alt="FitScout" style="max-width: 150px;">
        </div>
        
        <h1 style="color: #00b3f1; font-size: 28px; margin-bottom: 20px; text-align: center;">Benvenuto su FitScout!</h1>
        
        <p style="margin-bottom: 15px; font-size: 16px;">Ciao {{ $user->name }},</p>
        
        <p style="margin-bottom: 20px; font-size: 16px;">
            🎉 Benvenuto su FitScout— il tuo nuovo punto di riferimento per la ricerca di Centri Sportivi e Liberi Professionisti per il tuo benessere.
        </p>
        
        <p style="margin-bottom: 15px; font-size: 16px;">
            Trova in modo semplice, veloce e sicuro i migliori della tua zona.
        </p>
        
        <p style="margin-bottom: 25px; font-size: 16px;">
            Da oggi hai accesso a una community di esperti pronti ad aiutarti per ogni esigenza: basta un click per cercare, interagire e prenotare.
        </p>
        
        <p style="margin-bottom: 15px; font-size: 16px;">
            ✨ Cosa puoi fare subito:
        </p>
        
        <ol style="margin-bottom: 25px; padding-left: 20px; font-size: 16px;">
            <li style="margin-bottom: 10px;">🔍 Cerca il servizio di cui hai bisogno.</li>
            <li style="margin-bottom: 10px;">❤️ Salva i tuoi professionisti preferiti e tieni d'occhio i loro contenuti.</li>
            <li style="margin-bottom: 10px;">🗓️ Prenota online in pochi secondi.</li>
        </ol>
        
        <p style="margin-bottom: 15px; font-size: 16px;">
            Siamo felici di averti con noi!
        </p>
        
        <p style="margin-bottom: 25px; font-size: 16px;">
            Il nostro obiettivo è rendere la tua esperienza semplice e soddisfacente, ogni volta.
        </p>
        
        <p style="margin-bottom: 20px; font-size: 16px;">
            👉 Effettua subito il Login e completa ora il tuo profilo potrai accedere a un'esperienza su misura.
        </p>
        
        <p style="margin-bottom: 20px; font-size: 16px;">
            Per completare la tua registrazione e attivare il tuo account, clicca sul pulsante qui sotto per verificare il tuo indirizzo email:
        </p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $verificationUrl }}" style="display: inline-block; background-color: #00b3f1; color: white; padding: 15px 30px; text-decoration: none; border-radius: 4px; font-weight: 600; font-size: 16px;">
                Verifica Email
            </a>
        </div>
        
        <p style="margin-bottom: 15px; font-size: 14px; color: #666;">
            Oppure copia e incolla questo link nel tuo browser:
        </p>
        
        <p style="margin-bottom: 25px; font-size: 12px; color: #00b3f1; word-break: break-all;">
            {{ $verificationUrl }}
        </p>
        
        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
        
        <p style="margin-bottom: 15px; font-size: 14px; color: #666;">
            Se non hai creato un account su FitScout, puoi ignorare questa email.
        </p>
        
        <p style="margin-top: 30px; font-size: 14px; color: #333;">
            Cordiali saluti,<br>
            Il Team di FitScout<br>
            <a href="https://www.fitscout.it" style="color: #00b3f1; text-decoration: none;">www.fitscout.it</a>
        </p>
    </div>
</body>
</html>
