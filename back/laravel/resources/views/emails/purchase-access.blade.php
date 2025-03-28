<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accés a reserves</title>
</head>

<body style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #f8fafc;">
        <!-- Header -->
        <div style="background: #4f46e5; padding: 2rem; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 28px;">🎬 Cine</h1>
            <h2 style="color: white; margin: 1rem 0 0; font-size: 1.5rem;">Accés al teu historial de reserves</h2>
        </div>

        <!-- Contingut -->
        <div style="padding: 2rem;">
            <p style="color: #1e293b; line-height: 1.6;">Hola,</p>
            <p style="color: #1e293b; line-height: 1.6;">Has sol·licitat accés a les teves reserves a Cine. Fes
                clic al botó per veure totes les teves entrades:</p>

            <div style="text-align: center; margin: 2rem 0;">
                <a href="{{ $link }}" style="display: inline-block; padding: 1rem 2rem; 
                          background-color: #4f46e5; color: white; 
                          text-decoration: none; border-radius: 0.5rem;
                          font-weight: 500; font-size: 1.1rem;">
                    Veure les meves reserves
                </a>
            </div>

            <p style="color: #64748b; font-size: 0.9rem; line-height: 1.6;">
                ⏳ Aquest enllaç expirarà en 24 hores.<br>
                ⚠️ Si no has sol·licitat aquest accés, pots ignorar aquest missatge.
            </p>
        </div>

        <!-- Footer -->
        <div style="background: #e2e8f0; padding: 1.5rem; text-align: center;">
            <p style="margin: 0; color: #64748b; font-size: 0.8rem;">
                © {{ date('Y') }} Cine · Carrer del Cinema 123, Barcelona<br>
                <a href="https://tr3cine.daw.inspedralbes.cat" style="color: #4f46e5; text-decoration: none;">www.tr3cine.daw.inspedralbes.cat</a>
            </p>
        </div>
    </div>
</body>

</html>