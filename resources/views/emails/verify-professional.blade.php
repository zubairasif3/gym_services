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
            <img src="{{ asset('web/images/logo-dark.png') }}" alt="FitScout" style="max-width: 150px;">
        </div>
        
        <h1 style="color: #333; font-size: 24px; margin-bottom: 20px;">
            👋 Benvenuto su FitScout, la tua vetrina digitale!
        </h1>
        
        <p style="margin-bottom: 15px; font-size: 16px;">
            La piattaforma dove i professionisti come te possono farsi conoscere, fidelizzare clienti e aumentare le vendite.
        </p>
        
        <p style="margin-bottom: 25px; font-size: 16px;">
            La tua iscrizione è andata a buon fine 🎉
        </p>
        
        <p style="margin-bottom: 20px; font-size: 16px;">
            Da oggi la tua attività ha una vetrina online visibile a migliaia di utenti in cerca dei tuoi servizi e puoi iniziare subito a farti notare!
        </p>
        
        <div style="background-color: #fff5f5; border-left: 4px solid #ff4444; padding: 20px; margin: 25px 0;">
            <p style="margin-bottom: 10px; font-size: 18px; color: #ff4444; font-weight: 600;">
                Prova subito FitScout GRATIS.
            </p>
            <p style="margin-bottom: 10px; font-size: 16px; color: #ff4444;">
                Per te un periodo gratuito che ti farà vivere appieno l'esperienza.
            </p>
            <p style="margin-bottom: 15px; font-size: 16px; color: #ff4444;">
                Nessuna carta richiesta. Poi scegli se continuare:
            </p>
            <ul style="margin: 0; padding-left: 20px; color: #333; font-size: 16px;">
                <li style="margin-bottom: 8px;">- 29€/mese</li>
                <li>- 290€/anno (Possibilità di pagare in 3 rate senza interesse)</li>
            </ul>
        </div>
        
        <p style="margin-bottom: 25px; font-size: 16px; color: #666;">
            Riceverai un promemoria prima della scadenza, così potrai scegliere in autonomia se proseguire o disdire senza alcun impegno.
        </p>
        
        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
        
        <p style="margin-bottom: 15px; font-size: 18px; font-weight: 600;">
            🚀 I tuoi prossimi passi:
        </p>
        
        <ol style="margin-bottom: 25px; padding-left: 20px; font-size: 16px;">
            <li style="margin-bottom: 15px;">
                🖼️ Completa il tuo profilo inserendo contenuti per rendere la tua pagina ancora più visibile ed esclusiva.
            </li>
            <li style="margin-bottom: 15px;">
                🗓️ Gestisci appuntamenti facilmente con il nostro gestionale integrato.
            </li>
            <li style="margin-bottom: 15px;">
                📣 Un consiglio, stampa e fissa il QR code nella tua attività, condividilo per farti recensire e portare nuovi clienti sul tuo profilo.
            </li>
        </ol>
        
        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
        
        <p style="margin-bottom: 15px; font-size: 26px; font-weight: 600;">
            📎 SCAN ME
        </p>
        
        <p style="margin-bottom: 25px; font-size: 16px; color: #ff4444; padding-left: 20px;">
            <img src="{{ asset('web/images/qr-code.png') }}" alt="QR Code" style="max-width: 150px;">
        </p>
        <p style="margin-bottom: 15px; font-size: 16px;">
            Racconta la tua esperienza e lascia una recensione.
        </p>
        
        <div style="text-align: center; margin: 20px 0;">
            <a href="{{ asset('web/pdf/QR CODE FITSCOUT.pdf') }}" download style="display: inline-block; background-color: #00b3f1; color: white; padding: 12px 25px; text-decoration: none; border-radius: 4px; font-weight: 600; font-size: 14px;">
                📥 Scarica il QR Code PDF / Download QR Code PDF
            </a>
        </div>
        
        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
        
        <p style="margin-bottom: 15px; font-size: 16px;">
            👍 Accedi ora al tuo pannello e inizia a farti conoscere:
        </p>
        
        <p style="margin-bottom: 25px; font-size: 16px;">
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
        
        <p style="margin-bottom: 20px; font-size: 16px;">
            Siamo felici di averti nella community di FitScout— insieme stiamo costruendo un ecosistema digitale dove qualità, visibilità e innovazione si incontrano.
        </p>
        
        <p style="margin-top: 30px; font-size: 14px; color: #333;">
            A presto,<br>
            Il Team di FitScout<br>
            🌐 <a href="https://www.fitscout.it" style="color: #00b3f1; text-decoration: none;">www.fitscout.it</a>
        </p>
    </div>
</body>
</html>
